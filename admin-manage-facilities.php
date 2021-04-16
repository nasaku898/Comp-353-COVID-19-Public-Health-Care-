<?php 
require_once 'db/db_connection.php';

$statementFacility = $conn->prepare('SELECT DISTINCT phc.centerName, phc.phoneNumber, phc.website, phc.appointmentMethod, phc.installmentType, phc.hasDriveThru, a.city, a.civicNumber, a.streetName, pa.postalCode
                                    FROM publicHealthCenter phc, address a, located l, postalArea pa, inside i
                                    WHERE phc.centerName = l.centerName AND a.city = l.city AND a.civicNumber = l.civicNumber AND a.streetName = l.streetName
                                    AND a.city = i.city AND a.civicNumber = i.civicNumber AND a.streetName = i.streetName AND pa.postalCode = i.postalCode;');
$statementFacility->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <link rel="stylesheet" href="admin-HomeButton.css">
    <title>Manage Facilities</title>
</head>

<body>
    <div class="homeButtonDiv"> 
        <a href="https://aec353.encs.concordia.ca/admin-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
    <h1> Manage Facilities </h1>
    <br>
    <button onClick="document.location.href='https://aec353.encs.concordia.ca/admin-create-facility.php'">Add New Facility</button>
    <br>
    <table id="admin-table">
        <thead>
            <tr>
                <th>Center Name</th>
                <th>Phone Number</th>
                <th>Website</th>
                <th>Appointment Method</th>
                <th>Installment Type</th>
                <th>Has Drive Through (1 = Yes, 0 = No)</th>
                <th>City</th>
                <th>Civic Number</th>
                <th>Street Name</th>
                <th>Postal Code</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($rowFacility = $statementFacility->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <tr>
                    <td><?= $rowFacility["centerName"] ?></td>
                    <td><?= $rowFacility["phoneNumber"] ?></td>
                    <td><?= $rowFacility["website"] ?></td>
                    <td><?= $rowFacility["appointmentMethod"] ?></td>
                    <td><?= $rowFacility["installmentType"] ?></td>
                    <td><?= $rowFacility["hasDriveThru"] ?></td>
                    <td><?= $rowFacility["city"] ?></td>
                    <td><?= $rowFacility["civicNumber"] ?></td>
                    <td><?= $rowFacility["streetName"] ?></td>
                    <td><?= $rowFacility["postalCode"] ?></td>
                    <td>
                        <a href="./admin-edit-facility.php?center_name=<?= $rowFacility["centerName"] ?>"> Edit </a>
                        <a href="./admin-delete-facility.php?center_name=<?= $rowFacility["cennterName"] ?>"> Delete </a>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</body>
</html>