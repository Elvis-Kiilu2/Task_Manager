<?php
$host = "localhost";
$username = "root";
$password = ""; // XAMPP default has no password
$database = "task_manager";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
