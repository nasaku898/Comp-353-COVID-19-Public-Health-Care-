<?php
session_start();

if ((!isset($_SESSION["workerLoggedIn"]) || $_SESSION["workerLoggedIn"] !== true)) {
    ob_start();
    header("location:https://aec353.encs.concordia.ca/worker-login.php");
    ob_end_flush();
    die();
}

require_once 'db/db_connection.php';
require 'worker-HomeButton.php';

$select = $conn->prepare('SELECT name FROM groupZone;');
$select->execute();

    if(isset($_POST["groupZoneCreate"]) && isset($_POST["create"]) ){
        $workHistory = $conn->prepare("INSERT INTO groupZone(name) VALUES (:name);");
        $workHistory->bindParam(':name', $_POST["groupZoneCreate"]);
        $workHistory->execute();
    }

    if(isset($_POST["zoneSelected"]) && isset($_POST["delete"]) ){
        $delete = $conn->prepare("DELETE FROM groupZone WHERE (name = :zoneToDelete);");
        $delete->bindParam(':zoneToDelete', $_POST["zoneSelected"]);
        $delete->execute();
    }

    if(isset($_POST["zoneSelected"]) && isset($_POST["patientMedicareNumber"]) && isset($_POST["add"]) ){
        $insertId = $conn->prepare("SELECT groupId FROM groupZone WHERE (name = :zoneToAdd);");
        $insertId->bindParam(':zoneToAdd', $_POST["zoneSelected"]);
        $insertId->execute();

        $insert;
        while ($row = $insertId->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $insert = $row["groupId"];
        }

        $add = $conn->prepare("INSERT INTO memberOf(medicareNumber, groupId) VALUES (:medicareNumber, :groupId);");
        $add->bindParam(':medicareNumber', $_POST["patientMedicareNumber"]);
        $add->bindParam(':groupId', $insert);
        $add->execute();
    }

    if(isset($_POST["zoneSelected"]) && isset($_POST["newNameZone"]) && isset($_POST["update"]) ){
        $editId = $conn->prepare("SELECT groupId FROM groupZone WHERE (name = :zoneToEdit);");
        $editId->bindParam(':zoneToEdit', $_POST["zoneSelected"]);
        $editId->execute();
        $edit;
        while ($row = $editId->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $edit = $row["groupId"];
        }
        $update = $conn->prepare("UPDATE groupZone SET name = :newNameZone WHERE (groupId = :id);");
        $update->bindParam(':newNameZone', $_POST["newNameZone"]);
        $update->bindParam(':id', $edit);
        $update->execute();
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="group-zone.css">
    <title>Group Zone</title>
</head>

<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h1 class="title">Group Zone</h1>
        <h2>Create Group Zone</h2>
        <div id="form">
            <p>Name</p>
            <input type="text" name="groupZoneCreate" id="input_box">
            <input type="submit" name="create" value="Create" id="button"></input>
        </div>
        </br>
        <p>Select Zone</p>
        <select name="zoneSelected" id="input_box">
            <?php while ($row = $select->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <option value="<?= $row["name"] ?>"> <?= $row["name"] ?> </option>
            <?php } ?>
        </select>
        <div id="form">
            <p>New Name</p>
            <input type="text" name="newNameZone" id="input_box">
            <input type="submit" name="update" id="button" value="Update"></input>
        </div>
        </br>
        <div id="form">
            <p>Add patient to group zone (Medicare Number)</p>
            <input type="text" name="patientMedicareNumber" id="input_box">
            <input type="submit" name="add" id="button" value="Add"></input>
        </div>
        </br>
        <div id="form">
            <p>Delete Group Zone</p>
            <input type="submit" name="delete" id="button" value="Delete"></input>
        </div>
        </br>
    </form>
</body>

</html>