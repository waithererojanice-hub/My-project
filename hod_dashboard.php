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

    /* Sidebar */
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
        color: #fff;
        letter-spacing: 1px;
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

    /* Main content */
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
        transition: transform 0.2s;
    }

    form:hover {
        transform: scale(1.01);
    }

    input, select, button {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
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

    .header {
        background-color: #084298;
        color: white;
        padding: 15px;
        text-align: right;
        border-bottom: 3px solid #0a58ca;
    }

    .header h1 {
        margin: 0;
        text-align: left;
    }
</style>
</head>
<body>

<div class="sidebar">
    <h2>HOD Panel</h2>
    <a href="#students">👩‍🎓 Register Student</a>
    <a href="#lecturers">👨‍🏫 Register Lecturer</a>
    <a href="#courses">📘 Add Course</a>
    <a href="#units">📗 Add Unit</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Welcome, Head of Department</h1>
    </div>

    <!-- Register Student -->
    <section id="students">
        <h3>👩‍🎓 Register New Student</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_student">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <label>Select Course:</label>
            <select name="course_id" required>
                <option value="">Select Course</option>
                <?php
                $courses = $conn->query("SELECT * FROM courses");
                while ($c = $courses->fetch_assoc()) {
                    echo "<option value='{$c['course_id']}'>{$c['course_name']}</option>";
                }
                ?>
            </select>

            <label>Select Semester:</label>
            <select name="semester_id" required>
                <option value="">Select Semester</option>
                <?php
                $semesters = $conn->query("SELECT * FROM semesters");
                while ($s = $semesters->fetch_assoc()) {
                    echo "<option value='{$s['semester_id']}'>{$s['semester_name']}</option>";
                }
                ?>
            </select>

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

            <label>Select Department:</label>
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

    <!-- Add Course -->
    <section id="courses">
        <h3>📘 Add New Course</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_course">
            <input type="text" name="course_code" placeholder="Course Code" required>
            <input type="text" name="course_name" placeholder="Course Name" required>

            <label>Select Department:</label>
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

            <label>Select Course:</label>
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
</div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_student') {
        $stmt = $conn->prepare("INSERT INTO students (full_name, email, password, course_id, semester_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $_POST['full_name'], $_POST['email'], $_POST['password'], $_POST['course_id'], $_POST['semester_id']);
        $stmt->execute();
        echo "<script>alert('✅ Student registered successfully!');</script>";
    }

    elseif ($action === 'add_lecturer') {
        $stmt = $conn->prepare("INSERT INTO lecturers (full_name, email, password, department_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $_POST['full_name'], $_POST['email'], $_POST['password'], $_POST['department_id']);
        $stmt->execute();
        echo "<script>alert('✅ Lecturer registered successfully!');</script>";
    }

    elseif ($action === 'add_course') {
        $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, department_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $_POST['course_code'], $_POST['course_name'], $_POST['department_id']);
        $stmt->execute();
        echo "<script>alert('✅ Course added successfully!');</script>";
    }

    elseif ($action === 'add_unit') {
        $stmt = $conn->prepare("INSERT INTO units (unit_code, unit_name, course_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $_POST['unit_code'], $_POST['unit_name'], $_POST['course_id']);
        $stmt->execute();
        echo "<script>alert('✅ Unit added successfully!');</script>";
    }
}
?>