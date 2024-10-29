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

// Function to get input from a DELETE request
parse_str(file_get_contents("php://input"), $delete_vars);

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // Retrieve email and confirmation from the DELETE parameters
    $email = isset($delete_vars['email']) ? trim($delete_vars['email']) : '';
    $confirm = isset($delete_vars['confirm']) ? trim($delete_vars['confirm']) : '';

    // Validate email
    if (empty($email)) {
        echo json_encode(["success" => 0, "message" => "Email is required"]);
        exit;
    }

    // Validate confirmation
    if ($confirm !== 'yes') {
        echo json_encode(["success" => 0, "message" => "Please confirm deletion by setting 'confirm' to 'yes'"]);
        exit;
    }

    // Prepare and execute the delete statement
    $stmt = $pdo->prepare("DELETE FROM players1 WHERE email = :email");
    $stmt->bindParam(':email', $email);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => 1, "message" => "Account deleted successfully. This action is irreversible."]);
        } else {
            echo json_encode(["success" => 0, "message" => "No account found with that email"]);
        }
    } else {
        echo json_encode(["success" => 0, "message" => "Failed to delete account"]);
    }
} else {
    // If the method is not DELETE, return 405 Method Not Allowed
    http_response_code(405);
    echo json_encode(["success" => 0, "message" => "Method not allowed"]);
}
?>