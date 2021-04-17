<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    ob_start();
    header("location: https://aec353.encs.concordia.ca/admin-login.php");
    ob_end_flush();
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-home.css">
    <title>Admin Home</title>
</head>

<body>
    <h1 class="title">Home</h1>
    <a href="https://aec353.encs.concordia.ca/admin-manage-patients.php">Manage Patients</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-manage-workers.php">Manage Workers</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-manage-facilities.php">Manage Facilities</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-manage-region.php">Manage Region</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-group-zone.php">Manage Group Zone</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-recommendation.php">Manage Health Recommendation</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-message.php">Display All Messages</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-patients-address.php">Search Patients By Address</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-patients-result.php">Search Patients By Result Date</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-worker-facility.php">Search Workers By Facilities</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-workers-positive.php">Search Workers Tested Positive</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-facilities.php">Facilities List</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-regions.php">Regions List</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/admin-region-report.php">Regions Report</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/logout.php">Logout</a> <br /><br />
</body>

</html>