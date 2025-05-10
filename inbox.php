
<?php include 'menuu.php'; ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'SkillSwapDatabase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginpagee.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Fetch requests where the logged-in user is the receiver
$stmt = $conn->prepare("SELECT r.*, u.First_Name AS sender_name FROM requests r JOIN users u ON r.sender_id = u.User_ID WHERE r.receiver_id = :receiver_id ORDER BY r.created_at DESC");
$stmt->execute([':receiver_id' => $_SESSION['user_id']]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch sent requests
$stmt = $conn->prepare("SELECT r.*, u.First_Name AS receiver_name FROM requests r JOIN users u ON r.receiver_id = u.User_ID WHERE r.sender_id = :sender_id ORDER BY r.created_at DESC");
$stmt->execute([':sender_id' => $_SESSION['user_id']]);
$sentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch ongoing requests
$stmt = $conn->prepare("SELECT r.*, u.First_Name AS receiver_name FROM requests r JOIN users u ON r.receiver_id = u.User_ID WHERE (r.sender_id = :user_id OR r.receiver_id = :user_id) AND r.status = 'accepted' ORDER BY r.created_at DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$ongoingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch completed requests
$stmt = $conn->prepare("SELECT r.*, u.First_Name AS receiver_name FROM requests r JOIN users u ON r.receiver_id = u.User_ID WHERE r.sender_id = :sender_id AND r.status = 'completed' ORDER BY r.created_at DESC");
$stmt->execute([':sender_id' => $_SESSION['user_id']]);
$completedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: linear-gradient(to right, #fdfd96, #fff);
            padding: 20px;
        }

        .tabs {
            margin-top: 20px;
            display: flex;
            justify-content: space-around; /* Space tabs evenly */
            margin-bottom: 20px;
            background-color: #fdfd96; /* Light yellow background */
            border-radius: 5px;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow for the tab container */
        }

        .tab {
            flex: 1; /* Make all tabs equal width */
            text-align: center;
            padding: 10px 0;
            font-size: 16px;
            font-weight: bold;
            color: #000; /* Black text */
            cursor: pointer;
            position: relative;
            transition: color 0.3s ease; /* Smooth color transition */
        }

        .tab:hover {
            color: #333; /* Darker text on hover */
        }

        .tab.active {
            color: #000; /* Active tab text color */
            font-weight: bold;
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20%;
            width: 60%; /* Underline width */
            height: 3px;
            background-color: #000; /* Black underline */
            border-radius: 2px;
        }

        .tab-content {
            display: none;
            border: 1px solid #ddd;
            border-radius: 0 5px 5px 5px;
            padding: 15px;
            background-color: #fff;
        }

        .tab-content.active {
            display: block;
        }

        .request {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }

        .request h3 {
            margin: 0 0 10px;
        }

        .request p {
            margin: 5px 0;
        }

        .request-actions {
            margin-top: 10px;
        }

        .request-actions button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .accept {
            background-color: #4CAF50;
            color: white;
        }

        .reject {
            background-color: #f44336;
            color: white;
        }

        .scheduling {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .scheduling h4 {
            margin-bottom: 10px;
        }

        .calendar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .calendar label {
            font-weight: bold;
            margin-right: 5px;
        }

        .calendar input {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-content button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal-content button:hover {
            background-color: #45a049;
        }

        .chat-button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .chat-button:hover {
            background-color: #45a049;
        }

        .accept-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .accept-button:hover {
            background-color: #45a049;
        }

        .decline-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .decline-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

    <!-- Tabs -->
    <div class="tabs">
        <div class="tab active" onclick="showTab('requests')">Requests</div>
        <div class="tab" onclick="showTab('sent')">Sent</div>
        <div class="tab" onclick="showTab('ongoing')">Ongoing</div>
        <div class="tab" onclick="showTab('completed')">Completed</div>
    </div>

    <!-- Tab Content -->
    <div id="requests" class="tab-content">
        <script>
            function fetchIncomingRequests() {
                fetch('fetch_incoming_requests.php')
                    .then(response => response.json())
                    .then(incomingRequests => {
                        const requestsTab = document.getElementById('requests');
                        requestsTab.innerHTML = ''; // Clear existing content

                        if (incomingRequests.error) {
                            requestsTab.innerHTML = `<p>${incomingRequests.error}</p>`;
                            return;
                        }

                        if (incomingRequests.length === 0) {
                            requestsTab.innerHTML = '<p>No incoming requests found.</p>';
                            return;
                        }

                        incomingRequests.forEach(request => {
                            const requestElement = document.createElement('div');
                            requestElement.className = 'request';
                            requestElement.innerHTML = `
                                <h3>Request from ${request.sender_name}</h3>
                                <p>${request.message}</p>
                                <p><strong>Status:</strong> ${request.status}</p>
                                <div class="request-actions">
                                    <button class="accept-button" onclick="acceptRequest(${request.id}); showTab('ongoing');">Accept</button>
                                    <button class="decline-button" onclick="declineRequest(${request.id})">Decline</button>
                                </div>`;
                            requestsTab.appendChild(requestElement);
                        });
                    })
                    .catch(error => console.error('Error fetching incoming requests:', error));
            }

            document.addEventListener('DOMContentLoaded', fetchIncomingRequests);
        </script>
    </div>

    <div id="sent" class="tab-content">
        <script>
            function fetchSentRequests() {
                fetch('fetch_sent_requests.php')
                    .then(response => response.json())
                    .then(sentRequests => {
                        console.log('Sent Requests:', sentRequests); // Debugging: Log the response

                        const sentTab = document.getElementById('sent');
                        sentTab.innerHTML = ''; // Clear existing content

                        if (sentRequests.error) {
                            sentTab.innerHTML = `<p>${sentRequests.error}</p>`;
                            return;
                        }

                        if (sentRequests.length === 0) {
                            sentTab.innerHTML = '<p>No sent requests found.</p>';
                            return;
                        }

                        sentRequests.forEach(request => {
                            const requestElement = document.createElement('div');
                            requestElement.className = 'request';
                            requestElement.innerHTML = `
                                <h3>Request to ${request.receiver_name}</h3>
                                <p>${request.message}</p>
                                <p><strong>Status:</strong> ${request.status}</p>
                                <p><strong>Appointment:</strong> ${request.appointment_date || 'Not set'}</p>
                            `;
                            sentTab.appendChild(requestElement);
                        });
                    })
                    .catch(error => console.error('Error fetching sent requests:', error));
            }

            document.addEventListener('DOMContentLoaded', fetchSentRequests);
        </script>
    </div>

    <div id="ongoing" class="tab-content">
        <?php if (count($ongoingRequests) > 0): ?>
            <?php foreach ($ongoingRequests as $request): ?>
                <div class="request">
                    <h3>Ongoing with <?php echo htmlspecialchars($request['receiver_name']); ?></h3>
                    <p><?php echo htmlspecialchars($request['message']); ?></p>

                    <?php if (!$request['schedule_confirmed']): ?>
                        <div class="scheduling">
                            <h4>Schedule Availability</h4>
                            <p>Your Availability:</p>
                            <div class="calendar">
                                <label for="date-<?php echo $request['id']; ?>">Date:</label>
                                <input type="date" id="date-<?php echo $request['id']; ?>">
                                <label for="start-time-<?php echo $request['id']; ?>">Start Time:</label>
                                <input type="time" id="start-time-<?php echo $request['id']; ?>">
                                <label for="end-time-<?php echo $request['id']; ?>">End Time:</label>
                                <input type="time" id="end-time-<?php echo $request['id']; ?>">
                            </div>
                            <button onclick="saveSchedule(<?php echo $request['id']; ?>)">Save</button>

                            <h4>Other User's Availability:</h4>
                            <div id="other-availability-<?php echo $request['id']; ?>">
                                <?php if ($request['receiver_schedule']): ?>
                                    <p><?php echo htmlspecialchars($request['receiver_schedule']); ?></p>
                                <?php else: ?>
                                    <p>No availability provided yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>Schedule Confirmed: You can now chat!</p>
                        <button class="chat-button" onclick="proceedToChat(<?php echo $request['id']; ?>)">Chat</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No ongoing requests found.</p>
        <?php endif; ?>
    </div>

    <div id="completed" class="tab-content">
        <?php if (count($completedRequests) > 0): ?>
            <?php foreach ($completedRequests as $request): ?>
                <div class="request">
                    <h3>Completed with <?php echo htmlspecialchars($request['receiver_name']); ?></h3>
                    <p><?php echo htmlspecialchars($request['message']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No completed requests found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="saveModal" class="modal" style="display: none;">
        <div class="modal-content">
            <p>Schedule saved successfully!</p>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${tabId}`).classList.add('active');
            document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');
        }

        function saveSchedule(requestId) {
            const date = document.getElementById(`date-${requestId}`).value;
            const startTime = document.getElementById(`start-time-${requestId}`).value;
            const endTime = document.getElementById(`end-time-${requestId}`).value;

            if (!date || !startTime || !endTime) {
                alert('Please fill out all fields.');
                return;
            }

            const schedule = `Date: ${date}, Time: ${startTime} - ${endTime}`;

            fetch('save_schedule.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `request_id=${requestId}&schedule=${encodeURIComponent(schedule)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show the modal
                        document.getElementById('saveModal').style.display = 'flex';

                        // Update the other user's availability dynamically
                        const otherAvailability = document.getElementById(`other-availability-${requestId}`);
                        otherAvailability.innerHTML = `<p>${schedule}</p>`;

                        // Check if both schedules are saved and redirect to chat
                        if (data.schedule_confirmed) {
                            setTimeout(() => {
                                window.location.href = `chat_view.php?request_id=${requestId}`;
                            }, 2000); // Redirect after 2 seconds
                        }
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function closeModal() {
            document.getElementById('saveModal').style.display = 'none';
        }

        function acceptRequest(requestId) {
    if (confirm('Are you sure you want to accept this request?')) {
        fetch('accept_request.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `request_id=${requestId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Request accepted successfully!');
                showTab('ongoing'); // Switch to "Ongoing" tab
                fetchOngoingRequests(); // Fetch ongoing requests dynamically
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}


function fetchOngoingRequests() {
    fetch('fetch_ongoing_requests.php')
        .then(response => response.json())
        .then(ongoingRequests => {
            const ongoingTab = document.getElementById('ongoing');
            ongoingTab.innerHTML = ''; 

            if (ongoingRequests.length === 0) {
                ongoingTab.innerHTML = '<p>No ongoing requests found.</p>';
                return;
            }

            ongoingRequests.forEach(request => {
                const requestElement = document.createElement('div');
                requestElement.className = 'request';
                requestElement.innerHTML = `
                    <h3>Ongoing with ${request.other_name}</h3>
                    <p>Matching skills in progress...</p>
                    <button class="chat-button" onclick="proceedToChat(${request.id})">Chat</button>
                `;
                ongoingTab.appendChild(requestElement);
            });
        })
        .catch(error => console.error('Error fetching ongoing requests:', error));
}




        function loadRequests() {
            fetch('fetch_requests.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    const requestsContainer = document.getElementById('requests-container');
                    requestsContainer.innerHTML = ''; // Clear existing requests

                    if (data.requests.length > 0) {
                        data.requests.forEach(request => {
                            const requestElement = document.createElement('div');
                            requestElement.className = 'request';

                            requestElement.innerHTML = `
                                <h3>Request from ${request.sender_name}</h3>
                                <p>${request.message || 'No message provided'}</p>
                                <p><strong>Status:</strong> ${request.status}</p>
                                <div class="request-actions">
                                    <button class="accept" onclick="acceptRequest(${request.id})">Accept</button>
                                    <button class="reject">Reject</button>
                                </div>
                            `;

                            requestsContainer.appendChild(requestElement);
                        });
                    } else {
                        requestsContainer.innerHTML = '<p>No requests found.</p>';
                    }
                })
                .catch(error => console.error('Error fetching requests:', error));
        }

        function showModal() {
            document.getElementById('saveModal').style.display = 'flex';
        }

        function proceedToChat(requestId) {
            window.location.href = `chat_view.php?request_id=${requestId}`;
        }

        document.querySelector('.tab[onclick="showTab(\'requests\')"]').addEventListener('click', loadRequests);
    </script>
</body>
</html>
