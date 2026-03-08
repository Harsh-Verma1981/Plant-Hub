<?php
// source code for make a secure connection with our database ..
require "config.php";

$hostname = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$database = $_ENV['DB_NAME'];

$connect = mysqli_connect($hostname, $username, $password, $database);

if(!$connect){
    die(mysqli_error($connect));
}
// echo "Connection Successful";

?>