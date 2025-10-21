<!DOCTYPE html>
<html>
<head>
    <title>Timetabler Dashboard - SmartScheduler</title>
    <style>
        body { font-family: 'Segoe UI'; display: flex; margin: 0; height: 100vh; }
        .sidebar { width: 230px; background: #004d40; color: white; padding: 20px; }
        .sidebar a { display: block; color: white; text-decoration: none; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
        .sidebar a:hover { background: #00796b; }
        .main { flex: 1; padding: 40px; background: #e0f2f1; }
        h1 { color: #004d40; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>🧭 Menu</h3>
    <a href="add_room.php">🏫 Add Room/Lab</a>
    <a href="create_timetable.php">🗓 Create Timetable</a>
    <a href="process_timetable.php">⚙ Process Timetable</a>
    <a href="login.php">🚪 Logout</a>
</div>

<div class="main">
    <h1>Welcome, Timetabler</h1>
    <p>Use the menu on the left to manage scheduling tasks.</p>
</div>

</body>
</html>