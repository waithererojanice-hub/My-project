<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$getStudent = $conn->query("SELECT course_id FROM students WHERE email='$email'");
if ($getStudent && $getStudent->num_rows > 0) {
    $student = $getStudent->fetch_assoc();
    $course_id = $student['course_id'];
} else { echo "<p style='color:red;'>Student not found.</p>"; exit(); }

$result = $conn->query("SELECT unit_code, unit_name FROM units WHERE course_id='$course_id'");
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <title>View Units</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 40px; background: #f5f7fb; }
    h2 { color: #007bff; text-align:center; }
    table { width: 80%; border-collapse: collapse; margin: auto; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background-color: #007bff; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    .back { display: block; width: 160px; margin: 20px auto; text-align: center; background: #007bff; color: white; padding: 10px; border-radius: 5px; text-decoration: none; }
    .back:hover { background: #0056b3; }
  </style>
</head>
<body>
  <h2>Units Offered</h2>
  <?php if ($result && $result->num_rows > 0): ?>
  <table>
    <tr><th>Unit Code</th><th>Unit Name</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr><td><?= htmlspecialchars($row['unit_code']) ?></td><td><?= htmlspecialchars($row['unit_name']) ?></td></tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?><p style='text-align:center;'>No units found.</p><?php endif; ?>
  <a href="student_dashboard.php" class="back">⬅ Back to Dashboard</a>
</body>
</html>