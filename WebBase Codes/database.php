<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "players_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
