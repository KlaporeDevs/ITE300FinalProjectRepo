<?php
header("Content-Type: application/json");

// Database connection parameters
$host = 'localhost';
$dbname = 'playersdb';
$username = 'root';
$password = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => 0, "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the JSON data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Initialize an array for errors
    $errors = [];

    // Check if the required fields are present
    if (!isset($data['username']) || empty($data['username'])) {
        $errors[] = "Username is required";
    }
    if (!isset($data['email']) || empty($data['email'])) {
        $errors[] = "Email is required";
    }
    if (!isset($data['password']) || empty($data['password'])) {
        $errors[] = "Password is required";
    }
    if (!isset($data['confirmPassword']) || empty($data['confirmPassword'])) {
        $errors[] = "Confirm Password is required";
    }

    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }

    // Validate password length
    if (strlen($data['password']) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }

    // Check if passwords match
    if ($data['password'] !== $data['confirmPassword']) {
        $errors[] = "Passwords do not match";
    }

    // Check for duplicate email
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM players1 WHERE email = :email");
        $stmt->bindParam(':email', $data['email']);
        $stmt->execute();
        $emailCount = $stmt->fetchColumn();

        if ($emailCount > 0) {
            $errors[] = "Email is already in use";
        }
    }

    // If no errors, hash the password and insert into the database
    if (empty($errors)) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Prepare SQL statement to prevent SQL injection
        $stmt = $pdo->prepare("INSERT INTO players1 (username, email, password) VALUES (:username, :email, :password)");

        // Bind parameters
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(["success" => 1, "message" => "Player Created Successfully"]);
        } else {
            echo json_encode(["success" => 0, "message" => "Failed to create player"]);
        }
    } else {
        // Return errors as JSON
        echo json_encode(["success" => 0, "errors" => $errors]);
    }
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(["success" => 0, "message" => "Method not allowed"]);
}
?>