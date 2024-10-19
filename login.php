<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
    require_once 'database.php';
    $email = $_POST["email"];
    $password = $_POST["password"];
    $query = "SELECT * FROM players WHERE email= '$email'";
    $result = $mysqli->query($query);
    if($results->num_rows > 0){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            echo "Login Successful";
        }
        else{
            echo "Invalid Password";
        }
    }
    else{
        echo "Invalid Email";
    }
}
?>