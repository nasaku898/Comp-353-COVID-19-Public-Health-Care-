<?php
require_once './db/db_connection.php';
require 'admin-HomeButton.php';

// Initialize the session
session_start();

$statement = $conn->prepare("SELECT DISTINCT r.name, a.city, pa.postalCode
FROM region r, address a, postalArea pa, situatedIn si, inside i
WHERE a.city = si.city AND a.civicNumber = si.civicNumber AND a.streetName = si.streetName AND r.regionId = si.regionId and i.postalCode = pa.postalCode AND i.city = a.city AND i.civicNumber = a.civicNumber AND i.streetName = a.streetName
ORDER BY r.name, a.city;");
$statement->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <title>Admin Regions List</title>
</head>

<body>
    <h1 class="title">Regions List</h1>
    <table id="admin-table">
        <thead>
            <tr>
                <th>
                    Region Name
                </th>
                <th>
                    City Name
                </th>
                <th>
                    Postal Code
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["name"] ?>
                    </td>
                    <td>
                        <?= $row["city"] ?>
                    </td>
                    <td>
                        <?= $row["postalCode"] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>