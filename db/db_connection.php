<?php
session_start();
$dbServerName = "aec353.encs.concordia.ca";
$dbUsername = "aec353_4";
$dbPassword = "badobrat";
$dbName = "aec353_4";

try{
    $conn = new PDO("mysql:host=$dbServerName;dbname=$dbName;", $dbUsername,$dbPassword);
}catch(PDOException $e){
    die('Connection Failed'. $e->getMessage());
}
?>
