<?php
require_once './db/db_connection.php';
require 'admin-HomeButton.php';

// Initialize the session
session_start();

// Define variables and initialize with empty values
$startDate = $endDate = $facilityName = "";
$startDate_err = $endDate_err = $facilityName_err = "";

$facilities = $conn->prepare("SELECT DISTINCT phc.centerName FROM publicHealthCenter phc;");
$facilities->execute();

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

    // Check if facility name is empty
    if (empty(trim($_POST["facilityName"]))) {
        $facilityName_err = "Please choose a facility.";
    } else {
        $facilityName = trim($_POST["facilityName"]);
    }

    // Validate
    if (empty($startDate_err) && empty($endDate_err) && empty($facilityName_err)) {
        // Check if start date greater than end date
        if ($startDate > $endDate) {
            $endDate_err = "Invalid date. 'From' date greater than 'To' date.";
        } else {
            $statement = $conn->prepare("SELECT p.*
            FROM person p
            WHERE p.medicareNumber IN (
                SELECT wa.medicareNumber
                FROM worksAt wa, publicHealthCenter phc
                WHERE wa.centerName = phc.centerName AND phc.centerName =:facilityName AND wa.medicareNumber IN (
                    SELECT r.medicareNumber
                    FROM receive r, diagnostic d
                    WHERE r.diagnosticId = d.diagnosticId AND d.result = 'Positive' AND d.resultDate >=:startDate AND d.resultDate <=:endDate AND r.medicareNumber IN (
                        SELECT p.medicareNumber
                        FROM person p, publicHealthWorker phw
                        WHERE p.medicareNumber = phw.medicareNumber
                    )
                )
            )
            ");
            $statement->bindParam(":startDate", $startDate);
            $statement->bindParam(":endDate", $endDate);
            $statement->bindParam(":facilityName", $facilityName);
            $statement->execute();

            $contact = $conn->prepare("SELECT p.* 
            FROM person p
            WHERE p.medicareNumber IN (
                SELECT p.medicareNumber
                FROM publicHealthWorker phw, past p, workHistory w, publicHealthCenter phc,
                (
                    SELECT wa.medicareNumber, wa.centerName, infected_worker.datePerformed 
                    FROM worksAt wa, publicHealthCenter phc,
                    (
                        SELECT r.medicareNumber, d.datePerformed
                        FROM receive r, diagnostic d
                        WHERE r.diagnosticId = d.diagnosticId AND d.result = 'Positive' AND d.resultDate >=:startDate AND d.resultDate <=:endDate AND r.medicareNumber IN 
                        (
                            SELECT p.medicareNumber
                            FROM person p, publicHealthWorker phw
                            WHERE p.medicareNumber = phw.medicareNumber
                        )
                    ) as infected_worker
                    WHERE wa.centerName = phc.centerName AND phc.centerName =:facilityName AND wa.medicareNumber = infected_worker.medicareNumber
                ) as infected_worker_info
                WHERE phw.medicareNumber = p.medicareNumber AND p.workHistoryId = w.workHistoryId AND p.centerName = phc.centerName 
                AND infected_worker_info.centerName = phc.centerName 
                AND 
                (
                    (w.startDate between DATE_SUB(infected_worker_info.datePerformed, INTERVAL 14 DAY) AND infected_worker_info.datePerformed) 
                    OR (w.endDate between DATE_SUB(infected_worker_info.datePerformed, INTERVAL 14 DAY) AND infected_worker_info.datePerformed)
                    OR (DATE_SUB(infected_worker_info.datePerformed, INTERVAL 14 DAY) between w.startDate AND w.endDate)
                    OR (infected_worker_info.datePerformed between w.startDate AND w.endDate)
                )
            )
            ");
            $contact->bindParam(":startDate", $startDate);
            $contact->bindParam(":endDate", $endDate);
            $contact->bindParam(":facilityName", $facilityName);
            $contact->execute();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <title>Admin Workers Positive</title>
</head>

<body>
    <h1 class="title">Search Workers Tested Positive</h1>
    <?php
    if (!empty($startDate_err)) {
        echo '<div class="alert">' . $startDate_err . '</div>';
    } else if (!empty($endDate_err)) {
        echo '<div class="alert">' . $endDate_err . '</div>';
    } else if (!empty($facilityName_err)) {
        echo '<div class="alert">' . $facilityName_err . '</div>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="from">From:</label>
        <input type="date" id="from" name="from" /> <br /><br />

        <label for="to">To:</label>
        <input type="date" id="to" name="to" /> <br /><br />

        <label for="facilityName">Facilities:</label>
        <select name="facilityName" id="facilityName">
            <?php while ($row = $facilities->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <option value="<?= $row["centerName"] ?>"> <?= $row["centerName"] ?> </option>
            <?php } ?>
        </select> <br /><br />

        <input type="submit" value="Search" />
    </form>

    <? if (isset($statement) && isset($contact)) {?>
    <h3 class="title">Workers That Tested Positive</h3>
    <table id="admin-table">
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
                    Name
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
                        <?= $row["firstName"] . " " . $row["lastName"] ?>
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

    <h3 class="title">Workers In Contact With Positive Workers</h3>
    <table id="admin-table">
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
                    Name
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
            while ($row = $contact->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
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
                        <?= $row["firstName"] . " " . $row["lastName"] ?>
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
    <?}?>
</body>

</html>