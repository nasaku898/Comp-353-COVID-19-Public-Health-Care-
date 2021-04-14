<?php
require_once './db/db_connection.php';
require 'admin-HomeButton.php';

// Initialize the session
session_start();

// Define variables and initialize with empty values
$startDate = $endDate = "";
$startDate_err = $endDate_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if startDate is empty
    if (empty(trim($_POST["from"]))) {
        $startDate_err = "Please enter 'From' date.";
    } else {
        $startDate = trim($_POST["from"]) . " 00:00:00";
    }

    // Check if endDate is empty
    if (empty(trim($_POST["to"]))) {
        $endDate_err = "Please enter 'To' date.";
    } else {
        $endDate = trim($_POST["to"]) . " 23:59:59";
    }

    if (!empty($startDate_err) || !empty($endDate_err)) {
        $statement = $conn->prepare("SELECT p.firstName, p.lastName, p.dateOfBirth, p.telephoneNumber, p.emailAddress, d.result
        FROM diagnostic d, person p, receive r
        WHERE p.medicareNumber = r.medicareNumber AND r.diagnosticId = d.diagnosticId
        ORDER BY d.result;");
        $statement->execute();
    }

    // Validate
    if (empty($startDate_err) && empty($endDate_err)) {
        // Check if start date greater than end date
        if ($startDate > $endDate) {
            $endDate_err = "Invalid date. 'From' date greater than 'To' date.";
            $statement = $conn->prepare("SELECT p.firstName, p.lastName, p.dateOfBirth, p.telephoneNumber, p.emailAddress, d.result
            FROM diagnostic d, person p, receive r
            WHERE p.medicareNumber = r.medicareNumber AND r.diagnosticId = d.diagnosticId
            ORDER BY d.result;");
            $statement->execute();
        } else {
            $statement = $conn->prepare("SELECT p.firstName, p.lastName, p.dateOfBirth, p.telephoneNumber, p.emailAddress, d.result
            FROM diagnostic d, person p, receive r
            WHERE p.medicareNumber = r.medicareNumber AND r.diagnosticId = d.diagnosticId AND d.resultDate >=:startDate AND d.resultDate <=:endDate
            ORDER BY d.result;");
            $statement->bindParam(":startDate", $startDate);
            $statement->bindParam(":endDate", $endDate);
            $statement->execute();
        }
    }
} else {
    $statement = $conn->prepare("SELECT p.firstName, p.lastName, p.dateOfBirth, p.telephoneNumber, p.emailAddress, d.result
    FROM diagnostic d, person p, receive r
    WHERE p.medicareNumber = r.medicareNumber AND r.diagnosticId = d.diagnosticId
    ORDER BY d.result;");
    $statement->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-patients-result.css">
    <title>Admin Patients By Result Date</title>
</head>

<body>
    <h1 class="title">Patients By Result Date</h1>
    <?php
    if (!empty($startDate_err)) {
        echo '<div class="alert">' . $startDate_err . '</div>';
    } else if (!empty($endDate_err)) {
        echo '<div class="alert">' . $endDate_err . '</div>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="from">From:</label>
        <input type="date" id="from" name="from" /> <br /><br />
        <label for="to">To:</label>
        <input type="date" id="to" name="to" /> <br /><br />
        <input type="submit" value="Search" />
    </form>

    <table id="patients-result">
        <thead>
            <tr>
                <th>
                    First Name
                </th>
                <th>
                    Last Name
                </th>
                <th>
                    Date Of Birth
                </th>
                <th>
                    Telephone Number
                </th>
                <th>
                    Email
                </th>
                <th>
                    Result
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["firstName"] ?>
                    </td>
                    <td>
                        <?= $row["lastName"] ?>
                    </td>
                    <td>
                        <?= $row["dateOfBirth"] ?>
                    </td>
                    <td>
                        <?= $row["telephoneNumber"] ?>
                    </td>
                    <td>
                        <?= $row["emailAddress"] ?>
                    </td>
                    <td>
                        <?= $row["result"] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>