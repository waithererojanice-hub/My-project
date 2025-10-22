<?php
session_start();
include 'db_connect.php';

// Only allow Dean role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dean') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dean Dashboard | Sunrise SmartScheduler</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: #f4f5fb; }
    .sidebar { width: 250px; background-color: #311045ff; color: white; height: 100vh; padding-top: 30px; position: fixed; }
    .sidebar h2 { text-align: center; margin-bottom: 30px; }
    .sidebar a { display: block; color: white; text-decoration: none; padding: 14px 20px; margin: 5px 15px; border-radius: 8px; }
    .sidebar a:hover { background-color: #2d0b3fff; }
    .content { margin-left: 260px; padding: 30px; flex-grow: 1; }
    h1 { color: #35104cff; }
    p { color: #333; }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>🎓 Dean Panel</h2>
    <a href="dean_view_timetable.php">🗓 View Timetable</a>
    <a href="dean_approve_timetable.php">✅ Approve Timetable</a>
    <a href="dean_department_reports.php">📊 Department Reports</a>
    <a href="login.php">🚪 Logout</a>
  </div>

  <div class="content">
    <h1>Welcome, Dean!</h1>
    <p>Select a menu item to manage departmental scheduling and approvals.</p>
  </div>
</body>
</html>
