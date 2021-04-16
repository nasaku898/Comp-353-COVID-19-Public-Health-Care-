<?php
require_once './db/db_connection.php';
require 'admin-HomeButton.php';

// Initialize the session
session_start();

$statement = $conn->prepare("SELECT phc.centerName, phc.installmentType, phc.appointmentMethod, phc.phoneNumber, phc.website, phc.hasDriveThru, a.civicNumber, a.streetName, a.city, pa.postalCode, pa.province, COUNT(DISTINCT phw.medicareNumber) as NumberOfEmployees
FROM publicHealthCenter phc, located l, address a, inside i, postalArea pa, worksAt wa, publicHealthWorker phw
WHERE phw.medicareNumber = wa.medicareNumber AND wa.centerName = phc.centerName AND phc.centerName = l.centerName AND l.city = a.city AND l.streetName = a.streetName
AND l.civicNumber = a.civicNumber AND a.city = i.city AND a.streetName = i.streetName AND a.civicNumber = i.civicNumber AND i.postalCode = pa.postalCode
GROUP BY phc.centerName;");
$statement->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <title>Admin Facilities List</title>
</head>

<body>
    <h1 class="title">Facilities List</h1>
    <table id="admin-table">
        <thead>
            <tr>
                <th>
                    Center Name
                </th>
                <th>
                    Installment Type
                </th>
                <th>
                    Appointment Method
                </th>
                <th>
                    Phone Number
                </th>
                <th>
                    Website
                </th>
                <th>
                    Drive Thru
                </th>
                <th>
                    Address
                </th>
                <th>
                    Number Of Employees
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["centerName"] ?>
                    </td>
                    <td>
                        <?= $row["installmentType"] ?>
                    </td>
                    <td>
                        <?= $row["appointmentMethod"] ?>
                    </td>
                    <td>
                        <?= $row["phoneNumber"] ?>
                    </td>
                    <td>
                        <?= $row["website"] ?>
                    </td>
                    <td>
                        <? 
                            if($row["hasDriveThru"] == 0)
                            {
                                echo 'No';
                            } else{
                                echo 'Yes';
                            }
                        ?>
                    </td>
                    <td>
                        <?= $row["civicNumber"] . " " . $row["streetName"] . ", " . $row["city"] . ", " . $row["postalCode"] . ", " . $row["province"] ?>
                    </td>
                    <td>
                        <?= $row["NumberOfEmployees"] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>