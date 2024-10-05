<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputUsername = isset($_POST['players_email']) ? $_POST['players_email'] : '';
    $inputScore = isset($_POST['players_score']) ? $_POST['players_score'] : '';
    $inputName = isset($_POST['players_name']) ? $_POST['players_name'] : '';

    if (!empty($inputUsername) && !empty($inputScore) && !empty($inputName)) {
        $stmt = $conn->prepare("INSERT INTO players_db (players_email, players_score, players_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $inputUsername, $inputScore, $inputName);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Player successfully created"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Error creating the player."
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
        "message" => "Invalid request method. Please use POST."
    ]);
}

$conn->close();
?>
