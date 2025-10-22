<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lecturer Dashboard - Sunrise SmartScheduler</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #eef2f5;
            display: flex;
        }
        .sidebar {
            width: 220px;
            height: 100vh;
            background-color: #48657eff;
            color: white;
            position: fixed;
            padding: 20px;
        }
        .sidebar h2 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 40px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #3c5066ff;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
            flex-grow: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #3b566eff;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .logout {
            color: #ffeb3b;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>📚 Lecturer Panel</h2>
    <a href="lecturer_view_timetable.php">📅 View Timetables</a>
    <a href="lecturer_review_timetable.php">✅ Review Timetable</a>
    <a href="lecturer_confirm_timetable.php">💬 Confirm Timetable</a>
    <a class='change' href='request_change.php?tid={$row['timetable_id']}' </a>
    <a href="login.php" class="logout">🚪 Logout</a>
</div>

<div class="content">
    <h2>Welcome, Lecturer</h2>
    <p>Here is your current teaching timetable:</p>

    <?php
    // Fetch timetable data from the database
    $sql = "SELECT t.day_of_week, t.start_time, t.end_time, 
                   c.course_name, u.unit_name, r.room_name, s.semester_name
            FROM timetables t
            JOIN courses c ON t.course_id = c.course_id
            JOIN units u ON t.unit_id = u.unit_id
            JOIN rooms r ON t.room_id = r.room_id
            JOIN semesters s ON t.semester_id = s.semester_id
            ORDER BY FIELD(t.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday'), t.start_time";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Day</th>
                <th>Time</th>
                <th>Course</th>
                <th>Unit</th>
                <th>Room</th>
                <th>Semester</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            $start = date("g:i A", strtotime($row['start_time']));
            $end = date("g:i A", strtotime($row['end_time']));
            echo "<tr>
                    <td>{$row['day_of_week']}</td>
                    <td>$start - $end</td>
                    <td>{$row['course_name']}</td>
                    <td>{$row['unit_name']}</td>
                    <td>{$row['room_name']}</td>
                    <td>{$row['semester_name']}</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "<p style='color:red;'>No timetable assigned yet.</p>";
    }

    $conn->close();
    ?>
</div>

</body>
</html>