<?php
session_start();

if (!isset($_SESSION["workerLoggedIn"]) || $_SESSION["workerLoggedIn"] !== true) {
    ob_start();
    header("location: https://aec353.encs.concordia.ca/worker-login.php");
    ob_end_flush();
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-HomeButton.css">
</head>

<body>
    <div class="homeButtonDiv">
        <a href="https://aec353.encs.concordia.ca/worker-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
</body>

</html>