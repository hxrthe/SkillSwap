-- Create requests table
CREATE TABLE IF NOT EXISTS requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    message TEXT,
    appointment_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(User_ID),
    FOREIGN KEY (receiver_id) REFERENCES users(User_ID),
    UNIQUE KEY unique_request (sender_id, receiver_id)
);
