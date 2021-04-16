<?php
require_once './db/db_connection.php';

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
        $statement = $conn->prepare("SELECT m.* FROM message m, notifies n, person p where n.medicareNumber = :medicareNumber and p.medicareNumber = n.medicareNumber and n.messageId = m.messageId");
        $statement->bindParam(":medicareNumber", $_SESSION["patientMedicareNumber"]);
        $statement->execute();
    }

    // Validate
    if (empty($startDate_err) && empty($endDate_err)) {
        // Check if start date greater than end date
        if ($startDate > $endDate) {
            $endDate_err = "Invalid date. 'From' date greater than 'To' date.";
            $statement = $conn->prepare("SELECT m.* FROM message m, notifies n, person p where n.medicareNumber = :medicareNumber and p.medicareNumber = n.medicareNumber and n.messageId = m.messageId");
            $statement->bindParam(":medicareNumber", $_SESSION["patientMedicareNumber"]);
            $statement->execute();
        } else {
            $statement = $conn->prepare("SELECT m.* FROM message m, notifies n, person p WHERE m.date>=:startDate AND m.date<=:endDate and n.medicareNumber = :medicareNumber and p.medicareNumber = n.medicareNumber and n.messageId = m.messageId");
            $statement->bindParam(":startDate", $startDate);
            $statement->bindParam(":endDate", $endDate);
            $statement->bindParam(":medicareNumber", $_SESSION["patientMedicareNumber"]);
            $statement->execute();
        }
    }
} else {
    $statement = $conn->prepare("SELECT m.* FROM message m, notifies n, person p where n.medicareNumber = :medicareNumber and p.medicareNumber = n.medicareNumber and n.messageId = m.messageId");
    $statement->bindParam(":medicareNumber", $_SESSION["patientMedicareNumber"]);
    $statement->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <title>Admin Message</title>
</head>

<body>
    <h1 class="title">Messages</h1>
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

    <table id="admin-table">
        <thead>
            <tr>
                <th>
                    Message Id
                </th>
                <th>
                    Description
                </th>
                <th>
                    Alert Level
                </th>
                <th>
                    Date
                </th>
                <th>
                    Old Alert State
                </th>
                <th>
                    New Alert State
                </th>
                <th>
                    Message Type
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["messageId"] ?>
                    </td>
                    <td>
                        <?= $row["description"] ?>
                    </td>
                    <td>
                        <?= $row["alertLevel"] ?>
                    </td>
                    <td>
                        <?= $row["date"] ?>
                    </td>
                    <td>
                        <?= $row["oldAlertState"] ?>
                    </td>
                    <td>
                        <?= $row["newAlertState"] ?>
                    </td>
                    <td>
                        <?= $row["messageType"] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>