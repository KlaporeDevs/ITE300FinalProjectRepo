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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Retrieve email and password from the GET parameters
    $email = isset($_GET['email']) ? trim($_GET['email']) : '';
    $password = isset($_GET['password']) ? $_GET['password'] : '';

    // Initialize an array for errors
    $errors = [];

    // Check if the required fields are present
    if (empty($email)) {
        $errors[] = "Email is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }

    // If no errors, proceed to check the credentials
    if (empty($errors)) {
        // Debugging: Log the email being checked
        error_log("Checking email: '$email'");

        // Prepare SQL statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT password FROM players1 WHERE email = :email");
        $stmt->bindParam(':email', $email);

        // Execute the statement
        if (!$stmt->execute()) {
            echo json_encode(["success" => 0, "message" => "SQL Error: " . implode(", ", $stmt->errorInfo())]);
            exit;
        }

        // Fetch the hashed password from the database
        $hashedPassword = $stmt->fetchColumn();

        // Check if the email exists
        if ($hashedPassword) {
            // Verify the password
            if (password_verify($password, $hashedPassword)) {
                // Login successful
                echo json_encode(["success" => 1, "message" => "Login successful"]);
            } else {
                // Password does not match
                echo json_encode(["success" => 0, "message" => "Login failed: Incorrect password"]);
            }
        } else {
            // Email not found
            echo json_encode(["success" => 0, "message" => "Login failed: Email not found"]);
        }
    } else {
        // Return errors as JSON
        echo json_encode(["success" => 0, "errors" => $errors]);
    }
} else {
    // If the method is not GET, return 405 Method Not Allowed
    http_response_code(405);
    echo json_encode(["success" => 0, "message" => "Method not allowed"]);
}
?>