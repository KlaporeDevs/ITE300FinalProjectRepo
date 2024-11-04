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
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT username, scores FROM players ORDER BY scores DESC LIMIT 100";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $leaderboard = array();
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = array(
            "username" => $row["username"],
            "scores" => intval($row["scores"])
        );
    }

    if (empty($leaderboard)) {
        echo json_encode(array());
    } else {
        echo json_encode($leaderboard);
    }

    $result->free_result();
    $conn->close();

} catch (Exception $e) {
    error_log("Leaderboard error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => $e->getMessage()
    ));
}
?>