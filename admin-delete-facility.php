<?php
require_once 'db/db_connection.php';

$statementFacility = $conn->prepare('DELETE FROM publicHealthCenter phc WHERE phc.centerName = :center_name');
$statementFacility->bindParam(':center_name', $_GET["center_name"]);
$statementFacility->execute();
header("Location: ./admin-manage-facilities.php");
?>