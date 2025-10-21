<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'timetabler') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $unit_id = $_POST['unit_id'];
    $lecturer_id = $_POST['lecturer_id'];
    $room_id = $_POST['room_id'];
    $semester_id = $_POST['semester_id'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $conn->prepare("INSERT INTO timetables (course_id, unit_id, lecturer_id, room_id, semester_id, day_of_week, start_time, end_time)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiissss", $course_id, $unit_id, $lecturer_id, $room_id, $semester_id, $day, $start_time, $end_time);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Timetable Created Successfully!'); window.location='timetabler_dashboard.php';</script>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create timetable: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Timetable</title>
    <style>
        body { font-family: 'Segoe UI'; margin: 50px; }
        form { background: #fff; padding: 20px; border-radius: 8px; width: 450px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        select, input { width: 100%; padding: 8px; margin-bottom: 10px; }
        button { background: #2e7d32; color: #fff; border: none; padding: 10px; width: 100%; border-radius: 5px; }
        button:hover { background: #1b5e20; }
    </style>
</head>
<body>
<h2>📅 Create New Timetable</h2>

<form method="POST">
    <label>Course:</label>
    <select name="course_id" required>
        <?php
        $courses = $conn->query("SELECT * FROM courses");
        while ($row = $courses->fetch_assoc()) {
            echo "<option value='{$row['course_id']}'>{$row['course_name']}</option>";
        }
        ?>
    </select>

    <label>Unit:</label>
    <select name="unit_id" required>
        <?php
        $units = $conn->query("SELECT * FROM units");
        while ($row = $units->fetch_assoc()) {
            echo "<option value='{$row['unit_id']}'>{$row['unit_name']}</option>";
        }
        ?>
    </select>

    <label>Lecturer:</label>
    <select name="lecturer_id" required>
        <?php
        $lecturers = $conn->query("SELECT * FROM lecturers");
        while ($row = $lecturers->fetch_assoc()) {
            echo "<option value='{$row['lecturer_id']}'>{$row['full_name']}</option>";
        }
        ?>
    </select>

    <label>Room:</label>
    <select name="room_id" required>
        <?php
        $rooms = $conn->query("SELECT * FROM rooms");
        while ($row = $rooms->fetch_assoc()) {
            echo "<option value='{$row['room_id']}'>{$row['room_name']}</option>";
        }
        ?>
    </select>

    <label>Semester:</label>
    <select name="semester_id" required>
        <?php
        $semesters = $conn->query("SELECT * FROM semesters");
        while ($row = $semesters->fetch_assoc()) {
            echo "<option value='{$row['semester_id']}'>{$row['semester_name']}</option>";
        }
        ?>
    </select>

    <label>Day:</label>
    <select name="day" required>
        <option>Monday</option>
        <option>Tuesday</option>
        <option>Wednesday</option>
        <option>Thursday</option>
        <option>Friday</option>
    </select>

    <label>Start Time:</label>
    <input type="time" name="start_time" required>

    <label>End Time:</label>
    <input type="time" name="end_time" required>

    <button type="submit">Create Timetable</button>
</form>
</body>
</html>