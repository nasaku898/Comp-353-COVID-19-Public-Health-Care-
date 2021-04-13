<?php require_once './db/db_connection.php';
$statement = $conn->prepare('SELECT * FROM aec353_4.person');
$statement->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head> 
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Covid Health Application</title>
</head>
<body>
    <h1>Hello World</h1>
    <?php while($row = $statement->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_NEXT)){?>
        <h1><?= $row["firstName"] ?></h1>
    <?php } ?>
</body>
</html>