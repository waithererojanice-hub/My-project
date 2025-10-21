<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sunrise University Smart Scheduler System</title>
<style>
    body {
        margin: 0;
        font-family: "Poppins", sans-serif;
        background: linear-gradient(135deg, #0a58ca, #ffb703);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
    }

    .container {
        text-align: center;
        background: rgba(255, 255, 255, 0.15);
        padding: 50px 60px;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        backdrop-filter: blur(6px);
        width: 450px;
    }

    h1 {
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    p {
        font-size: 15px;
        color: #fff;
        margin-bottom: 25px;
    }

    .btn {
        background-color: #fff;
        color: #0a58ca;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }

    .btn:hover {
        background-color: #ffb703;
        color: white;
        transform: scale(1.05);
    }
</style>
</head>
<body>
<div class="container">
    <h1>Sunrise University</h1>
    <p>Welcome to the Smart Scheduler System</p>
    <a href="login.php" class="btn">Login to Continue</a>
</div>
</body>
</html>