<?php
require_once 'db/db_connection.php';

$statementRegion = $conn->prepare('DELETE FROM region r WHERE r.regionId = :regionId');
$statementRegion->bindParam(':regionId', $_GET["regionId"]);
$statementRegion->execute();
header("Location: ./admin-manage-region.php");

?>