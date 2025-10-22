<?php
include 'db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>HOD Report — SmartScheduler</title>
<style>
    body {
        margin: 0;
        font-family: "Poppins", sans-serif;
        background-color: #f4f7fc;
    }
    .sidebar {
        width: 240px;
        background-color: #0a58ca;
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
        background-color: #084298;
    }
    .main {
        margin-left: 260px;
        padding: 20px 40px;
    }
    h1 {
        color: #0a58ca;
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
        background: #0a58ca;
        color: white;
    }
</style>
</head>
<body>

<div class="sidebar">
    <h2>HOD Panel</h2>
    <a href="hod_dashboard.php">🏠 Dashboard</a>
    <a href="hod_report.php">📊 Department Report</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h1>📊 Department Report</h1>

    <table>
        <tr>
            <th>Course Name</th>
            <th>Total Units</th>
            <th>Total Lecturers</th>
            <th>Total Students</th>
        </tr>

        <?php
        $query = "
            SELECT 
                c.course_name,
                COUNT(DISTINCT u.unit_id) AS total_units,
                COUNT(DISTINCT l.lecturer_id) AS total_lecturers,
                COUNT(DISTINCT s.student_id) AS total_students
            FROM courses c
            LEFT JOIN units u ON c.course_id = u.course_id
            LEFT JOIN lecturers l ON c.department_id = l.department_id
            LEFT JOIN students s ON c.course_id = s.course_id
            GROUP BY c.course_id
        ";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['course_name']}</td>
                        <td>{$row['total_units']}</td>
                        <td>{$row['total_lecturers']}</td>
                        <td>{$row['total_students']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No records found.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>