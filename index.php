<?php require_once './db/db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Welcome</title>
</head>

<body>
    <h1 class="title">Welcome</h1>
    <div class="button">
        <a href="https://aec353.encs.concordia.ca/patient-index.php">
            <button class="specificButton">Patient</button>
        </a>
        <a href="https://aec353.encs.concordia.ca/worker-index.php">
            <button  class="specificButton">Worker</button>
        </a>
        <a href="https://aec353.encs.concordia.ca/admin-login.php">
            <button class="specificButton">Admin</button>
        </a>
    </div>
</body>

</html>