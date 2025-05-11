<?php include 'menuu.php'; ?>
<?php
session_start();

require_once 'SkillSwapDatabase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;
$schedule = isset($_POST['schedule']) ? trim($_POST['schedule']) : '';

if (!$request_id || !$schedule) {
    echo json_encode(['error' => 'Request ID and schedule are required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Determine if the logged-in user is the sender or receiver
    $stmt = $conn->prepare("SELECT sender_id, receiver_id FROM requests WHERE id = :request_id");
    $stmt->execute([':request_id' => $request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        echo json_encode(['error' => 'Request not found']);
        exit();
    }

    $column = ($_SESSION['user_id'] == $request['sender_id']) ? 'sender_schedule' : 'receiver_schedule';

    // Update the user's schedule
    $stmt = $conn->prepare("UPDATE requests SET $column = :schedule WHERE id = :request_id");
    $stmt->execute([':schedule' => $schedule, ':request_id' => $request_id]);

    // Check if both schedules are set
    $stmt = $conn->prepare("SELECT sender_schedule, receiver_schedule FROM requests WHERE id = :request_id");
    $stmt->execute([':request_id' => $request_id]);
    $updatedRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    $schedule_confirmed = false;
    if ($updatedRequest['sender_schedule'] && $updatedRequest['receiver_schedule']) {
        // Mark the schedule as confirmed
        $stmt = $conn->prepare("UPDATE requests SET schedule_confirmed = 1 WHERE id = :request_id");
        $stmt->execute([':request_id' => $request_id]);
        $schedule_confirmed = true;
    }

    echo json_encode(['success' => 'Schedule saved successfully', 'schedule_confirmed' => $schedule_confirmed]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <style>
        body {            font-family: Arial, sans-serif;            margin: 0;            background: linear-gradient(to right, #fdfd96, #fff);            padding: 20px;        }        .tabs {            margin-top: 20px;            display: flex;            justify-content: space-around; /* Space tabs evenly */            margin-bottom: 20px;            background-color: #fdfd96; /* Light yellow background */            border-radius: 5px;            padding: 10px 0;            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow for the tab container */        }        .tab {            flex: 1; /* Make all tabs equal width */            text-align: center;            padding: 10px 0;            font-size: 16px;            font-weight: bold;            color: #000; /* Black text */            cursor: pointer;            position: relative;            transition: color 0.3s ease; /* Smooth color transition */        }        .tab:hover {            color: #333; /* Darker text on hover */        }        .tab.active {            color: #000; /* Active tab text color */            font-weight: bold;        }        .tab.active::after {            content: '';            position: absolute;            bottom: 0;            left: 20%;            width: 60%; /* Underline width */            height: 3px;            background-color: #000; /* Black underline */            border-radius: 2px;        }        .tab-content {
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
        <div id="requests-container">
            <!-- Requests will be dynamically loaded here -->
        </div>
    </div>
    <div id="sent" class="tab-content">
        <?php if (count($sentRequests) > 0): ?>
            <?php foreach ($sentRequests as $request): ?>
                <div class="request">
                    <h3>Request to <?php echo htmlspecialchars($request['receiver_name']); ?></h3>
                    <p><?php echo htmlspecialchars($request['message']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($request['status']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No sent requests found.</p>
        <?php endif; ?>
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
            const schedule = document.getElementById(`schedule-${requestId}`).value;
            fetch('save_schedule.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `request_id=${requestId}&schedule=${encodeURIComponent(schedule)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Schedule saved successfully!');
                        location.reload(); // Reload to update the other user's view
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
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
                            location.reload(); // Reload the page to update the tabs
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
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

        document.querySelector('.tab[onclick="showTab(\'requests\')"]').addEventListener('click', loadRequests);
    </script>
</body>
</html>