<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$host = 'localhost';
$dbname = 'playersdb';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo json_encode(["success" => 1, "message" => "Welcome to Glutton"]);
} catch (PDOException $e) {
    echo json_encode(["success" => 0, "message" => "Failed To Connect The Server" . $e->getMessage()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        echo json_encode(["success" => 0, "message" => "Invalid JSON"]);
        exit;
    }

    $errors = [];

    // Validate inputs
    if (empty($data['username'])) {
        $errors[] = "Username is required";
    }
    if (empty($data['email'])) {
        $errors[] = "Email is required";
    }
    if (empty($data['password'])) {
        $errors[] = "Password is required";
    }
    if (empty($data['confirmPassword'])) {
        $errors[] = "Confirm Password is required";
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }

    if (strlen($data['password']) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }

    if ($data['password'] !== $data['confirmPassword']) {
        $errors[] = "Passwords do not match";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM players WHERE email = :email");
        $stmt->bindParam(':email', $data['email']);
        $stmt->execute();
        $emailCount = $stmt->fetchColumn();

        if ($emailCount > 0) {
            $errors[] = "Email is already in use";
        }
    }

    // If no errors, proceed to insert the player
    if (empty($errors)) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO players (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);

        // Check if the insert was successful
        try {
            if ($stmt->execute()) {
                echo json_encode(["success" => 1, "message" => "Player Created Successfully"]);
            } else {
                echo json_encode(["success" => 0, "message" => "Failed to create player"]);
            }
        } catch (PDOException $e) {
            // Log the error message for debugging
            error_log("SQL Error: " . $e->getMessage());
            echo json_encode(["success" => 0, "message" => "Error executing query: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => 0, "errors" => $errors]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => 0, "message" => "Method not allowed"]);
}
?>