<?php
// Set error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Log incoming request data
$logFile = "delete_account.log";
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Received request\n", FILE_APPEND);
file_put_contents($logFile, "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Database connection parameters
$host = 'localhost';
$dbname = 'playersdb';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get POST data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $confirm = isset($_POST['confirm']) ? trim($_POST['confirm']) : '';

    // Log received data
    file_put_contents($logFile, "Email: $email\nConfirm: $confirm\n", FILE_APPEND);

    // Validate input
    if (empty($email)) {
        throw new Exception("Email is required");
    }

    if ($confirm !== 'yes') {
        throw new Exception("Please confirm deletion");
    }

    // Prepare and execute the delete statement
    $stmt = $pdo->prepare("DELETE FROM players WHERE email = :email");
    $stmt->bindParam(':email', $email);
    
    if ($stmt->execute()) {
        $rowCount = $stmt->rowCount();
        file_put_contents($logFile, "Rows affected: $rowCount\n", FILE_APPEND);
        
        if ($rowCount > 0) {
            $response = ["success" => 1, "message" => "Account deleted successfully"];
        } else {
            $response = ["success" => 0, "message" => "No account found with that email"];
        }
    } else {
        throw new Exception("Failed to delete account");
    }

} catch (PDOException $e) {
    file_put_contents($logFile, "Database Error: " . $e->getMessage() . "\n", FILE_APPEND);
    $response = ["success" => 0, "message" => "Database error: " . $e->getMessage()];
} catch (Exception $e) {
    file_put_contents($logFile, "General Error: " . $e->getMessage() . "\n", FILE_APPEND);
    $response = ["success" => 0, "message" => $e->getMessage()];
}

// Ensure proper JSON response
echo json_encode($response);
file_put_contents($logFile, "Response: " . json_encode($response) . "\n\n", FILE_APPEND);
?>