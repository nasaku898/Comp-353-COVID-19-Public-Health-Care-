<?php

session_start();
if (!isset($_SESSION["patientLoggedIn"]) || $_SESSION["patientLoggedIn"] !== true) {
    ob_start();
    header("location: https://aec353.encs.concordia.ca/patient-login.php");
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
    <title>Patient Home</title>
</head>

<body>
    <h1 class="title">Home</h1>
    <a href="/patient-followUp.php">Follow Up Form</a> <br /><br />
    <a href="/patient-message.php">View All Messages</a> <br /><br />
    <a href="/patient-update-account.php">Update Account</a> <br /><br />
    <a href="/patient-associateChild.php">Associate Child</a> <br /><br />
    <a href="/patient-symptoms-progress.php">View Symptoms Progress</a> <br /><br />
    <a href="https://aec353.encs.concordia.ca/logout.php">Logout</a>
</body>

</html>