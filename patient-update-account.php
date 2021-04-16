<?php
require_once 'db/db_connection.php';

session_start();

$statementPerson = $conn->prepare('SELECT * FROM person p WHERE p.medicareNumber = :medicare_number');
$statementPerson->bindParam(':medicare_number', $_SESSION["patientMedicareNumber"]);
$statementPerson->execute();
$patient = $statementPerson->fetch(PDO::FETCH_ASSOC);

$statementLivesAt = $conn->prepare('SELECT * FROM livesAt la WHERE la.medicareNumber = :medicare_number');
$statementLivesAt->bindParam(':medicare_number', $_SESSION["patientMedicareNumber"]);
$statementLivesAt->execute();
$livesAt = $statementLivesAt->fetch(PDO::FETCH_ASSOC);

$statementInside = $conn->prepare('SELECT i.postalCode
                                    FROM livesAt la, inside i
                                    WHERE la.medicareNumber = :medicare_number AND la.city = i.city AND la.civicNumber = i.civicNumber AND la.streetName = i.streetName;');
$statementInside->bindParam(':medicare_number', $_SESSION["patientMedicareNumber"]);
$statementInside->execute();
$inside = $statementInside->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Personal information
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $telephoneNumber = $_POST["telephone_number"];
    $citizenship = $_POST["citizenship"];
    $email = $_POST["email_address"];
    $gender = $_POST["gender"];
    $medicareNumber = $_POST["medicare_number"];

    //Address
    $civicNumber = $_POST["civic_number"];
    $streetName = $_POST["street_name"];
    $city = $_POST["city"];
    $oldCivicNumber = $_POST["old_civic_number"];
    $oldStreetName = $_POST["old_street_name"];
    $oldCity = $_POST["old_city"];
    $oldPostalCode = $_POST["old_postal_code"];
    $postalCode = $_POST["postal_code"];

    if (
        empty(trim($firstName))
        || empty(trim($lastName))
        || empty(trim($telephoneNumber))
        || empty(trim($citizenship))
        || empty(trim($email))
        || empty(trim($gender))
        || empty(trim($civicNumber))
        || empty(trim($streetName))
        || empty(trim($city))
        || empty(trim($postalCode))
    ) {
        $error = "error";
        echo $error;
    } else {
        $updatePerson = $conn->prepare("UPDATE person SET telephoneNumber=:telephoneNumber,
                                                            citizenship=:citizenship, 
                                                            firstName=:firstName, 
                                                            gender=:gender, 
                                                            lastName=:lastName, 
                                                            emailAddress=:email
                                                            WHERE medicareNumber=:medicareNumber;");
        $updatePerson->bindParam(":medicareNumber", $medicareNumber);
        $updatePerson->bindParam(":telephoneNumber", $telephoneNumber);
        $updatePerson->bindParam(":citizenship", $citizenship);
        $updatePerson->bindParam(":firstName", $firstName);
        $updatePerson->bindParam(":lastName", $lastName);
        $updatePerson->bindParam(":gender", $gender);
        $updatePerson->bindParam(":email", $email);

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

        if ($updatePerson->execute() && $updatePostalCode->execute() && $updateAddress->execute()) {
            unset($_POST);
            ob_start();
            header("location: https://aec353.encs.concordia.ca/patient-home.php");
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
    <link rel="stylesheet" href="admin-edit-patient.css">
    <link rel="stylesheet" href="admin-HomeButton.css">
    <link rel="stylesheet" href="index.css">
    <title>Edit Patient</title>
</head>

<body>
    <h1> Edit Patient </h1>
    <div class="homeButtonDiv">
        <a href="https://aec353.encs.concordia.ca/patient-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="first_name"> First Name: </label>
        <input type="text" name="first_name" id="first_name" value="<?= $patient['firstName'] ?>">
        <br>
        <label for="last_name"> Last Name: </label>
        <input type="text" name="last_name" id="last_name" value="<?= $patient['lastName'] ?>">
        <br>
        <label for="gender"> Gender:</label>
        <input type="text" name="gender" id="gender" value="<?= $patient['gender'] ?>">
        <br>
        <label for="telephone_number"> Telephone Number: </label>
        <input type="text" name="telephone_number" id="telephone_number" value="<?= $patient['telephoneNumber'] ?>">
        <br>
        <label for="citizenship">Citizenship:</label>
        <input type="text" name="citizenship" id="citizenship" value="<?= $patient['citizenship'] ?>">
        <br>
        <label for="email_address"> Email LivesAt:</label>
        <input type="text" name="email_address" id="email_address" value="<?= $patient['emailAddress'] ?>">
        <br>
        <p> <b> LivesAt: </b> </p>
        <label for="civic_number"> Civic Number:</label>
        <input type="text" name="civic_number" id="civic_number" value="<?= $livesAt['civicNumber'] ?>">
        <br>
        <label for="street_name"> Street Name:</label>
        <input type="text" name="street_name" id="street_name" value="<?= $livesAt['streetName'] ?>">
        <br>
        <label for="city"> City: </label>
        <input type="text" name="city" id="city" value="<?= $livesAt['city'] ?>">
        <br>
        <label for="postal_code"> Postal Code:</label>
        <input type="text" name="postal_code" id="postal_code" value="<?= $inside['postalCode'] ?>">
        <br>
        <input type="button" onClick="document.location.href='https://aec353.encs.concordia.ca/patient-home.php'" value="Cancel" />
        <button type="submit"> Update </button>
        <br>
        <input type="hidden" name="medicare_number" value="<?= $patient['medicareNumber'] ?>">
        <input type="hidden" name="old_civic_number" value="<?= $livesAt['civicNumber'] ?>">
        <input type="hidden" name="old_street_name" value="<?= $livesAt['streetName'] ?>">
        <input type="hidden" name="old_city" value="<?= $livesAt['city'] ?>">
        <input type="hidden" name="old_postal_code" id="old_postal_code" value="<?= $inside['postalCode'] ?>">
    </form>

</body>

</html>