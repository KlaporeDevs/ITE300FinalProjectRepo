<?php
if($_SERVER["REQUEST_METHOD"]="POST"){
    require_once 'database.php';
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query="INSERT INTO players (email, password) VALUES ('".$email."', '".$password."')";
    $result =$mysqli->query($query);
    if($result==true){
        echo "Player Created";
    }
    else{
        echo "Error";
    }
}
?>