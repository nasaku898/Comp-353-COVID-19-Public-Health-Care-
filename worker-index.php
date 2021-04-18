<?php require_once './db/db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Welcome Worker</title>
</head>

<body>
    <h1 class="title">Welcome Worker</h1>
    <div class="button">
        <button type="button" class="specificButton">
            <a href="/worker-registration.php">
                Create
            </a>
        </button>

        <button type="button" class="specificButton">
            <a href="/worker-login.php">
                Login
            </a>
        </button>
    </div>
</body>

</html>