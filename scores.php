<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "playersdb";

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['scores'])) {
        throw new Exception("Missing required parameters");
    }

    $playerUsername = $data['username'];
    $newScore = intval($data['scores']);
    // Add validation
    if (empty($playerUsername) || $newScore < 0) {
        throw new Exception("Invalid username or score");
    }

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Make sure your table has the correct structure
    $sql = "INSERT INTO players (username, scores) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE 
            scores = GREATEST(scores, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sii", $playerUsername, $newScore, $newScore);
    
    if ($stmt->execute()) {
        echo json_encode(array(
            "success" => true,
            "message" => "Score updated successfully"
        ));
    } else {
        throw new Exception("Error updating score: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    error_log("Score update error: " . $e->getMessage());
    echo json_encode(array(
        "success" => false,
        "message" => $e->getMessage()
    ));
}
?>