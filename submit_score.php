<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "playersdb");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Get and decode JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    die(json_encode(['success' => false, 'message' => 'Invalid JSON data']));
}

// Validate input
if (!isset($data['username']) || !isset($data['score'])) {
    die(json_encode(['success' => false, 'message' => 'Missing required fields']));
}

$username = $data['username'];
$score = (int)$data['score'];

// First check if user exists
$checkStmt = $conn->prepare("SELECT username FROM players WHERE username = ?");
$checkStmt->bind_param("s", $username);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    // Update existing user's score
    $stmt = $conn->prepare("UPDATE players SET scores = ? WHERE username = ?");
    $stmt->bind_param("is", $score, $username);
} else {
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO players (username, scores) VALUES (?, ?)");
    $stmt->bind_param("si", $username, $score);
}

$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Score updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update score: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>