<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$getStudent = $conn->query("SELECT student_id, course_id FROM students WHERE email='$email'");
if ($getStudent && $getStudent->num_rows > 0) {
    $student = $getStudent->fetch_assoc();
    $course_id = $student['course_id'];
} else {
    echo "<p style='color:red;'>Student not found in database.</p>";
    exit();
}

$query = "
SELECT c.course_name, u.unit_name, r.room_name, s.semester_name,
       t.day_of_week, t.start_time, t.end_time
FROM timetables t
JOIN courses c ON t.course_id = c.course_id
JOIN units u ON t.unit_id = u.unit_id
JOIN rooms r ON t.room_id = r.room_id
JOIN semesters s ON t.semester_id = s.semester_id
WHERE t.course_id = '$course_id'
ORDER BY t.day_of_week, t.start_time
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <title>Your Timetable</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 40px; background: #f5f7fb; }
    h2 { color: #007bff; }
    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background-color: #007bff; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    a.back { display: inline-block; margin-top: 20px; padding: 10px 15px; background: #007bff; color: white; border-radius: 5px; text-decoration: none; }
    a.back:hover { background: #0056b3; }
  </style>
</head>
<body>
  <h2>Your Timetable</h2>
  <?php if ($result && $result->num_rows > 0): ?>
  <table>
    <tr>
      <th>Course</th><th>Unit</th><th>Room</th><th>Semester</th>
      <th>Day</th><th>Start</th><th>End</th>
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
    <p>No timetable records available yet.</p>
  <?php endif; ?>
  <a href="student_dashboard.php" class="back">⬅ Back to Dashboard</a>
</body>
</html>