<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'database.php';
    $email = $_POST["email"];
    $new_email = $_POST["new_email"];
    $new_password = $_POST["new_password"];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $query = "SELECT * FROM players WHERE email = '$email'";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        $update_query = "UPDATE players SET email = '$new_email', password = '$hashed_password' WHERE email = '$email'";
        $update_result = $mysqli->query($update_query);
        if ($update_result == true) {
            echo "Account Updated";
        } else {
            echo "Error Updating Account";
        }
    } else {
        echo "Email Not Found";
    }
}
?>

