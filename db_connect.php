<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "schedulersystem"; // change this if your database name is different

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>