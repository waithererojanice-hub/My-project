<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>View Timetable</title>
  <style>
    body{font-family:Arial;margin:20px;background:#eef4ff}
    h2{color:#1e88e5}
    table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.08)}
    th,td{padding:10px;border:1px solid #ddd;text-align:left}
    th{background:#1e88e5;color:#fff}
    tr:hover{background:#f7fbff}
    .back{margin-top:12px;display:inline-block}
  </style>
</head>
<body>
  <h2>📅 View Timetable</h2>

  <?php
  // safety check: ensure table exists
  $chk = $conn->query("SHOW TABLES LIKE 'timetables'");
  if (!$chk || $chk->num_rows == 0) {
      echo "<p style='color:red;'>Error: table <strong>timetables</strong> not found in the database.</p>";
      exit;
  }

  $sql = "SELECT t.timetable_id, t.day_of_week, t.start_time, t.end_time,
                 c.course_name, u.unit_name, r.room_name, s.semester_name, l.full_name AS lecturer_name
          FROM timetables t
          LEFT JOIN courses c ON t.course_id = c.course_id
          LEFT JOIN units u ON t.unit_id = u.unit_id
          LEFT JOIN rooms r ON t.room_id = r.room_id
          LEFT JOIN semesters s ON t.semester_id = s.semester_id
          LEFT JOIN lecturers l ON t.lecturer_id = l.lecturer_id
          ORDER BY FIELD(t.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday'), t.start_time";

  $res = $conn->query($sql);
  if (!$res) {
      echo "<p style='color:red;'>Query error: " . htmlspecialchars($conn->error) . "</p>";
      exit;
  }

  if ($res->num_rows === 0) {
      echo "<p>No timetable entries found.</p>";
  } else {
      echo "<table>";
      echo "<tr><th>ID</th><th>Day</th><th>Time</th><th>Course</th><th>Unit</th><th>Lecturer</th><th>Room</th><th>Semester</th></tr>";
      while ($row = $res->fetch_assoc()) {
          $start = $row['start_time'] ? date("g:i A", strtotime($row['start_time'])) : '';
          $end   = $row['end_time']   ? date("g:i A", strtotime($row['end_time']))   : '';
          $time  = trim("$start - $end");
          echo "<tr>
                  <td>{$row['timetable_id']}</td>
                  <td>{$row['day_of_week']}</td>
                  <td>{$time}</td>
                  <td>" . htmlspecialchars($row['course_name']) . "</td>
                  <td>" . htmlspecialchars($row['unit_name']) . "</td>
                  <td>" . htmlspecialchars($row['lecturer_name']) . "</td>
                  <td>" . htmlspecialchars($row['room_name']) . "</td>
                  <td>" . htmlspecialchars($row['semester_name']) . "</td>
                </tr>";
      }
      echo "</table>";
  }

  $res->free();
  $conn->close();
  ?>

  <p class="back"><a href="lecturer_dashboard.php">⬅ Back to Dashboard</a></p>
</body>
</html>