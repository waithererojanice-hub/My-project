<?php
// timetabler_dashboard.php
include 'db_connect.php';
session_start();

// helper: check if a column exists in a table
function column_exists($conn, $table, $column) {
    $sql = "SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return ($r['cnt'] > 0);
}

// helper: check if a table exists
function table_exists($conn, $table) {
    $sql = "SELECT COUNT(*) AS cnt FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return ($r['cnt'] > 0);
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Timetabler Dashboard — SmartScheduler</title>
<style>
body { margin:0; font-family:Poppins, sans-serif; background:#f5f6fa; display:flex; }
.sidebar { width:240px; background:#16a085; color:#fff; height:100vh; padding:20px; position:fixed; }
.sidebar h2{ text-align:center; margin-bottom:20px; }
.sidebar a{ color:#fff; text-decoration:none; display:block; padding:10px; margin:6px 0; border-radius:6px; }
.sidebar a:hover{ background:#117a65; }
.main { margin-left:260px; padding:24px; width:100%; }
.header { background:#1abc9c; color:#fff; padding:14px; border-radius:8px; margin-bottom:20px; }
.section { background:#fff; padding:18px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.08); margin-bottom:20px; }
h2.section-title{ color:#117a65; margin:0 0 12px 0; }
table{ width:100%; border-collapse:collapse; margin-top:12px; }
th, td{ padding:10px; border:1px solid #e6e6e6; text-align:center; }
th{ background:#16a085; color:#fff; }
.status{ padding:6px 10px; border-radius:6px; color:#fff; font-weight:600; display:inline-block; }
.hod{ background:#f39c12; } .dean{ background:#2980b9; } .final{ background:#27ae60; } .pending{ background:#7f8c8d; }
.card-grid{ display:flex; gap:12px; flex-wrap:wrap; }
.card{ background:#ecf6f3; padding:12px; border-radius:8px; min-width:180px; text-align:center; }
.card h4{ margin:0 0 8px 0; color:#117a65; }
.card p{ margin:0; font-weight:700; color:#0b6b5a; font-size:18px; }
button { background:#16a085; color:#fff; border:none; padding:10px 14px; border-radius:6px; cursor:pointer; }
</style>
</head>
<body>

<div class="sidebar">
  <h2>Timetabler Panel</h2>
  <a href="#add_room">🏫 Add Room</a>
  <a href="#create_timetable">📅 Create Timetable</a>
  <a href="#process_timetable">⚙ Process Timetable</a>
  <a href="#report">📊 Timetable Report</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
  <div class="header">
    <h1>Welcome, Timetabler</h1>
    <p>Choose an action from the left menu to manage timetables and reports.</p>
  </div>

  <!-- Add Room -->
  <section id="add_room" class="section">
    <h2 class="section-title">🏫 Add Room or Lab</h2>
    <form method="POST">
      <input type="hidden" name="action" value="add_room">
      <input name="room_name" placeholder="Room name" required><br><br>
      <input name="building_name" placeholder="Building name" required><br><br>
      <input type="number" name="capacity" placeholder="Capacity" required><br><br>
      <button type="submit">Add Room</button>
    </form>
  </section>

  <!-- Create Timetable -->
  <section id="create_timetable" class="section">
    <h2 class="section-title">📅 Create Timetable</h2>
    <form method="POST">
      <input type="hidden" name="action" value="create_timetable">
      <?php
      // dropdowns for course, unit, lecturer, room
      $courses = $conn->query("SELECT course_id, course_name FROM courses");
      echo '<select name="course_id" required><option value="">Select course</option>';
      while ($r = $courses->fetch_assoc()) echo "<option value='{$r['course_id']}'>{$r['course_name']}</option>";
      echo '</select><br><br>';

      $units = $conn->query("SELECT unit_id, unit_name FROM units");
      echo '<select name="unit_id" required><option value="">Select unit</option>';
      while ($r = $units->fetch_assoc()) echo "<option value='{$r['unit_id']}'>{$r['unit_name']}</option>";
      echo '</select><br><br>';

      $lect = $conn->query("SELECT lecturer_id, full_name FROM lecturers");
      echo '<select name="lecturer_id" required><option value="">Select lecturer</option>';
      while ($r = $lect->fetch_assoc()) echo "<option value='{$r['lecturer_id']}'>{$r['full_name']}</option>";
      echo '</select><br><br>';

      $rooms = $conn->query("SELECT room_id, room_name, building_name FROM rooms");
      echo '<select name="room_id" required><option value="">Select room</option>';
      while ($r = $rooms->fetch_assoc()) echo "<option value='{$r['room_id']}'>{$r['room_name']} ({$r['building_name']})</option>";
      echo '</select><br><br>';
      ?>
      <input name="day_of_week" placeholder="Day (e.g. Monday)" required><br><br>
      <input type="time" name="start_time" required> to 
      <input type="time" name="end_time" required><br><br>
      <input type="number" name="semester_id" placeholder="Semester ID" required><br><br>
      <button type="submit">Create Timetable</button>
    </form>
  </section>

  <!-- Process Timetable -->
  <section id="process_timetable" class="section">
    <h2 class="section-title">⚙ Process Timetable (Approval Progress)</h2>

    <?php
    $hasHOD = column_exists($conn, 'timetables', 'hod_approval');
    $hasDean = column_exists($conn, 'timetables', 'dean_approval');

    $sql = "
        SELECT t.timetable_id, t.day_of_week, t.start_time, t.end_time,
               u.unit_name, c.course_name, r.room_name,
               " . ($hasHOD ? "t.hod_approval," : "NULL AS hod_approval,") . "
               " . ($hasDean ? "t.dean_approval" : "NULL AS dean_approval") . "
        FROM timetables t
        JOIN units u ON t.unit_id = u.unit_id
        JOIN courses c ON t.course_id = c.course_id
        JOIN rooms r ON t.room_id = r.room_id
        ORDER BY t.day_of_week, t.start_time
    ";
    $result = $conn->query($sql);

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
                    <th>Overall</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            $hodStatus = ($row['hod_approval'] === 'Approved') ? "<span class='status hod'>✅ Approved</span>" : "<span class='status pending'>⏳ Pending</span>";
            $deanStatus = ($row['dean_approval'] === 'Approved') ? "<span class='status dean'>✅ Approved</span>" : "<span class='status pending'>⏳ Pending</span>";
            $overall = ($row['hod_approval'] === 'Approved' && $row['dean_approval'] === 'Approved')
                        ? "<span class='status final'>✅ Fully Approved</span>"
                        : "<span class='status pending'>⏳ Waiting</span>";

            echo "<tr>
                    <td>{$row['day_of_week']}</td>
                    <td>{$row['start_time']} - {$row['end_time']}</td>
                    <td>{$row['unit_name']}</td>
                    <td>{$row['course_name']}</td>
                    <td>{$row['room_name']}</td>
                    <td>$hodStatus</td>
                    <td>$deanStatus</td>
                    <td>$overall</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No timetable data available.</p>";
    }
    ?>
  </section>

  <!-- Report -->
  <section id="report" class="section">
    <h2 class="section-title">📊 Timetable Summary Report</h2>
    <?php
    $totRooms = $conn->query("SELECT COUNT(*) AS t FROM rooms")->fetch_assoc()['t'];
    $totSessions = $conn->query("SELECT COUNT(*) AS t FROM timetables")->fetch_assoc()['t'];
    $totLecturers = $conn->query("SELECT COUNT(DISTINCT lecturer_id) AS t FROM timetables")->fetch_assoc()['t'];
    ?>
    <div class="card-grid">
      <div class="card"><h4>🏫 Total Rooms</h4><p><?= $totRooms ?></p></div>
      <div class="card"><h4>🕒 Total Sessions</h4><p><?= $totSessions ?></p></div>
      <div class="card"><h4>👨‍🏫 Lecturers Assigned</h4><p><?= $totLecturers ?></p></div>
    </div>
  </section>

</div>

<?php
// handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_room') {
        $stmt = $conn->prepare("INSERT INTO rooms (room_name, building_name, capacity) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $_POST['room_name'], $_POST['building_name'], $_POST['capacity']);
        $stmt->execute(); $stmt->close();
        echo "<script>alert('✅ Room added successfully');</script>";
    }
    if ($_POST['action'] === 'create_timetable') {
        $stmt = $conn->prepare("INSERT INTO timetables (course_id, unit_id, lecturer_id, room_id, semester_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiissss", $_POST['course_id'], $_POST['unit_id'], $_POST['lecturer_id'], $_POST['room_id'], $_POST['semester_id'], $_POST['day_of_week'], $_POST['start_time'], $_POST['end_time']);
        $stmt->execute(); $stmt->close();
        echo "<script>alert('✅ Timetable created successfully');</script>";
    }
}
?>
</body>
</html>