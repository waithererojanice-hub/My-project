<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dean') {
    header("Location: login.php");
    exit();
}

// Handle form submission (when dean clicks approve)
if (isset($_POST['approve_id'])) {
    $timetable_id = $_POST['approve_id'];

    // Update timetable to show that the dean has approved it (if you later add an approval column)
    // For now, this just displays a message since that column doesn’t exist yet.
    $message = "Timetable ID $timetable_id has been approved successfully.";
}

// Fetch timetables for display
$query = "
SELECT t.timetable_id, c.course_name, u.unit_name, r.room_name, s.semester_name,
       t.day_of_week, t.start_time, t.end_time
FROM timetables t
JOIN courses c ON t.course_id = c.course_id
JOIN units u ON t.unit_id = u.unit_id
JOIN rooms r ON t.room_id = r.room_id
JOIN semesters s ON t.semester_id = s.semester_id
ORDER BY t.day_of_week, t.start_time
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <title>Approve Timetable - Dean Dashboard</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 40px; background: #f4f5fb; }
    h2 { color: #00838f; }
    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background-color: #00838f; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    form { display: inline; }
    button { padding: 6px 12px; border: none; background: #00838f; color: white; border-radius: 5px; cursor: pointer; }
    button:hover { background: #005662; }
    .msg { margin-bottom: 15px; color: green; font-weight: bold; }
    a.back { display: inline-block; margin-top: 20px; padding: 10px 15px; background: #00838f; color: white; border-radius: 5px; text-decoration: none; }
    a.back:hover { background: #005662; }
  </style>
</head>
<body>
  <h2>Approve Timetables</h2>

  <?php if (isset($message)) echo "<p class='msg'>$message</p>"; ?>

  <?php if ($result && $result->num_rows > 0): ?>
  <table>
    <tr>
      <th>Course</th><th>Unit</th><th>Room</th><th>Semester</th>
      <th>Day</th><th>Start</th><th>End</th><th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['course_name']) ?></td>
      <td><?= htmlspecialchars($row['unit_name']) ?></td>
      <td><?= htmlspecialchars($row['room_name']) ?></td>
      <td><?= htmlspecialchars($row['semester_name']) ?></td>
      <td><?= htmlspecialchars($row['day_of_week']) ?></td>
      <td><?= htmlspecialchars($row['start_time']) ?></td>
      <td><?= htmlspecialchars($row['end_time']) ?></td>
      <td>
        <form method="POST">
          <input type="hidden" name="approve_id" value="<?= $row['timetable_id'] ?>">
          <button type="submit">Approve ✅</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p>No timetable records found.</p>
  <?php endif; ?>

  <a href="dean_dashboard.php" class="back">⬅ Back to Dashboard</a>
</body>
</html>