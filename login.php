<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $inputUsername = isset($_GET['players_email']) ? $_GET['players_email'] : '';
    $inputName = isset($_GET['players_name']) ? $_GET['players_name'] : '';

    if (!empty($inputUsername) && !empty($inputName)) {
        $stmt = $conn->prepare("SELECT players_id, players_score FROM players_db WHERE players_email = ? AND players_name = ?");
        $stmt->bind_param("ss", $inputUsername, $inputName);

        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($playersId, $playersScore);
            $stmt->fetch();

            echo json_encode([
                "status" => "success",
                "data" => [
                    "players_id" => $playersId,
                    "players_score" => $playersScore
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Player not found"
            ]);
        }
        $stmt->close();
        echo json_encode([
            "status" => "error",
            "message" => "Please provide both players_email and players_name."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Please use GET."
    ]);
}

$conn->close();
?>
