<?php
session_start();
include 'db_connect.php';

// Only allow Dean role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dean') {
    header("Location: login.php");
    exit();
}

// Handle Dean timetable approval
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_timetable_id'])) {
    $timetableId = intval($_POST['approve_timetable_id']);
    $conn->query("UPDATE timetables SET dean_approval = 'Approved' WHERE timetable_id = $timetableId");
    echo "<script>alert('✅ Timetable approved successfully by Dean!'); window.location.href='dean_dashboard.php#approve';</script>";
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
    .sidebar { width: 250px; background-color: #9c96a0ff; color: white; height: 100vh; padding-top: 30px; position: fixed; }
    .sidebar h2 { text-align: center; margin-bottom: 30px; }
    .sidebar a { display: block; color: white; text-decoration: none; padding: 14px 20px; margin: 5px 15px; border-radius: 8px; }
    .sidebar a:hover { background-color: #2d0b3fff; }
    .content { margin-left: 260px; padding: 30px; flex-grow: 1; }
    h1 { color: #35104cff; }
    h2 { color: #35104cff; border-left: 5px solid #35104cff; padding-left: 10px; }
    p { color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
    th { background: #35104cff; color: white; }
    .approve-btn {
      background: #28a745;
      color: white;
      padding: 6px 10px;
      border-radius: 5px;
      cursor: pointer;
      border: none;
      font-weight: bold;
    }
    .approve-btn:hover { background: #218838; }
    .approved-label { color: green; font-weight: bold; }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>🎓 Dean Panel</h2>
    <a href="#view">🗓 View Timetable</a>
    <a href="#approve">✅ Approve Timetable</a>
    <a href="dean_department_reports.php">📊 Department Reports</a>
    <a href="login.php">🚪 Logout</a>
  </div>

  <div class="content">
    <h1>Welcome, Dean!</h1>
    <p>Select a menu item to manage departmental scheduling and approvals.</p>

    <!-- View Timetable -->
    <section id="view">
      <h2>🗓 Department Timetable Overview</h2>
      <?php
      $query = "
        SELECT t.timetable_id, t.day_of_week, t.start_time, t.end_time,
               u.unit_name, c.course_name, r.room_name, t.hod_approval, t.dean_approval
        FROM timetables t
        JOIN units u ON t.unit_id = u.unit_id
        JOIN courses c ON t.course_id = c.course_id
        JOIN rooms r ON t.room_id = r.room_id
        ORDER BY t.day_of_week, t.start_time
      ";
      $result = $conn->query($query);

      if ($result->num_rows > 0) {
          echo "<table>
                  <tr>
                      <th>Day</th>
                      <th>Time</th>
                      <th>Unit</th>
                      <th>Course</th>
                      <th>Room</th>
                      <th>HOD Approval</th>
                      <th>Dean Approval</th>
                  </tr>";
          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['day_of_week']}</td>
                      <td>{$row['start_time']} - {$row['end_time']}</td>
                      <td>{$row['unit_name']}</td>
                      <td>{$row['course_name']}</td>
                      <td>{$row['room_name']}</td>
                      <td>" . ($row['hod_approval'] == 'Approved' ? "✅ Approved" : "❌ Pending") . "</td>
                      <td>" . ($row['dean_approval'] == 'Approved' ? "✅ Approved" : "❌ Pending") . "</td>
                    </tr>";
          }
          echo "</table>";
      } else {
          echo "<p>No timetable records found.</p>";
      }
      ?>
    </section>

    <!-- Approve Timetable -->
    <section id="approve">
      <h2>✅ Approve Timetable</h2>
      <?php
      $pending = $conn->query("
        SELECT t.timetable_id, t.day_of_week, t.start_time, t.end_time,
               u.unit_name, c.course_name, r.room_name, t.hod_approval, t.dean_approval
        FROM timetables t
        JOIN units u ON t.unit_id = u.unit_id
        JOIN courses c ON t.course_id = c.course_id
        JOIN rooms r ON t.room_id = r.room_id
        WHERE t.hod_approval = 'Approved'
        ORDER BY t.day_of_week, t.start_time
      ");

      if ($pending->num_rows > 0) {
          echo "<table>
                  <tr>
                      <th>Day</th>
                      <th>Time</th>
                      <th>Unit</th>
                      <th>Course</th>
                      <th>Room</th>
                      <th>Status</th>
                  </tr>";
          while ($row = $pending->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['day_of_week']}</td>
                      <td>{$row['start_time']} - {$row['end_time']}</td>
                      <td>{$row['unit_name']}</td>
                      <td>{$row['course_name']}</td>
                      <td>{$row['room_name']}</td>
                      <td>";
              if ($row['dean_approval'] == 'Approved') {
                  echo "<span class='approved-label'>✅ Approved</span>";
              } else {
                  echo "<form method='POST' action='' style='margin:0;'>
                          <input type='hidden' name='approve_timetable_id' value='{$row['timetable_id']}'>
                          <button type='submit' class='approve-btn'>Approve</button>
                        </form>";
              }
              echo "</td></tr>";
          }
          echo "</table>";
      } else {
          echo "<p>No timetables available for approval yet.</p>";
      }
      ?>
    </section>
  </div>
</body>
</html>