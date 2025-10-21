<?php
session_start();
include 'db_connect.php';

// Ensure only logged-in lecturers can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}

// Get lecturer email from session
$email = $_SESSION['email'];

// Find lecturer_id from lecturers table
$getLecturer = $conn->query("SELECT lecturer_id FROM lecturers WHERE email='$email'");
if ($getLecturer && $getLecturer->num_rows > 0) {
    $lecturer = $getLecturer->fetch_assoc();
    $lecturer_id = $lecturer['lecturer_id'];
} else {
    echo "<p style='color:red;'>Lecturer not found in the database.</p>";
    exit();
}

// Fetch all timetable records assigned to this lecturer
$query = "
SELECT t.timetable_id, c.course_name, u.unit_name, r.room_name, s.semester_name,
       t.day_of_week, t.start_time, t.end_time
FROM timetables t
JOIN courses c ON t.course_id = c.course_id
JOIN units u ON t.unit_id = u.unit_id
JOIN rooms r ON t.room_id = r.room_id
JOIN semesters s ON t.semester_id = s.semester_id
WHERE t.lecturer_id = '$lecturer_id'
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Timetable | Lecturer Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; color: #333; }
        h2 { color: #004aad; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #004aad; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        a { text-decoration: none; color: #004aad; }
        .back-btn { display: inline-block; margin-top: 20px; background: #004aad; color: white; padding: 10px 15px; border-radius: 5px; }
        .back-btn:hover { background: #003080; }
    </style>
</head>
<body>
    <h2>Review Timetable</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Course</th>
                <th>Unit</th>
                <th>Room</th>
                <th>Semester</th>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= htmlspecialchars($row['unit_name']) ?></td>
                    <td><?= htmlspecialchars($row['room_name']) ?></td>
                    <td><?= htmlspecialchars($row['semester_name']) ?></td>
                    <td><?= htmlspecialchars($row['day_of_week']) ?></td>
                    <td><?= htmlspecialchars($row['start_time']) ?></td>
                    <td><?= htmlspecialchars($row['end_time']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No timetable records found for you yet.</p>
    <?php endif; ?>

    <a href="lecturer_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</body>
</html>