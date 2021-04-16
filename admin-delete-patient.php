<?php
require_once 'db/db_connection.php';

$statementPatient = $conn->prepare('DELETE FROM person p WHERE p.medicareNumber = :medicare_number');
$statementPatient->bindParam(':medicare_number', $_GET["medicare_number"]);
$statementPatient->execute();
header("Location: ./admin-manage-patients.php");
?>