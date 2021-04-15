<?php
session_start();

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
    <link rel="stylesheet" href="admin-HomeButton.css">
</head>

<body>
    <div class="homeButtonDiv">
        <a href="https://aec353.encs.concordia.ca/admin-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
</body>

</html>