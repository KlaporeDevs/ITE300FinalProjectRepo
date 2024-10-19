<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'database.php';
    $email = $_POST["email"];
    $password = $_POST["password"];
    $query = "SELECT * FROM players WHERE email = '$email'";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $delete_query = "DELETE FROM players WHERE email = '$email'";
            $delete_result = $mysqli->query($delete_query);
            if ($delete_result == true) {
                echo "Account Deleted";
            } else {
                echo "Error Deleting Account";
            }
        } else {
            echo "Invalid Password";
        }
    } else {
        echo "Email Not Found";
    }
}
?>

