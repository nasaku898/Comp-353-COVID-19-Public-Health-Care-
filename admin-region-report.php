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
        $statement = $conn->prepare("SELECT DISTINCT r.name, m.date, m.description, m.oldAlertState, m.newAlertState
        FROM region r, notifies n, message m
        WHERE r.regionId = n.regionId AND n.messageId = m.messageId AND m.messageType = 'Alert' ORDER BY m.date desc");
        $statement->execute();

        $cases = $conn->prepare("SELECT result.name, group_concat(result.results, CONCAT(':', result.numberOfCase)) as cases
        FROM(
        SELECT r.name, patient_result.result as results, COUNT(p.medicareNumber) as numberOfCase
        FROM region r, situatedIn si, address a, livesAt la, person p,
        (
            SELECT p.medicareNumber, d.result
            FROM person p, receive r, diagnostic d
            WHERE p.medicareNumber = r.medicareNumber AND r.diagnosticId = d.diagnosticId
        ) as patient_result
        WHERE r.regionId = si.regionId 
        AND si.city = a.city AND si.streetName = a.streetName AND si.civicNumber = a.civicNumber
        AND la.city = a.city AND la.streetName = a.streetName AND la.civicNumber = a.civicNumber
        AND la.medicareNumber = p.medicareNumber AND p.medicareNumber = patient_result.medicareNumber
        GROUP BY r.name, patient_result.result
        ) as result
        GROUP BY result.name;");
        $cases->execute();
    }

    // Validate
    if (empty($startDate_err) && empty($endDate_err)) {
        // Check if start date greater than end date
        if ($startDate > $endDate) {
            $endDate_err = "Invalid date. 'From' date greater than 'To' date.";
            $statement = $conn->prepare("SELECT DISTINCT r.name, m.date, m.description, m.oldAlertState, m.newAlertState
            FROM region r, notifies n, message m
            WHERE r.regionId = n.regionId AND n.messageId = m.messageId AND m.messageType = 'Alert' ORDER BY m.date desc");
            $statement->execute();

            $cases = $conn->prepare("SELECT result.name, group_concat(result.results, CONCAT(':', result.numberOfCase)) as cases
            FROM(
            SELECT r.name, patient_result.result as results, COUNT(p.medicareNumber) as numberOfCase
            FROM region r, situatedIn si, address a, livesAt la, person p,
            (
                SELECT p.medicareNumber, d.result
                FROM person p, receive r, diagnostic d
                WHERE p.medicareNumber = r.medicareNumber AND r.diagnosticId = d.diagnosticId
            ) as patient_result
            WHERE r.regionId = si.regionId 
            AND si.city = a.city AND si.streetName = a.streetName AND si.civicNumber = a.civicNumber
            AND la.city = a.city AND la.streetName = a.streetName AND la.civicNumber = a.civicNumber
            AND la.medicareNumber = p.medicareNumber AND p.medicareNumber = patient_result.medicareNumber
            GROUP BY r.name, patient_result.result
            ) as result
            GROUP BY result.name;");
            $cases->execute();
        } else {
            $statement = $conn->prepare("SELECT DISTINCT r.name, m.date, m.description, m.oldAlertState, m.newAlertState
            FROM region r, notifies n, message m
            WHERE r.regionId = n.regionId AND n.messageId = m.messageId AND m.messageType = 'Alert' AND m.date >=:startDate AND m.date <=:endDate ORDER BY m.date desc");
            $statement->bindParam(":startDate", $startDate);
            $statement->bindParam(":endDate", $endDate);
            $statement->execute();

            $cases = $conn->prepare("SELECT result.name, group_concat(result.results, CONCAT(':', result.numberOfCase)) as cases
            FROM(
            SELECT r.name, patient_result.result as results, COUNT(p.medicareNumber) as numberOfCase
            FROM region r, situatedIn si, address a, livesAt la, person p,
            (
                SELECT p.medicareNumber, d.result
                FROM person p, receive r, diagnostic d
                WHERE p.medicareNumber = r.medicareNumber AND r.diagnosticId = d.diagnosticId AND d.resultDate >=:startDate and d.resultDate <=:endDate
            ) as patient_result
            WHERE r.regionId = si.regionId 
            AND si.city = a.city AND si.streetName = a.streetName AND si.civicNumber = a.civicNumber
            AND la.city = a.city AND la.streetName = a.streetName AND la.civicNumber = a.civicNumber
            AND la.medicareNumber = p.medicareNumber AND p.medicareNumber = patient_result.medicareNumber
            GROUP BY r.name, patient_result.result
            ) as result
            GROUP BY result.name;");
            $cases->bindParam(":startDate", $startDate);
            $cases->bindParam(":endDate", $endDate);
            $cases->execute();
        }
    }
} else {
    $statement = $conn->prepare("SELECT DISTINCT r.name, m.date, m.description, m.oldAlertState, m.newAlertState
    FROM region r, notifies n, message m
    WHERE r.regionId = n.regionId AND n.messageId = m.messageId AND m.messageType = 'Alert' ORDER BY m.date desc");
    $statement->execute();

    $cases = $conn->prepare("SELECT result.name, group_concat(result.results, CONCAT(':', result.numberOfCase)) as cases
    FROM(
    SELECT r.name, patient_result.result as results, COUNT(p.medicareNumber) as numberOfCase
    FROM region r, situatedIn si, address a, livesAt la, person p,
    (
        SELECT p.medicareNumber, d.result
        FROM person p, receive r, diagnostic d
        WHERE p.medicareNumber = r.medicareNumber AND r.diagnosticId = d.diagnosticId
    ) as patient_result
    WHERE r.regionId = si.regionId 
    AND si.city = a.city AND si.streetName = a.streetName AND si.civicNumber = a.civicNumber
    AND la.city = a.city AND la.streetName = a.streetName AND la.civicNumber = a.civicNumber
    AND la.medicareNumber = p.medicareNumber AND p.medicareNumber = patient_result.medicareNumber
    GROUP BY r.name, patient_result.result
    ) as result
    GROUP BY result.name;");
    $cases->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <title>Admin Regions Report</title>
</head>

<body>
    <h1 class="title">Regions Report</h1>
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
                    Region Name
                </th>
                <th>
                    Number of positive cases
                </th>
                <th>
                    Number of negative cases
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $cases->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["name"] ?>
                    </td>
                    <td>
                        <? 
                        if(isset($row["cases"]))
                        {
                            $done = false;
                            $case = explode(",", trim($row["cases"]));
                            foreach ($case as $value) {
                                if ($value[0] == 'P') {
                                    $caseNumber = substr($value, strpos($value, ':')+1, strlen($value)-1);
                                    echo $caseNumber;
                                    $done = true;
                                }
                            }
                            if(!$done)
                            {
                                echo '0';
                            }
                        } else{
                            echo '0';
                        }
                        ?>
                    </td>
                    <td>
                        <?
                        if(isset($row["cases"]))
                        {
                            $done = false;
                            $case = explode(",", trim($row["cases"]));
                            foreach ($case as $value) {
                                if ($value[0] == 'N') {
                                    $caseNumber = substr($value, strpos($value, ':')+1, strlen($value)-1);
                                    echo $caseNumber;
                                    $done = true;
                                }
                            }
                            if(!$done)
                            {
                                echo '0';
                            }
                        } else{
                            echo '0';
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table id="admin-table">
        <thead>
            <tr>
                <th>
                    Region Name
                </th>
                <th>
                    Alert Date
                </th>
                <th>
                    Description
                </th>
                <th>
                    Old Alert State
                </th>
                <th>
                    New Alert State
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["name"] ?>
                    </td>
                    <td>
                        <?= $row["date"] ?>
                    </td>
                    <td>
                        <?= $row["description"] ?>
                    </td>
                    <td>
                        <?= $row["oldAlertState"] ?>
                    </td>
                    <td>
                        <?= $row["newAlertState"] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>