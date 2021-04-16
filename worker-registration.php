<?php
require_once 'db/db_connection.php';

$statement = $conn->prepare('SELECT distinct name, regionId FROM region');
$statement->execute();

$centerName = $conn->prepare('SELECT distinct centerName FROM publicHealthCenter');
$centerName->execute();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Personal information
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $medicareNumber = $_POST["medicareNumber"];
    $telephoneNumber = $_POST["telephoneNumber"];
    $dateOfBirth = $_POST["dateOfBirth"];
    $citizenship = $_POST["citizenship"];
    $email = $_POST["email"];
    $gender = $_POST["gender"];

    //Address
    $civicNumber = $_POST["civicNumber"];
    $streetName = $_POST["streetName"];
    $city = $_POST["city"];
    $postalCode = $_POST["postalCode"];
    $province = $_POST["province"];
    $region = $_POST["region"];

    $workplace = $_POST["workplace"];

    if (empty(trim($firstName)) || empty(trim($lastName)) || empty(trim($medicareNumber)) || empty(trim($telephoneNumber)) || empty(trim($dateOfBirth)) || empty(trim($citizenship)) || empty(trim($email)) || empty(trim($gender)) || empty(trim($civicNumber)) || empty(trim($streetName)) || empty(trim($city)) || empty(trim($postalCode)) || empty(trim($province)) || empty(trim($workplace))) {
        $error = "error";
        echo $error;
    } else {
        $statement = $conn->prepare("INSERT INTO person(medicareNumber,telephoneNumber,dateOfBirth,citizenship, firstName, gender, lastName, emailAddress) VALUES (:medicareNumber,:telephoneNumber,:dateOfBirth,:citizenship,:firstName,:gender,:lastName, :email);");
        $statement->bindParam(":medicareNumber", $medicareNumber);
        $statement->bindParam(":telephoneNumber", $telephoneNumber);
        $statement->bindParam(":dateOfBirth", $dateOfBirth);
        $statement->bindParam(":citizenship", $citizenship);
        $statement->bindParam(":firstName", $firstName);
        $statement->bindParam(":lastName", $lastName);
        $statement->bindParam(":gender", $gender);
        $statement->bindParam(":email", $email);

        $createAddress = $conn->prepare("INSERT IGNORE INTO  address(city,streetName,civicNumber) VALUES(:city, :streetName,:civicNumber);");
        $createAddress->bindParam(":city", $city);
        $createAddress->bindParam(":streetName", $streetName);
        $createAddress->bindParam(":civicNumber", $civicNumber);

        $createPostalArea = $conn->prepare("INSERT IGNORE INTO  postalArea(postalCode, province) VALUES(:postalCode,:province)");
        $createPostalArea->bindParam(":postalCode", $postalCode);
        $createPostalArea->bindParam(":province", $province);

        $createLivesAt = $conn->prepare("INSERT INTO livesAt(medicareNumber,streetName, civicNumber,city) VALUES(:medicareNumber,:streetName,:civicNumber,:city)");
        $createLivesAt->bindParam(":medicareNumber", $medicareNumber);
        $createLivesAt->bindParam(":streetName", $streetName);
        $createLivesAt->bindParam(":civicNumber", $civicNumber);
        $createLivesAt->bindParam(":city", $city);

        $createSituatedIn = $conn->prepare("INSERT IGNORE INTO  situatedIn(regionId,streetName, civicNumber,city) VALUES(:regionId,:streetName,:civicNumber,:city)");
        $createSituatedIn->bindParam(":regionId", $region);
        $createSituatedIn->bindParam(":streetName", $streetName);
        $createSituatedIn->bindParam(":civicNumber", $civicNumber);
        $createSituatedIn->bindParam(":city", $city);

        $createInside = $conn->prepare("INSERT IGNORE INTO  inside(postalCode,streetName, civicNumber,city) VALUES(:postalCode, :streetName,:civicNumber,:city)");
        $createInside->bindParam(":postalCode", $postalCode);
        $createInside->bindParam(":streetName", $streetName);
        $createInside->bindParam(":civicNumber", $civicNumber);
        $createInside->bindParam(":city", $city);

        $createHealthWorker = $conn->prepare("INSERT INTO publicHealthWorker(medicareNumber) VALUES(:medicareNumber)");
        $createHealthWorker->bindParam(":medicareNumber", $medicareNumber);

        $createWorksAt = $conn->prepare("INSERT INTO worksAt(medicareNumber,centerName) VALUES(:medicareNumber, :centerName)");
        $createWorksAt->bindParam(":medicareNumber", $medicareNumber);
        $createWorksAt->bindParam(":centerName", $workplace);

        if ($statement->execute() &&  $createAddress->execute() && $createPostalArea->execute()) {
            if ($createLivesAt->execute() && $createSituatedIn->execute() && $createInside->execute() && $createHealthWorker->execute() && $createWorksAt->execute()) {
                unset($_POST);
                ob_start();
                header("location: https://aec353.encs.concordia.ca/admin-home.php");
                ob_end_flush();
                die();
            }
        }
    }
    unset($_POST);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="patient-registration.css">
    <title>Health Worker Register</title>
</head>

<body>
    <h1 class="title">Health Worker Register</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName">
        <br />
        <label for="lastName">Last Name</label>
        <input type="text" id="lastName" name="lastName">
        <br />
        <label for="medicareNumber">Medicare Number</label>
        <input type="text" id="medicareNumber" name="medicareNumber">
        <br />
        <label for="telephoneNumber">Telephone Number</label>
        <input type="number" id="telephoneNumber" name="telephoneNumber">
        <br />
        <label for="dateOfBirth">Date of Birth</label>
        <input type="date" id="dateOfBirth" name="dateOfBirth">
        <br />
        <label for="citizenship">Citizenship</label>
        <input type="text" id="citizenship" name="citizenship">
        <br />
        <label for="email">Email</label>
        <input type="email" id="email" name="email">
        <br />
        <label for="gender">Gender</label>
        <select name="gender" id="gender">
            <option value="M">Male</option>
            <option value="F">Female</option>
        </select>
        <br />

        <h3>Your address information</h3>
        <label for="civicNumber">Civic Number</label>
        <input type="number" id="civicNumber" name="civicNumber">
        <br />
        <label for="streetName">Street Name</label>
        <input type="text" id="streetName" name="streetName">
        <br />
        <label for="city">City</label>
        <input type="text" id="city" name="city">
        <br />
        <label for="postalCode">Postal Code</label>
        <input type="text" id="postalCode" name="postalCode">
        <br />
        <label for="province">Province</label>
        <input type="text" id="province" name="province">
        <br />
        <select name="region" id="region">
            <?php while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <option value="<?= $row["regionId"] ?>"> <?= $row["name"] ?> </option>
            <?php } ?>
        </select>

        <h3>Health Workplace</h3>
        <label>Select your work location</label>
        <select name="workplace" id="workplace">
            <?php while ($row = $centerName->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <option value="<?= $row["centerName"] ?>"> <?= $row["centerName"] ?> </option>
            <?php } ?>
        </select>
        <br />
        <input type="submit" value="Create" />
    </form>
</body>

</html>