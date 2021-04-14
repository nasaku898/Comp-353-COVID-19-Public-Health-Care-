<?php
require_once 'db/db_connection.php';

$select = $conn->prepare('SELECT centerName FROM publicHealthCenter;');
$select->execute();

if (isset($_POST["workerMedicareNumber"]) && isset($_POST["startDate"]) && isset($_POST["endDate"]) && isset($_POST["center"]) && ($_POST["endDate"] > $_POST["startDate"])) {
    $workHistory = $conn->prepare("INSERT INTO workHistory(startDate, endDate) VALUES (:startDate, :endDate);");
    $workHistory->bindParam(':startDate', $_POST["startDate"]);
    $workHistory->bindParam(':endDate', $_POST["endDate"]);
    $workHistory->execute();

    $last_id = $conn->lastInsertId();

    $past = $conn->prepare("INSERT INTO past(centerName, medicareNumber, workHistoryId) VALUES (:centerName, :medicareNumber, :workHistoryId);");
    $past->bindParam(':centerName', $_POST["center"]);
    $past->bindParam(':medicareNumber', $_POST["workerMedicareNumber"]);
    $past->bindParam(':workHistoryId', $last_id);
    $past->execute();

    if($past->execute()){
        unset($_POST, $last_id);
        ob_start();
        header("location: https://aec353.encs.concordia.ca/admin-home.php");
        ob_end_flush();
        die();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="worker-log.css">
    <title>Diagnostic</title>
</head>

<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h1 class="title">Work Log History</h1>
        <p>Health Care Worker Information</p>
        <div id="form">
            <p>Medicare Number</p>
            <input type="text" name="workerMedicareNumber" id="input_box">
        </div>
        <div id="form">
            <p>Start Date</p>
            <input type="date" name="startDate" id="input_box">
        </div>
        <div id="form">
            <p>End Date</p>
            <input type="date" name="endDate" id="input_box">
        </div>
        <div id="form">
            <p>Health Center</p>
            <select name="center" id="input_box">
            <?php while ($row = $select->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <option value="<?= $row["centerName"] ?>"> <?= $row["centerName"] ?> </option>
            <?php } ?>
        </select>
        </div>
        </br>
        <Button type="submit">Log</Button>
    </form>
</body>

</html>