<?php
require_once 'db/db_connection.php';
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["patientLoggedIn"]) && $_SESSION["patientLoggedIn"] === true) {
    ob_start();
    header("location: https://aec353.encs.concordia.ca/patient-home.php");
    ob_end_flush();
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $medicareNumber = $_POST["medicareNumber"];
    $dateOfBirth = $_POST["dateOfBirth"];

    if (empty(trim($medicareNumber)) || empty(trim($dateOfBirth))) {
        echo '<p>Please fill the form completely</p>';
    } else {
        $statement = $conn->prepare('SELECT medicareNumber, dateOfBirth from person where person.medicareNumber = :medicareNumber and person.dateOfBirth = :dateOfBirth');
        $statement->bindParam(":medicareNumber", $medicareNumber);
        $statement->bindParam(":dateOfBirth", $dateOfBirth);
        if ($statement->execute()) {
            if ($statement->rowCount() <= 0) {
                echo '<p>Incorrect credential or account does not exist</p>';
            } else {
                while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                    if (isset($row["medicareNumber"])) {
                        $_SESSION["patientMedicareNumber"] = $row["medicareNumber"];
                        $_SESSION["patientLoggedIn"] = true;
                    }
                }
                ob_start();
                // Redirect user to welcome page
                header("location: https://aec353.encs.concordia.ca/patient-home.php");
                ob_end_flush();
                die();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-login.css">
    <title>Patient Login</title>
</head>

<body>
    <h1 class="title">Patient Login</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="usernameDiv">
            <label for="medicareNumber">Medicare Number</label>
            <input type="text" id="medicareNumber" name="medicareNumber" /> <br /><br />
        </div>
        <div class="passwordDiv">
            <label for="dateOfBirth">Date of Birth</label>
            <input type="date" id="dateOfBirth" name="dateOfBirth" /><br /><br />
        </div>
        <input type="submit" value="Login" />
    </form>
</body>

</html>