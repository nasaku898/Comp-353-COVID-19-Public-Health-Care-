<?php
require_once './db/db_connection.php';
require 'admin-HomeButton.php';

// Initialize the session
session_start();

// Define variables and initialize with empty values
$facilityName = "";
$facilityName_err = "";

$facilities = $conn->prepare("SELECT DISTINCT phc.centerName FROM publicHealthCenter phc;");
$facilities->execute();

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if facility name is empty
    if (empty(trim($_POST["facilityName"]))) {
        $facilityName_err = "Please choose a facility.";
    } else {
        $facilityName = trim($_POST["facilityName"]);
    }

    if (!empty($facilityName_err)) {
        $statement = $conn->prepare("SELECT p.*
        FROM person p
        WHERE p.medicareNumber in (
        SELECT phw.medicareNumber
        FROM publicHealthWorker phw, publicHealthCenter phc, worksAt wa
        WHERE phw.medicareNumber = wa.medicareNumber AND wa.centerName = phc.centerName);");
        $statement->execute();
    }

    // Validate
    if (empty($facilityName_err)) {
        $statement = $conn->prepare("SELECT p.*
        FROM person p
        WHERE p.medicareNumber in (
        SELECT phw.medicareNumber
        FROM publicHealthWorker phw, publicHealthCenter phc, worksAt wa
        WHERE phw.medicareNumber = wa.medicareNumber AND wa.centerName = phc.centerName AND phc.centerName =:facilityName);");
        $statement->bindParam(":facilityName", $facilityName);
        $statement->execute();
    }
} else {
    $statement = $conn->prepare("SELECT p.*
    FROM person p
    WHERE p.medicareNumber in (
    SELECT phw.medicareNumber
    FROM publicHealthWorker phw, publicHealthCenter phc, worksAt wa
    WHERE phw.medicareNumber = wa.medicareNumber AND wa.centerName = phc.centerName);");
    $statement->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-worker-facility.css">
    <title>Admin Worker by Facilities</title>
</head>

<body>
    <h1 class="title">Worker by Facilities</h1>
    <?php
    if (!empty($facilityName_err)) {
        echo '<div class="alert">' . $facilityName_err . '</div>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="facilityName">Facilities:</label>
        <select name="facilityName" id="facilityName">
            <?php while ($row = $facilities->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <option value="<?= $row["centerName"] ?>"> <?= $row["centerName"] ?> </option>
            <?php } ?>
        </select> <br /><br />

        <input type="submit" value="Search" />
    </form>

    <table id="workers-facility">
        <thead>
            <tr>
                <th>
                    Medicare Number
                </th>
                <th>
                    Telephone Number
                </th>
                <th>
                    Date Of Birth
                </th>
                <th>
                    Citizenship
                </th>
                <th>
                    First Name
                </th>
                <th>
                    Last Name
                </th>
                <th>
                    Gender
                </th>
                <th>
                    Email
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["medicareNumber"] ?>
                    </td>
                    <td>
                        <?= $row["telephoneNumber"] ?>
                    </td>
                    <td>
                        <?= $row["dateOfBirth"] ?>
                    </td>
                    <td>
                        <?= $row["citizenship"] ?>
                    </td>
                    <td>
                        <?= $row["firstName"] ?>
                    </td>
                    <td>
                        <?= $row["lastName"] ?>
                    </td>
                    <td>
                        <?= $row["gender"] ?>
                    </td>
                    <td>
                        <?= $row["emailAddress"] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>