<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dean') {
    header("Location: login.php");
    exit();
}

// Fetch department report data
$query = "
SELECT d.department_name,
       COUNT(DISTINCT c.course_id) AS total_courses,
       COUNT(DISTINCT l.lecturer_id) AS total_lecturers,
       COUNT(DISTINCT s.student_id) AS total_students
FROM departments d
LEFT JOIN courses c ON d.department_id = c.department_id
LEFT JOIN lecturers l ON d.department_id = l.department_id
LEFT JOIN students s ON c.course_id = s.course_id
GROUP BY d.department_id, d.department_name
ORDER BY d.department_name
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <title>Department Reports - Dean Dashboard</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fd; margin: 40px; }
    h2 { color: #006064; }
    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background-color: #006064; color: white; }
    tr:nth-child(even) { background: #f2f2f2; }
    a.back { display: inline-block; margin-top: 20px; padding: 10px 15px; background: #006064; color: white; border-radius: 5px; text-decoration: none; }
    a.back:hover { background: #00363a; }
  </style>
</head>
<body>

  <h2>📊 Department Reports</h2>

  <?php if ($result && $result->num_rows > 0): ?>
  <table>
    <tr>
      <th>Department</th>
      <th>Total Courses</th>
      <th>Total Lecturers</th>
      <th>Total Students</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['department_name']) ?></td>
      <td><?= htmlspecialchars($row['total_courses']) ?></td>
      <td><?= htmlspecialchars($row['total_lecturers']) ?></td>
      <td><?= htmlspecialchars($row['total_students']) ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p>No department data found.</p>
  <?php endif; ?>

  <a href="dean_dashboard.php" class="back">⬅ Back to Dashboard</a>
</body>
</html>