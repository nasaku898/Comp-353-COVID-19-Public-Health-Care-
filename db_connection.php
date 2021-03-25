<?php

$dbServerName = "aec353.encs.concordia.ca";
$dbUsername = "aec353_4";
$dbPassword = "badobrat";
$dbName = "aec353_4";

// $dbServerName = "localhost";
// $dbUsername = "root";
// $dbPassword = "";
// $dbName = "con";

$conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
