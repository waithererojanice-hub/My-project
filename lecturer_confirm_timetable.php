<?php
include 'db_connect.php';

// Temporary lecturer ID for testing
$lecturer_id = 1; // later replace with $_SESSION['lecturer_id']

// Handle confirmation
if (isset($_GET['confirm_id'])) {
    $tid = $_GET['confirm_id'];
    $conn->query("UPDATE timetables SET day_of_week = day_of_week WHERE timetable_id = $tid AND lecturer_id = $lecturer_id");
    echo "<p style='color:green;'>✅ Timetable confirmed successfully!</p>";
}

// Fetch lecturer’s timetable
$query = "
    SELECT t.timetable_id, t.day_of_week, t.start_time, t.end_time,
           u.unit_name, c.course_name, r.room_name
    FROM timetables t
    JOIN units u ON t.unit_id = u.unit_id
    JOIN courses c ON t.course_id = c.course_id
    JOIN rooms r ON t.room_id = r.room_id
    WHERE t.lecturer_id = $lecturer_id
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Timetable</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            padding: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #0066cc;
            color: white;
        }
        a {
            padding: 6px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .confirm {
            background: #28a745;
            color: white;
        }
        .change {
            background: #ffc107;
            color: black;
        }
    </style>
</head>
<body>
    <h2>Confirm Your Timetable</h2>

    <table>
        <tr>
            <th>Day</th>
            <th>Time</th>
            <th>Unit</th>
            <th>Course</th>
            <th>Room</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['day_of_week']}</td>
                        <td>{$row['start_time']} - {$row['end_time']}</td>
                        <td>{$row['unit_name']}</td>
                        <td>{$row['course_name']}</td>
                        <td>{$row['room_name']}</td>
                        <td>
                            <a class='confirm' href='confirm_timetable.php?confirm_id={$row['timetable_id']}'>✅ Confirm</a>
                            <a class='change' href='request_change.php?tid={$row['timetable_id']}'>✏ Request Change</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No timetable found for this lecturer.</td></tr>";
        }
        ?>
    </table>
</body>
</html>