<?php
include 'db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>HOD Dashboard — SmartScheduler</title>
<style>
    body {
        margin: 0;
        font-family: "Poppins", sans-serif;
        background-color: #f1f4f9;
        display: flex;
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
        width: 100%;
    }
    h3 {
        color: #0a58ca;
        border-left: 5px solid #0a58ca;
        padding-left: 10px;
    }
    form {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 30px;
    }
    input, select, button {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 6px;
        border: 1px solid #ccc;
    }
    button {
        background-color: #0a58ca;
        color: white;
        border: none;
        font-weight: bold;
        cursor: pointer;
    }
    button:hover {
        background-color: #084298;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
    }
    th, td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }
    th {
        background: #0a58ca;
        color: white;
    }
    .approve-btn {
        background: #28a745;
        color: white;
        padding: 6px 10px;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
    }
    .approve-btn:hover {
        background: #218838;
    }
    .approved-label {
        color: green;
        font-weight: bold;
    }
</style>
<script>
function approveTimetable(button) {
    button.parentElement.innerHTML = "<span class='approved-label'>✅ Approved</span>";
}
</script>
</head>
<body>

<div class="sidebar">
    <h2>HOD Panel</h2>
    <a href="#students">👩‍🎓 Register Student</a>
    <a href="#lecturers">👨‍🏫 Register Lecturer</a>
    <a href="#departments">🏛 Add Department</a>
    <a href="#courses">📘 Add Course</a>
    <a href="#units">📗 Add Unit</a>
    <a href="#timetable">🕒 View / Approve Timetable</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h1>Welcome, Head of Department</h1>

    <!-- Register Student -->
    <section id="students">
        <h3>👩‍🎓 Register New Student</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_student">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="course_id" required>
                <option value="">Select Course</option>
                <?php
                $courses = $conn->query("SELECT * FROM courses");
                while ($c = $courses->fetch_assoc()) {
                    echo "<option value='{$c['course_id']}'>{$c['course_name']}</option>";
                }
                ?>
            </select>
            <input type="number" name="semester_id" placeholder="Semester ID" required>
            <button type="submit">Register Student</button>
        </form>
    </section>

    <!-- Register Lecturer -->
    <section id="lecturers">
        <h3>👨‍🏫 Register New Lecturer</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_lecturer">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="department_id" required>
                <option value="">Select Department</option>
                <?php
                $departments = $conn->query("SELECT * FROM departments");
                while ($d = $departments->fetch_assoc()) {
                    echo "<option value='{$d['department_id']}'>{$d['department_name']}</option>";
                }
                ?>
            </select>
            <button type="submit">Register Lecturer</button>
        </form>
    </section>

    <!-- Add Department -->
    <section id="departments">
        <h3>🏛 Add New Department</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_department">
            <input type="text" name="department_name" placeholder="Department Name" required>
            <button type="submit">Add Department</button>
        </form>
    </section>

    <!-- Add Course -->
    <section id="courses">
        <h3>📘 Add New Course</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_course">
            <input type="text" name="course_code" placeholder="Course Code" required>
            <input type="text" name="course_name" placeholder="Course Name" required>
            <select name="department_id" required>
                <option value="">Select Department</option>
                <?php
                $departments = $conn->query("SELECT * FROM departments");
                while ($d = $departments->fetch_assoc()) {
                    echo "<option value='{$d['department_id']}'>{$d['department_name']}</option>";
                }
                ?>
            </select>
            <button type="submit">Add Course</button>
        </form>
    </section>

    <!-- Add Unit -->
    <section id="units">
        <h3>📗 Add New Unit</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_unit">
            <input type="text" name="unit_code" placeholder="Unit Code" required>
            <input type="text" name="unit_name" placeholder="Unit Name" required>
            <select name="course_id" required>
                <option value="">Select Course</option>
                <?php
                $courses = $conn->query("SELECT * FROM courses");
                while ($c = $courses->fetch_assoc()) {
                    echo "<option value='{$c['course_id']}'>{$c['course_name']}</option>";
                }
                ?>
            </select>
            <button type="submit">Add Unit</button>
        </form>
    </section>

    <!-- View / Approve Timetable -->
    <section id="timetable">
        <h3>🕒 View / Approve Department Timetable</h3>
        <?php
        $query = "
            SELECT t.timetable_id, t.day_of_week, t.start_time, t.end_time,
                   u.unit_name, c.course_name, r.room_name
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
                        <th>Action</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['day_of_week']}</td>
                        <td>{$row['start_time']} - {$row['end_time']}</td>
                        <td>{$row['unit_name']}</td>
                        <td>{$row['course_name']}</td>
                        <td>{$row['room_name']}</td>
                        <td><button class='approve-btn' onclick='approveTimetable(this)'>Approve</button></td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No timetable records found.</p>";
        }
        ?>
    </section>

</div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add_student') {
        $stmt = $conn->prepare("INSERT INTO students (full_name, email, password, course_id, semester_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $_POST['full_name'], $_POST['email'], $_POST['password'], $_POST['course_id'], $_POST['semester_id']);
        $stmt->execute();
        echo "<script>alert('✅ Student registered successfully!');</script>";
    }

    if ($action == 'add_lecturer') {
        $stmt = $conn->prepare("INSERT INTO lecturers (full_name, email, password, department_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $_POST['full_name'], $_POST['email'], $_POST['password'], $_POST['department_id']);
        $stmt->execute();
        echo "<script>alert('✅ Lecturer registered successfully!');</script>";
    }

    if ($action == 'add_department') {
        $stmt = $conn->prepare("INSERT INTO departments (department_name) VALUES (?)");
        $stmt->bind_param("s", $_POST['department_name']);
        $stmt->execute();
        echo "<script>alert('✅ Department added successfully!');</script>";
    }

    if ($action == 'add_course') {
        $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, department_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $_POST['course_code'], $_POST['course_name'], $_POST['department_id']);
        $stmt->execute();
        echo "<script>alert('✅ Course added successfully!');</script>";
    }

    if ($action == 'add_unit') {
        $stmt = $conn->prepare("INSERT INTO units (unit_code, unit_name, course_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $_POST['unit_code'], $_POST['unit_name'], $_POST['course_id']);
        $stmt->execute();
        echo "<script>alert('✅ Unit added successfully!');</script>";
    }
}
?>