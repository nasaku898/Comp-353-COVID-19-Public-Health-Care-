<?php
require_once './db/db_connection.php';
require 'admin-HomeButton.php';

// Initialize the session
session_start();

// Define variables and initialize with empty values
$startDate = $endDate = "";
$startDate_err = $endDate_err = $empty_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if startDate is empty
    if (empty(trim($_POST["from"]))) {
        $startDate_err = "Please enter from date.";
    } else {
        $startDate = trim($_POST["from"]);
    }

    // Check if endDate is empty
    if (empty(trim($_POST["to"]))) {
        $endDate_err = "Please enter to date.";
    } else {
        $endDate = trim($_POST["to"]);
    }

    // Validate
    if (empty($startDate_err) && empty($endDate_err)) {
        $statement = $conn->prepare("SELECT * FROM message m WHERE m.date>:startDate AND m.date<:endDate");
        $statement->bindParam(":startDate", $startDate);
        $statement->bindParam(":endDate", $endDate);
        $statement->execute();
    }
} else {
    $statement = $conn->prepare("SELECT * FROM message m");
    $statement->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-message.css">
    <title>Admin Message</title>
</head>

<body>
    <h1 class="title">Messages</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="from">From:</label>
        <input type="date" id="from" name="from" /> <br /><br />
        <label for="to">To:</label>
        <input type="date" id="to" name="to" /> <br /><br />
        <input type="submit" value="Search" />
    </form>

    <table>
        <thead>
            <tr>
                <td>
                    Message Id
                </td>
                <td>
                    Description
                </td>
                <td>
                    Alert Level
                </td>
                <td>
                    Date
                </td>
                <td>
                    Old Alert State
                </td>
                <td>
                    New Alert State
                </td>
                <td>
                    Message Type
                </td>
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