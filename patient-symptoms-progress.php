<?php
require_once './db/db_connection.php';

// Initialize the session
session_start();

$startDate = $endDate = "";

if ((!isset($_SESSION["patientLoggedIn"]) || $_SESSION["patientLoggedIn"] !== true)) {
    ob_start();
    header("location:https://aec353.encs.concordia.ca/patient-login.php");
    ob_end_flush();
    die();
}

if (isset($_POST["startDate"]) && isset($_POST["endDate"]) && ($_POST["endDate"] > $_POST["startDate"])) {
    $startDate = $_POST["startDate"] . " 00:00:00";
    $endDate = $_POST["endDate"] . " 23:59:59";
    $select = $conn->prepare("SELECT fu.followUpId, fu.temperature, fu.otherSymptoms, fu.followUpDate, group_concat(concat(' ', s.name)) as symptoms
    FROM follow_up fu, diagnosticFollowUp dfu, symptomsStatus ss, symptoms s
    WHERE dfu.diagnosticId IN (
    SELECT d.diagnosticId
    FROM diagnostic d, receive r, person p
    WHERE p.medicareNumber =:mid and r.medicareNumber = p.medicareNumber and r.diagnosticId = d.diagnosticId)
    and dfu.followUpId = fu.followUpId and followUpDate >=:start and followUpDate <=:end
    AND fu.followUpId = ss.followUpId AND ss.name = s.name
    GROUP BY fu.followUpId, fu.temperature, fu.otherSymptoms, fu.followUpDate;");
    $select->bindParam(':mid', $_SESSION["patientMedicareNumber"]);
    $select->bindParam(':start', $startDate);
    $select->bindParam(':end', $endDate);
    $select->execute();
} else {
    $select = $conn->prepare("SELECT fu.followUpId, fu.temperature, fu.otherSymptoms, fu.followUpDate, group_concat(concat(' ', s.name)) as symptoms
    FROM follow_up fu, diagnosticFollowUp dfu, symptomsStatus ss, symptoms s
    WHERE dfu.diagnosticId IN (
    SELECT d.diagnosticId
    FROM diagnostic d, receive r, person p
    WHERE p.medicareNumber =:mid and r.medicareNumber = p.medicareNumber and r.diagnosticId = d.diagnosticId)
    and dfu.followUpId = fu.followUpId
    AND fu.followUpId = ss.followUpId AND ss.name = s.name
    GROUP BY fu.followUpId, fu.temperature, fu.otherSymptoms, fu.followUpDate;");
    $select->bindParam(':mid', $_SESSION["patientMedicareNumber"]);
    $select->execute();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <link rel="stylesheet" href="admin-HomeButton.css">
    <title>Admin Patient Date</title>
</head>

<body>
    <h1 class="title">My Symptoms by Date</h1>
    <div class="homeButtonDiv">
        <a href="https://aec353.encs.concordia.ca/patient-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="civic">Start Date</label>
        <input type="date" name="startDate" /> <br /><br />
        <label for="civic">End Date</label>
        <input type="date" name="endDate" /> <br /><br />
        <input type="submit" value="Search" />
    </form>

    <h3>MID : <?php echo $_SESSION["patientMedicareNumber"] ?></h3>

    <table id="admin-table">
        <thead>
            <tr>
                <th>
                    Follow Up Id
                </th>
                <th>
                    Temperature
                </th>
                <th>
                    Date
                </th>
                <th>
                    Symptoms
                </th>
                <th>
                    Other Symptoms
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $select->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["followUpId"] ?>
                    </td>
                    <td>
                        <?= $row["temperature"] ?>
                    </td>
                    <td>
                        <?= $row["followUpDate"] ?>
                    </td>
                    <td>
                        <?= $row["symptoms"] ?>
                    </td>
                    <td>
                        <?= $row["otherSymptoms"] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>