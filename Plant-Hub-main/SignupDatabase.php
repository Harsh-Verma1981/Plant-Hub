<?php
// source code for make a secure connection with our database ..
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'PlantHub';

$connect = mysqli_connect($hostname, $username, $password, $database);

if(!$connect){
    die(mysqli_error($connect));
}
// echo "Connection Successful";

?>