<?php
session_start();
include 'db_connect.php';

// Only allow students
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard | Sunrise SmartScheduler</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: #f5f7fb; }
    .sidebar { width: 250px; background-color: #3e128aff; color: white; height: 100vh; padding-top: 30px; position: fixed; }
    .sidebar h2 { text-align: center; margin-bottom: 30px; }
    .sidebar a { display: block; color: white; text-decoration: none; padding: 14px 20px; margin: 5px 15px; border-radius: 8px; }
    .sidebar a:hover { background-color: #230c58ff; }
    .content { margin-left: 260px; padding: 30px; flex-grow: 1; }
    h1 { color: #1c0f65ff; }
    iframe { width: 100%; height: 80vh; border: none; border-radius: 10px; background: white; }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>🎓 Student Panel</h2>
    <a href="student_view_timetable.php">🗓 View Timetable</a>
    <a href="student_view_units.php">📘 View Units</a>
    <a href="student_view_lecturers.php">👨‍🏫 View Lecturers</a>
    <a href="login.php">🚪 Logout</a>
  </div>

  <div class="content">
    <h1>Welcome, Student!</h1>
    <p>Select an option from the menu to view your course information.</p>
  </div>
</body>
</html>