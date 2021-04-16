<?php 
require_once 'db/db_connection.php';

$statementPerson = $conn->prepare('SELECT p.medicareNumber, p.telephoneNumber, p.dateOfBirth, p.citizenship, p.firstName, p.lastName, p.gender, p.emailAddress, a.city, a.civicNumber, a.streetName, pa.postalCode, phc.centerName
                                    FROM publicHealthWorker phw, worksAt wa, publicHealthCenter phc, person p, address a, livesAt la, postalArea pa, inside i
                                    WHERE p.medicareNumber = la.medicareNumber AND a.city = la.city AND a.civicNumber = la.civicNumber AND a.streetName = la.streetName
                                    AND a.city = i.city AND a.civicNumber = i.civicNumber AND a.streetName = i.streetName AND pa.postalCode = i.postalCode
                                    AND p.medicareNumber = phw.medicareNumber 
                                    AND phw.medicareNumber = wa.medicareNumber AND phc.centerName = wa.centerName;');
$statementPerson->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <link rel="stylesheet" href="admin-HomeButton.css">
    <title>Manage Health Workers</title>
</head>

<body>
    <div class="homeButtonDiv"> 
        <a href="https://aec353.encs.concordia.ca/admin-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
    <h1> Manage Health Workers </h1>
    <br>
    <button onClick="document.location.href='https://aec353.encs.concordia.ca/worker-registration.php'">Add New Health Worker</button>
    <br>
    <table id="admin-table">
        <thead>
            <tr>
                <th>Medicare Number</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Telephone Number</th>
                <th>Citizenship</th>
                <th>Email Address</th>
                <th>City</th>
                <th>Civic Number</th>
                <th>Street Name</th>
                <th>Postal Code</th>
                <th>Center Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($rowPerson = $statementPerson->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <tr>
                    <td><?= $rowPerson["medicareNumber"] ?></td>
                    <td><?= $rowPerson["firstName"] ?></td>
                    <td><?= $rowPerson["lastName"] ?></td>
                    <td><?= $rowPerson["dateOfBirth"] ?></td>
                    <td><?= $rowPerson["gender"] ?></td>
                    <td><?= $rowPerson["telephoneNumber"] ?></td>
                    <td><?= $rowPerson["citizenship"] ?></td>
                    <td><?= $rowPerson["emailAddress"] ?></td>
                    <td><?= $rowPerson["city"] ?></td>
                    <td><?= $rowPerson["civicNumber"] ?></td>
                    <td><?= $rowPerson["streetName"] ?></td>
                    <td><?= $rowPerson["postalCode"] ?></td>
                    <td><?= $rowPerson["centerName"] ?></td>
                    <td>
                        <a href="./admin-edit-worker.php?medicare_number=<?= $rowPerson["medicareNumber"] ?>"> Edit </a>
                        <a href="./admin-delete-worker.php?medicare_number=<?= $rowPerson["medicareNumber"] ?>"> Delete </a>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</body>
</html>