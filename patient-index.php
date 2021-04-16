<?php require_once './db/db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Welcome Patient</title>
</head>

<body>
    <h1 class="title">Welcome Patient</h1>
    <div class="button">
        <button type="button" class="specificButton">
            <a href="/patient-register.php">
                Create
            </a>
        </button>

        <button type="button" class="specificButton">
            <a href="/patient-login.php">
                Login
            </a>
        </button>
    </div>
</body>

</html>