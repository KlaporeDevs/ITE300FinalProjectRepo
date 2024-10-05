<?php
include 'database.php';

parse_str(file_get_contents("php://input"), $_PUT);

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $inputUsername = isset($_PUT['players_email']) ? $_PUT['players_email'] : '';
    $inputScore = isset($_PUT['players_score']) ? $_PUT['players_score'] : '';
    $inputName = isset($_PUT['players_name']) ? $_PUT['players_name'] : '';

    if (!empty($inputUsername) && !empty($inputScore) && !empty($inputName)) {
        $stmt = $conn->prepare("UPDATE players_db SET players_score = ? WHERE players_email = ? AND players_name = ?");
        $stmt->bind_param("iss", $inputScore, $inputUsername, $inputName);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Player score updated successfully"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Player not found or score already up to date"
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Error updating the player."
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Please provide players_email, players_score, and players_name."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Please use PUT."
    ]);
}

$conn->close();
?>
