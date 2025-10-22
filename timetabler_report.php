<?php
include 'db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Timetabler Report — SmartScheduler</title>
<style>
    body {
        margin: 0;
        font-family: "Poppins", sans-serif;
        background-color: #f4f7fc;
    }
    .sidebar {
        width: 240px;
        background-color: #198754;
        color: white;
        height: 100vh;
        position: fixed;
        padding: 20px;
    }
    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
    }
    .sidebar a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 12px;
        margin: 6px 0;
        border-radius: 6px;
        transition: background 0.3s;
    }
    .sidebar a:hover {
        background-color: #146c43;
    }
    .main {
        margin-left: 260px;
        padding: 20px 40px;
    }
    h1 {
        color: #198754;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: center;
    }
    th {
        background: #198754;
        color: white;
    }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Timetabler Panel</h2>
    <a href="timetabler_dashboard.php">🏠 Dashboard</a>
    <a href="timetabler_report.php">📈 Timetable Report</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h1>📈 Timetable Utilization Report</h1>

    <table>
        <tr>
            <th>Room</th>
            <th>Total Sessions</th>
            <th>Most Used Day</th>
            <th>Total Lecturers Assigned</th>
        </tr>

        <?php
        $query = "
            SELECT 
                r.room_name,
                COUNT(t.timetable_id) AS total_sessions,
                (SELECT t2.day_of_week 
                 FROM timetables t2 
                 WHERE t2.room_id = r.room_id 
                 GROUP BY t2.day_of_week 
                 ORDER BY COUNT(*) DESC LIMIT 1) AS most_used_day,
                COUNT(DISTINCT t.lecturer_id) AS total_lecturers
            FROM rooms r
            LEFT JOIN timetables t ON r.room_id = t.room_id
            GROUP BY r.room_id
        ";

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['room_name']}</td>
                        <td>{$row['total_sessions']}</td>
                        <td>" . ($row['most_used_day'] ?? '—') . "</td>
                        <td>{$row['total_lecturers']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No timetable data found.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>