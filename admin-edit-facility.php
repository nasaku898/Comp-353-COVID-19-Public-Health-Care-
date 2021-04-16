<?php
require_once 'db/db_connection.php';

$statementFacility = $conn->prepare('SELECT * FROM publicHealthCenter phc WHERE phc.centerName = :center_name');
$statementFacility->bindParam(':center_name', $_GET["center_name"]);
$statementFacility->execute();
$facility = $statementFacility->fetch(PDO::FETCH_ASSOC);

$statementLocated = $conn->prepare('SELECT * FROM located l WHERE l.centerName = :center_name');
$statementLocated->bindParam(':center_name', $_GET["center_name"]);
$statementLocated->execute();
$located = $statementLocated->fetch(PDO::FETCH_ASSOC);

$statementInside = $conn->prepare('SELECT i.postalCode
                                    FROM located l, inside i
                                    WHERE l.centerName = :center_name AND l.city = i.city AND l.civicNumber = i.civicNumber AND l.streetName = i.streetName;');
$statementInside->bindParam(':center_name', $_GET["center_name"]);
$statementInside->execute();
$inside = $statementInside->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Faciliy information
    $centerName = $_POST["center_name"];
    $phoneNumber = $_POST["phone_number"];
    $website = $_POST["website"];
    $appointmentMethod = $_POST["appointment_method"];
    $installmentType = $_POST["installment_type"];
    $hasDriveThru = $_POST["has_drivethru"];

    //Address
    $civicNumber = $_POST["civic_number"];
    $streetName = $_POST["street_name"];
    $city = $_POST["city"];
    $oldCivicNumber = $_POST["old_civic_number"];
    $oldStreetName = $_POST["old_street_name"];
    $oldCity = $_POST["old_city"];
    $oldPostalCode = $_POST["old_postal_code"];
    $postalCode = $_POST["postal_code"];

    if (empty(trim($centerName)) 
        || empty(trim($phoneNumber)) 
        || empty(trim($website)) 
        || empty(trim($appointmentMethod)) 
        || empty(trim($installmentType)) 
        || empty(trim($civicNumber)) 
        || empty(trim($streetName)) 
        || empty(trim($city))
        || empty(trim($postalCode))) {
        $error = "error";
        echo $error;
    } else {
        $updateFacility = $conn->prepare("UPDATE IGNORE publicHealthCenter SET
                                                            phoneNumber=:phoneNumber, 
                                                            website=:website, 
                                                            appointmentMethod=:appointmentMethod, 
                                                            installmentType=:installmentType, 
                                                            hasDriveThru=:hasDriveThru
                                                            WHERE centerName=:centerName;");
        $updateFacility->bindParam(":centerName", $centerName);
        $updateFacility->bindParam(":phoneNumber", $phoneNumber); 
        $updateFacility->bindParam(":website", $website);
        $updateFacility->bindParam(":appointmentMethod", $appointmentMethod);
        $updateFacility->bindParam(":installmentType", $installmentType);
        $updateFacility->bindParam(":hasDriveThru", $hasDriveThru);

        $updateAddress = $conn->prepare("UPDATE IGNORE address SET
                                        streetName=:streetName,
                                        civicNumber=:civicNumber, 
                                        city=:city
                                        WHERE streetName=:oldStreetName AND civicNumber=:oldCivicNumber AND city=:oldCity;");
        $updateAddress->bindParam(":streetName", $streetName); 
        $updateAddress->bindParam(":civicNumber", $civicNumber);
        $updateAddress->bindParam(":city", $city);
        $updateAddress->bindParam(":oldStreetName", $oldStreetName); 
        $updateAddress->bindParam(":oldCivicNumber", $oldCivicNumber);
        $updateAddress->bindParam(":oldCity", $oldCity);

        $updatePostalCode = $conn->prepare("UPDATE IGNORE postalArea SET
                                            postalCode = :postalCode
                                            WHERE postalCode = :oldPostalCode;");
        $updatePostalCode->bindParam(":postalCode", $postalCode); 
        $updatePostalCode->bindParam(":oldPostalCode", $oldPostalCode);

        if ($updateFacility->execute() &&  $updateAddress->execute() && $updatePostalCode->execute()) {
            unset($_POST);
            ob_start();
            header("location: https://aec353.encs.concordia.ca/admin-manage-facilities.php");
            ob_end_flush();
            die();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-edit-facility.css">
    <title>Edit Facility</title>
</head>

<body>
    <h1> Edit Facility </h1>
    <form action="./admin-edit-facility.php" method="post">
        <p> <b> Center Name: <?= $facility['centerName'] ?> </b></p>
        <br>
        <label for="phone_number"> Phone Number: </label>
        <input type="text" name="phone_number" id="phone_number" value="<?= $facility['phoneNumber'] ?>">
        <br>
        <label for="website"> Website: </label>
        <input type="text" name="website" id="website" value="<?= $facility['website'] ?>">
        <br>
        Appointment Method (Previous: <?= $facility['appointmentMethod'] ?>):
        <select name="appointment_method">
            <option value="Walk-In">Walk-In</option>
            <option value="Appointment-Only">Appointment Only</option>
            <option value="Both">Both</option>
        </select>
        <br>
        <label for="installment_type"> Installment Type:</label>
        <input type="text" name="installment_type" id="installment_type" value="<?= $facility['installmentType'] ?>">
        <br>
        <label> Has Drive Through (Previous: <?= $facility['hasDriveThru'] ?>) </label>
        <select name="has_drivethru">
            <option value=1>Yes (1)</option>
            <option value=0>No (0)</option>
        </select>
        <br>
        <label for="city"> City:</label>
        <input type="text" name="city" id="city" value="<?= $located['city'] ?>">
        <br>
        <label for="civic_number"> Civic Number: </label>
        <input type="text" name="civic_number" id="civic_number" value="<?= $located['civicNumber'] ?>">
        <br>
        <label for="street_name"> Street Name: </label>
        <input type="text" name="street_name" id="street_name" value="<?= $located['streetName'] ?>">
        <br>
        <label for="postal_code"> Postal Code:</label>
        <input type="text" name="postal_code" id="postal_code" value="<?= $inside['postalCode'] ?>">
        <br>
        <input type="button" onClick="document.location.href='https://aec353.encs.concordia.ca/admin-manage-facilities.php'" value="Cancel" />
        <button type="submit"> Update </button>
        <br>
        <input type="hidden" name="center_name" value="<?= $facility['centerName'] ?>">
        <input type="hidden" name="old_civic_number" value="<?= $located['civicNumber'] ?>">
        <input type="hidden" name="old_street_name" value="<?= $located['streetName'] ?>">
        <input type="hidden" name="old_city" value="<?= $located['city'] ?>">
        <input type="hidden" name="old_postal_code" id="old_postal_code" value="<?= $inside['postalCode'] ?>">
    </form>

</body>

</html>