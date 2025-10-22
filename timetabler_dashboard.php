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
/* Clean green theme (what you liked) */
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
      <!-- use selects where possible; fallback to numeric inputs when no rows -->
      <?php
      // Courses
      $courses = $conn->query("SELECT course_id, course_name FROM courses");
      if ($courses && $courses->num_rows > 0) {
          echo '<select name="course_id" required><option value="">Select course</option>';
          while ($r = $courses->fetch_assoc()) echo "<option value='{$r['course_id']}'>".htmlspecialchars($r['course_name'])."</option>";
          echo '</select><br><br>';
      } else {
          echo '<input name="course_id" placeholder="Course ID" required><br><br>';
      }

      // Units
      $units = $conn->query("SELECT unit_id, unit_name FROM units");
      if ($units && $units->num_rows > 0) {
          echo '<select name="unit_id" required><option value="">Select unit</option>';
          while ($r = $units->fetch_assoc()) echo "<option value='{$r['unit_id']}'>".htmlspecialchars($r['unit_name'])."</option>";
          echo '</select><br><br>';
      } else {
          echo '<input name="unit_id" placeholder="Unit ID" required><br><br>';
      }

      // Lecturers
      $lect = $conn->query("SELECT lecturer_id, full_name FROM lecturers");
      if ($lect && $lect->num_rows > 0) {
          echo '<select name="lecturer_id" required><option value="">Select lecturer</option>';
          while ($r = $lect->fetch_assoc()) echo "<option value='{$r['lecturer_id']}'>".htmlspecialchars($r['full_name'])."</option>";
          echo '</select><br><br>';
      } else {
          echo '<input name="lecturer_id" placeholder="Lecturer ID" required><br><br>';
      }

      // Rooms
      $rooms = $conn->query("SELECT room_id, room_name, building_name FROM rooms");
      if ($rooms && $rooms->num_rows > 0) {
          echo '<select name="room_id" required><option value="">Select room</option>';
          while ($r = $rooms->fetch_assoc()) echo "<option value='{$r['room_id']}'>".htmlspecialchars($r['room_name'])." ({$r['building_name']})</option>";
          echo '</select><br><br>';
      } else {
          echo '<input name="room_id" placeholder="Room ID" required><br><br>';
      }
      ?>
      <input name="day_of_week" placeholder="Day (e.g. Monday)" required><br><br>
      <input type="time" name="start_time" required> &nbsp; to &nbsp; <input type="time" name="end_time" required><br><br>
      <button type="submit">Create Timetable</button>
    </form>
  </section>

  <!-- Process Timetable -->
  <section id="process_timetable" class="section">
    <h2 class="section-title">⚙ Process Timetable (Approval Progress)</h2>

    <?php
    // Determine whether approval columns exist
    $has_hod_col  = column_exists($conn, 'timetables', 'hod_approved');
    $has_dean_col = column_exists($conn, 'timetables', 'dean_approved');

    // Determine whether there's a separate approvals table (recommended)
    $has_approvals_table = table_exists($conn, 'approvals');

    // Base query for timetables (do not select non-existent columns)
    $q = "SELECT t.timetable_id, t.day_of_week, t.start_time, t.end_time,
                 u.unit_name, c.course_name, r.room_name
          FROM timetables t
          JOIN units u ON t.unit_id = u.unit_id
          JOIN courses c ON t.course_id = c.course_id
          JOIN rooms r ON t.room_id = r.room_id
          ORDER BY t.day_of_week, t.start_time";
    $res = $conn->query($q);

    if ($res && $res->num_rows > 0) {
        echo "<table><tr>
                <th>Day</th><th>Time</th><th>Unit</th><th>Course</th><th>Room</th><th>HOD</th><th>Dean</th><th>Overall</th>
              </tr>";
        while ($row = $res->fetch_assoc()) {
            $tid = (int)$row['timetable_id'];

            // default values
            $hodApproved = false;
            $deanApproved = false;

            // 1) If columns exist on timetables table, fetch them
            if ($has_hod_col || $has_dean_col) {
                $cols = [];
                if ($has_hod_col)  $cols[] = 'hod_approved';
                if ($has_dean_col) $cols[] = 'dean_approved';
                $colList = implode(',', $cols);
                $stmt = $conn->prepare("SELECT $colList FROM timetables WHERE timetable_id = ?");
                $stmt->bind_param("i", $tid);
                $stmt->execute();
                $r2 = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($has_hod_col && isset($r2['hod_approved']))  $hodApproved = (bool)$r2['hod_approved'];
                if ($has_dean_col && isset($r2['dean_approved'])) $deanApproved = (bool)$r2['dean_approved'];
            }

            // 2) Else if an approvals table exists, read approvals from there
            elseif ($has_approvals_table) {
                // approvals table expected columns: id, timetable_id, role (hod|dean|timetabler), approved (0/1), approved_at
                $stmt = $conn->prepare("SELECT role FROM approvals WHERE timetable_id = ? AND approved = 1");
                $stmt->bind_param("i", $tid);
                $stmt->execute();
                $r3 = $stmt->get_result();
                while ($a = $r3->fetch_assoc()) {
                    $role = strtolower($a['role']);
                    if ($role === 'hod') $hodApproved = true;
                    if ($role === 'dean') $deanApproved = true;
                }
                $stmt->close();
            }

            // 3) Else: no approval data available in DB — leave as Pending (but you can integrate later)
            // Now compute status labels
            $hodLabel  = $hodApproved  ? "<span class='status hod'>✅ HOD</span>" : "<span class='status pending'>⏳ HOD</span>";
            $deanLabel = $deanApproved ? "<span class='status dean'>✅ Dean</span>" : "<span class='status pending'>⏳ Dean</span>";
            if ($hodApproved && $deanApproved) $overall = "<span class='status final'>✅ Fully Approved</span>";
            else $overall = "<span class='status pending'>⏳ Pending</span>";

            echo "<tr>
                    <td>".htmlspecialchars($row['day_of_week'])."</td>
                    <td>".htmlspecialchars($row['start_time'])." - ".htmlspecialchars($row['end_time'])."</td>
                    <td>".htmlspecialchars($row['unit_name'])."</td>
                    <td>".htmlspecialchars($row['course_name'])."</td>
                    <td>".htmlspecialchars($row['room_name'])."</td>
                    <td>$hodLabel</td>
                    <td>$deanLabel</td>
                    <td>$overall</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No timetable records found.</p>";
    }
    ?>
  </section>

  <!-- Timetable Report (Detailed) -->
  <section id="report" class="section">
    <h2 class="section-title">📊 Detailed Timetable Report</h2>

    <?php
    // Rooms usage: which rooms and how many sessions each
    $roomsReport = $conn->query("
        SELECT r.room_name, r.building_name, COUNT(t.timetable_id) AS sessions
        FROM rooms r
        LEFT JOIN timetables t ON r.room_id = t.room_id
        GROUP BY r.room_id
        ORDER BY sessions DESC, r.room_name
    ");

    // Most used day
    $mostDayQ = $conn->query("SELECT day_of_week, COUNT(*) AS cnt FROM timetables GROUP BY day_of_week ORDER BY cnt DESC LIMIT 1");
    $mostUsedDay = ($mostDayQ && $mostDayQ->num_rows) ? $mostDayQ->fetch_assoc()['day_of_week'] : 'N/A';

    // Lecturer load by sessions
    $lectLoad = $conn->query("
        SELECT l.full_name, COUNT(t.timetable_id) AS sessions
        FROM lecturers l
        LEFT JOIN timetables t ON l.lecturer_id = t.lecturer_id
        GROUP BY l.lecturer_id
        ORDER BY sessions DESC, l.full_name
    ");

    // Basic totals
    $totRooms = $conn->query("SELECT COUNT(*) AS total FROM rooms")->fetch_assoc()['total'] ?? 0;
    $totSessions = $conn->query("SELECT COUNT(*) AS total FROM timetables")->fetch_assoc()['total'] ?? 0;
    $totLecturersAssigned = $conn->query("SELECT COUNT(DISTINCT lecturer_id) AS total FROM timetables")->fetch_assoc()['total'] ?? 0;
    ?>

    <div class="card-grid" style="margin-bottom:18px;">
      <div class="card"><h4>🏫 Total Rooms</h4><p><?php echo (int)$totRooms; ?></p></div>
      <div class="card"><h4>🕒 Total Sessions</h4><p><?php echo (int)$totSessions; ?></p></div>
      <div class="card"><h4>📅 Most Used Day</h4><p><?php echo htmlspecialchars($mostUsedDay); ?></p></div>
      <div class="card"><h4>👨‍🏫 Lecturers Assigned</h4><p><?php echo (int)$totLecturersAssigned; ?></p></div>
    </div>

    <h3>Room usage (rooms with session counts)</h3>
    <?php
    if ($roomsReport && $roomsReport->num_rows) {
        echo "<table><tr><th>Room</th><th>Building</th><th>Sessions</th></tr>";
        while ($r = $roomsReport->fetch_assoc()) {
            echo "<tr><td>".htmlspecialchars($r['room_name'])."</td><td>".htmlspecialchars($r['building_name'])."</td><td>".(int)$r['sessions']."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No room usage data available.</p>";
    }
    ?>

    <h3 style="margin-top:18px;">Lecturer load (number of classes)</h3>
    <?php
    if ($lectLoad && $lectLoad->num_rows) {
        echo "<table><tr><th>Lecturer</th><th>Sessions</th></tr>";
        while ($l = $lectLoad->fetch_assoc()) {
            echo "<tr><td>".htmlspecialchars($l['full_name'])."</td><td>".(int)$l['sessions']."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No lecturer data available.</p>";
    }
    ?>

    <br>
    <form method="POST" action="export_timetable_report.php">
      <button type="submit">📄 Export Report (PDF / Excel)</button>
    </form>
  </section>

</div>

<?php
// handle POST actions for add_room & create_timetable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'add_room') {
        $stmt = $conn->prepare("INSERT INTO rooms (room_name, building_name, capacity) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $_POST['room_name'], $_POST['building_name'], $_POST['capacity']);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Room added'); window.location = window.location.href.split('#')[0] + '#add_room';</script>";
    }
    if ($action === 'create_timetable') {
        // required fields - basic insert (adjust to your schema)
        $stmt = $conn->prepare("INSERT INTO timetables (course_id, unit_id, lecturer_id, room_id, semester_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiissss",
            $_POST['course_id'], $_POST['unit_id'], $_POST['lecturer_id'], $_POST['room_id'],
            $_POST['semester_id'], $_POST['day_of_week'], $_POST['start_time'], $_POST['end_time']
        );
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Timetable created'); window.location = window.location.href.split('#')[0] + '#process_timetable';</script>";
    }
}
?>

</body>
</html>