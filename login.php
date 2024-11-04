<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
require_once 'database.php';

// Database connection parameters
$servername = "localhost";
$username = "root";  // your database username
$password = "";      // your database password
$dbname = "playersdb";

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to send JSON response
function send_json_response($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit();
}

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Get and decode JSON input
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);

    // Check if JSON parsing was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON format');
    }

    // Validate input
    if (!isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Email and password are required');
    }

    // Sanitize input
    $email = sanitize_input($data['email']);
    $password = $data['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM players WHERE email = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception('Prepare statement failed: ' . $conn->error);
    }

    $stmt->bind_param("s", $email);
    
    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }

    // Get result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Send success response
            send_json_response(true, 'Login successful', [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'scores' => $user['scores']
            ]);
        } else {
            throw new Exception('Invalid password');
        }
    } else {
        throw new Exception('Username And Password Are Incorrect');
    }

} catch (Exception $e) {
    send_json_response(false, $e->getMessage());
} finally {
    // Close database connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>