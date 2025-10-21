<?php
include 'db_connect.php';
session_start();

// Temporary lecturer_id for testing — replace with session later
$lecturer_id = 1;

// Ensure the timetable ID exists
if (!isset($_GET['tid'])) {
    die("<p style='color:red;'>Invalid timetable request!</p>");
}

$timetable_id = $_GET['tid'];

// When lecturer submits a request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reason = $conn->real_escape_string($_POST['reason']);

    // Create the table in your DB if it doesn’t exist:
    // CREATE TABLE change_requests (
    //  request_id INT AUTO_INCREMENT PRIMARY KEY,
    //  timetable_id INT,
    //  lecturer_id INT,
    //  reason TEXT,
    //  status VARCHAR(20) DEFAULT 'Pending',
    //  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    // );

    $query = "INSERT INTO change_requests (timetable_id, lecturer_id, reason, status)
              VALUES ('$timetable_id', '$lecturer_id', '$reason', 'Pending')";

    if ($conn->query($query) === TRUE) {
        echo "<script>alert('✅ Request submitted successfully!'); window.location='lecturer_confirm_timetable.php';</script>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Request Timetable Change</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f4f7fb;
        padding: 30px;
    }
    .container {
        max-width: 450px;
        background: white;
        padding: 25px;
        margin: 40px auto;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    textarea {
        width: 100%;
        height: 120px;
        padding: 10px;
        margin-top: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    button {
        background: #007bff;
        color: white;
        border: none;
        padding: 10px;
        width: 100%;
        margin-top: 15px;
        border-radius: 6px;
        cursor: pointer;
    }
    button:hover {
        background: #0056b3;
    }
</style>
</head>
<body>

<div class="container">
    <h2>✏ Request Timetable Change</h2>
    <form method="POST">
        <label>Reason for Change:</label>
        <textarea name="reason" placeholder="Explain why you need this change..." required></textarea>
        <button type="submit">Submit Request</button>
    </form>
</div>

</body>
</html>