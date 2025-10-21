<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];
    $capacity = $_POST['capacity'];
    $building_name = $_POST['building_name'];

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO rooms (room_name, capacity, building_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $room_name, $capacity, $building_name);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Room added successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Room / Lab</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 40px;
        }
        form {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h2 align="center">Add Room / Lab</h2>
    <form method="POST" action="">
        <label>Room Name:</label>
        <input type="text" name="room_name" required>

        <label>Capacity:</label>
        <input type="number" name="capacity" required>

        <label>Building Name:</label>
        <input type="text" name="building_name" required>

        <button type="submit">Add Room</button>
    </form>
</body>
</html>