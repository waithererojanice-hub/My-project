<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Determine query based on selected role
    if (in_array($role, ["admin", "hod", "dean", "timetabler"])) {
        $query = "SELECT * FROM admins WHERE email='$email' AND password='$password' AND role='$role'";
    } elseif ($role == "lecturer") {
        $query = "SELECT * FROM lecturers WHERE email='$email' AND password='$password'";
    } else {
        $query = "SELECT * FROM students WHERE email='$email' AND password='$password'";
    }

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
    session_start(); // ✅ start session

    $user = $result->fetch_assoc();
    $_SESSION['email'] = $user['email'];
    $_SESSION['role']  = $role;

    // ✅ Redirect based on role
    if ($role == "admin") {
        header("Location: admin_dashboard.php");
    } elseif ($role == "hod") {
        header("Location: hod_dashboard.php");
    } elseif ($role == "dean") {
        header("Location: dean_dashboard.php");
    } elseif ($role == "timetabler") {
        header("Location: timetabler_dashboard.php");
    } elseif ($role == "lecturer") {
        header("Location: lecturer_dashboard.php");
    } elseif ($role == "student") {
        header("Location: student_dashboard.php");
    } else {
        echo "<p style='color:red;'>Unknown role detected.</p>";
    }
    exit();
    } else {
        $error = "Invalid email, password, or role. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Sunrise University Smart Scheduler</title>
<style>
    body {
        margin: 0;
        font-family: "Poppins", sans-serif;
        background: linear-gradient(135deg, #0a58ca, #ffb703);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-box {
        background: white;
        width: 400px;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        text-align: center;
    }

    h2 {
        color: #0a58ca;
        margin-bottom: 10px;
    }

    p {
        font-size: 14px;
        color: #555;
        margin-bottom: 25px;
    }

    input, select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    button {
        background-color: #0a58ca;
        color: white;
        border: none;
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }

    button:hover {
        background-color: #084298;
    }

    .error {
        color: red;
        font-weight: bold;
    }

</style>
</head>
<body>

<div class="login-box">
    <h2>Sunrise University</h2>
    <p>Smart Scheduler System Login</p>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Administrator</option>
            <option value="hod">Head of Department</option>
            <option value="dean">Dean</option>
            <option value="timetabler">Timetabler</option>
            <option value="lecturer">Lecturer</option>
            <option value="student">Student</option>
        </select>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>