<?php
header("Content-Type: application/json");

// Include the database connection file
include 'database.php';

// Ensure the connection is successful
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all players
        $sql = "SELECT * FROM players";
        $result = $conn->query($sql);

        if (!$result) {
            echo json_encode(["error" => "Error executing query: " . $conn->error]);
            break;
        }

        $players = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $players[] = $row;
            }
        }
        echo json_encode($players);
        break;

    case 'POST':
        // Get the raw POST data
        $data = json_decode(file_get_contents("php://input"), true);

        // Check if all required fields are present
        if (isset($data['players_name']) && isset($data['players_score'])) {
            $players_name = $data['players_name'];
            $players_score = $data['players_score'];

            // Prepare the SQL statement
            $sql = "INSERT INTO players (players_name, players_score) VALUES ('$players_name', '$players_score')";

            // Execute the query and check for success
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["message" => "Player added successfully"]);
            } else {
                echo json_encode(["error" => "Error: " . $conn->error]);
            }
        } else {
            echo json_encode(["error" => "Invalid input: 'players_name' and 'players_score' required"]);
        }
        break;

    default:
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

// Close the connection at the end of the script
$conn->close();
?>
