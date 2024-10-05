<?php
include 'database.php';

parse_str(file_get_contents("php://input"), $_DELETE);

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $inputUsername = isset($_DELETE['players_email']) ? $_DELETE['players_email'] : '';
    $inputName = isset($_DELETE['players_name']) ? $_DELETE['players_name'] : '';

    if (!empty($inputUsername) && !empty($inputName)) {
        $stmt = $conn->prepare("DELETE FROM players_db WHERE players_email = ? AND players_name = ?");
        $stmt->bind_param("ss", $inputUsername, $inputName);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Player successfully deleted"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Player not found or already deleted"
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Error deleting the player."
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Please provide both players_email and players_name."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Please use DELETE."
    ]);
}
$conn->close();
