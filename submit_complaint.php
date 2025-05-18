<?php
// submit_complaint.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "", "skillswap");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_no = $_POST['contact_no'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO complaints (name, email, contact_no, message, status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("ssss", $name, $email, $contact_no, $message);

    if ($stmt->execute()) {
        echo "Complaint submitted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML Form -->
<form method="POST" action="submit_complaint.php">
    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br>

    <label>Contact No.:</label><br>
    <input type="text" name="contact_no" required><br>

    <label>Message:</label><br>
    <textarea name="message" required></textarea><br>

    <button type="submit">Send Messaget</button>
</form>
