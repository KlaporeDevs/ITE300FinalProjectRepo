<?php
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "players_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert a new player into the players table
$players_name = "John Doe";
$players_score = 150;

$sql = "INSERT INTO players (players_name, players_score) VALUES ('$players_name', '$players_score')";

if ($conn->query($sql) === TRUE) {
    echo "New player added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
