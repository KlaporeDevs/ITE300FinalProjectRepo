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

// Function to get input from a PUT request
parse_str(file_get_contents("php://input"), $put_vars);

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Retrieve email, username, and password from the PUT parameters
    $email = isset($put_vars['email']) ? trim($put_vars['email']) : '';
    $username = isset($put_vars['username']) ? trim($put_vars['username']) : '';
    $password = isset($put_vars['password']) ? $put_vars['password'] : '';

    // Initialize an array for errors
    $errors = [];

    // Validate input fields
    if (empty($email)) {
        $errors[] = "Email is required";
    }
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }

    // If no errors, proceed to update the credentials
    if (empty($errors)) {
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the update statement
        $stmt = $pdo->prepare("UPDATE players1 SET username = :username, password = :password WHERE email = :email");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            echo json_encode(["success" => 1, "message" => "Account updated successfully"]);
        } else {
            echo json_encode(["success" => 0, "message" => "Failed to update account: " . implode(", ", $stmt->errorInfo())]);
        }
    } else {
        // Return errors as JSON
        echo json_encode(["success" => 0, "errors" => $errors]);
    }
} else {
    // If the method is not PUT, return 405 Method Not Allowed
    http_response_code(405);
    echo json_encode(["success" => 0, "message" => "Method not allowed"]);
}
?>