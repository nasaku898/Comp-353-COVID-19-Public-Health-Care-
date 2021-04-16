<?php 
require_once 'db/db_connection.php';

$statementRegion = $conn->prepare('SELECT * FROM region');
$statementRegion->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <link rel="stylesheet" href="admin-HomeButton.css">
    <title>Manage Region</title>
</head>

<body>
    <div class="homeButtonDiv"> 
        <a href="https://aec353.encs.concordia.ca/admin-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
    <h1> Manage Region </h1>
    <br>
    <button onClick="document.location.href='https://aec353.encs.concordia.ca/admin-create-region.php'">Add New Region</button>
    <br>
    <table id="admin-table">
        <thead>
            <tr>
                <th>Region Id</th>
                <th>Region Name</th>
                <th>Current Alert Level</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($rowRegion = $statementRegion->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <tr>
                    <td><?= $rowRegion["regionId"] ?></td>
                    <td><?= $rowRegion["name"] ?></td>
                    <td><?= $rowRegion["alertLevel"] ?></td>
                    <td>
                        <a href="./admin-edit-facility.php?regionId=<?= $rowRegion["regionId"] ?>"> Edit </a>
                        <a href="./admin-delete-facility.php?regionId=<?= $rowRegion["regionId"] ?>"> Delete </a>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</body>
</html>