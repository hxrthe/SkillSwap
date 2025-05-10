<?php
require_once 'SkillSwapDatabase.php';

class Crud {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createUser($firstname, $lastname, $email, $password, $verificationCode, $isVerified = false) {
        try {
            // Call the stored procedure
            $stmt = $this->conn->prepare("CALL CreateUser(?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $firstname,      // p_firstname
                $lastname,       // p_lastname
                $email,         // p_email
                $password,      // p_password
                $verificationCode, // p_verification_code
                $isVerified ? 1 : 0  // p_is_verified
            ]);

        } catch (PDOException $e) {
            // Check for the custom error from the stored procedure
            if ($e->getCode() == '45000') {
                throw new Exception('email_exists');
            } else {
                throw $e;
            }
        }    
    }

    public function createPost($user_id, $community_id, $content) {
        try {
            $stmt = $this->conn->prepare("CALL createPost(?, ?, ?)");
            $stmt->execute([$user_id, $community_id, $content]);
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function createComment($post_id, $user_id, $community_id, $comment_text) {
        try {
            $stmt = $this->conn->prepare("CALL insertComment(?, ?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $community_id, $comment_text]);
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function createReply($post_id, $user_id, $community_id, $comment_text, $parent_comment_id) {
        try {
            $stmt = $this->conn->prepare("CALL insertReply(?, ?, ?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $community_id, $comment_text, $parent_comment_id]);
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function likePost($post_id, $user_id, $community_id) {
        try {
            $stmt = $this->conn->prepare("CALL insertLike(?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $community_id]);
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getPosts($community_id) {
        try {
            $stmt = $this->conn->prepare("CALL GetCommunityPosts(:community_id)");
            $stmt->bindParam(":community_id", $community_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getComments($post_id) {
        try {
            $stmt = $this->conn->prepare("CALL GetPostComments(:post_id)");
            $stmt->bindParam(":post_id", $post_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getReplies($comment_id) {
        try {
            $stmt = $this->conn->prepare("CALL GetCommentReplies(:comment_id)");
            $stmt->bindParam(":comment_id", $comment_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getPostLikes($postId) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as like_count FROM post_likes WHERE Post_ID = ?");
            $stmt->execute([$postId]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getPostComments($postId) {
        try {
            $stmt = $this->conn->prepare("CALL FetchPostComments(:post_id)");
            $stmt->bindParam(":post_id", $postId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getCommentReplies($commentId) {
        try {
            $stmt = $this->conn->prepare("CALL FetchCommentReplies(:comment_id)");
            $stmt->bindParam(":comment_id", $commentId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getPost($postId) {
        try {
            $stmt = $this->conn->prepare("CALL FetchPost(:post_id)");
            $stmt->bindParam(":post_id", $postId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function hasUserLikedPost($userId, $postId) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM post_likes WHERE User_ID = ? AND Post_ID = ?");
            $stmt->execute([$userId, $postId]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function unlikePost($post_id, $user_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM post_likes WHERE Post_ID = ? AND User_ID = ?");
            $stmt->execute([$post_id, $user_id]);
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getUserProfilePicture($user_id) {
        try {
            $stmt = $this->conn->prepare("SELECT profile_picture FROM users WHERE User_ID = ?");
            $stmt->execute([$user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['profile_picture'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function createAnnouncement($admin_id, $title, $content) {
        $stmt = $this->conn->prepare("CALL createAnnouncement(:admin_id, :title, :content)");
        return $stmt->execute([
            ':admin_id' => $admin_id,      
            ':title' => $title,
            ':content' => $content
        ]);
    }

    public function restrictUser($user_id, $status, $reason, $restricted_until, $admin_id) {
        try {
            $stmt = $this->conn->prepare("CALL RestrictUser(?, ?, ?, ?, ?)");
            return $stmt->execute([$user_id, $status, $reason, $restricted_until, $admin_id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function removeRestriction($user_id) {
        try {
            $stmt = $this->conn->prepare("CALL RemoveRestriction(?)");
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getAllUsersWithRestrictions() {
        try {
            $stmt = $this->conn->prepare("CALL GetAllUsersWithRestrictions()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getRestrictedUsers() {
        try {
            $stmt = $this->conn->prepare("CALL GetRestrictedUsers()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function banUser($user_id, $reason, $restricted_until, $admin_id) {
        try {
            $stmt = $this->conn->prepare("CALL BanUser(?, ?, ?, ?)");
            return $stmt->execute([$user_id, $reason, $restricted_until, $admin_id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getBannedUsers() {
        try {
            $stmt = $this->conn->prepare("CALL GetBannedUsers()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getUserStatistics($start_date, $end_date) {
        try {
            $stmt = $this->conn->prepare("CALL GetUserStatistics(?, ?)");
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getRestrictionStatistics($start_date, $end_date) {
        try {
            $stmt = $this->conn->prepare("CALL GetRestrictionStatistics(?, ?)");
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getDailyUserRegistrations($start_date, $end_date) {
        try {
            $stmt = $this->conn->prepare("CALL GetDailyUserRegistrations(?, ?)");
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getDailyRestrictions($start_date, $end_date) {
        try {
            $stmt = $this->conn->prepare("CALL GetDailyRestrictions(?, ?)");
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getDailyPosts($start_date, $end_date) {
        try {
            $stmt = $this->conn->prepare("CALL GetDailyPosts(?, ?)");
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getDailyComments($start_date, $end_date) {
        try {
            $stmt = $this->conn->prepare("CALL GetDailyComments(?, ?)");
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getSystemStatistics() {
        try {
            $stmt = $this->conn->prepare("CALL GetSystemStatistics()");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getUserActivityStatistics($start_date, $end_date) {
        try {
            $stmt = $this->conn->prepare("CALL GetUserActivityStatistics(?, ?)");
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function createAdmin($firstName, $lastName, $email, $password, $role, $is_active = true) {
        try {
            $stmt = $this->conn->prepare("CALL CreateAdmin(?, ?, ?, ?, ?)");
            return $stmt->execute([$firstName, $lastName, $email, $password, $role]);
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                throw new Exception('email_exists');
            }
            throw $e;
        }
    }

}