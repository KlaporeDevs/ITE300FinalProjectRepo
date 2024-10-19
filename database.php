<?php
$mysqli=new mysqli("localhost", "root", "", "playersdb");
if($mysqli->connect_error){
    die("Connection failed: ");
}
else{
    die("Connected");
}
?>