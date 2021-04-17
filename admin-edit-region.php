<?php require_once './db/db_connection.php';

$fetchRegion = $conn->prepare('SELECT * FROM region WHERE region.regionId = :regionId');
$fetchRegion->bindParam(':regionId', $_GET["regionId"]);
$fetchRegion->execute();
$region = $fetchRegion->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $regionName = $_POST["regionName"];
    $currentAlertLevel = $_POST["currentAlertLevel"];

    if (empty(trim($regionName)) || empty(trim($currentAlertLevel))) {
        echo '<p>Please fill the form completely</p>';
    } else {
        $updateRegion = $conn->prepare('UPDATE region SET name=:regionName, alertLevel = :currentAlertLevel WHERE region.regionId = :regionId');
        $updateRegion->bindParam(":regionName", $regionName);
        $updateRegion->bindParam(":currentAlertLevel", $currentAlertLevel);
        $updateRegion->bindParam(":regionId", $_POST["regionId"]);
        if ($updateRegion->execute()) {
            $createAlertMessage = $conn->prepare('INSERT INTO message (description, date, oldAlertState, newAlertState, messageType) VALUES (:description, NOW(), :oldAlertLevel, :newAlertLevel, "ALERT");');

            $createGuidelineMessage = $conn->prepare('INSERT INTO message (description, date, oldAlertState, newAlertState, messageType) VALUES (:description, NOW(), :oldAlertLevel, :newAlertLevel, "GUIDELINE");');

            $alertRiseMsg = "Alert state has been updated to a HIGHER strict level.";
            $alertLowerMsg = "Alert state has been updated to a LESS strict level.";
            switch ($_POST["oldAlertLevel"]) {
                case "GREEN":
                    $createAlertMessage->bindParam(":description", $alertRiseMsg);
                    break;
                case "YELLOW":
                    if ($currentAlertLevel === "green") {
                        $createAlertMessage->bindParam(":description", $alertLowerMsg);
                    } else {
                        $createAlertMessage->bindParam(":description", $alertRiseMsg);
                    }
                    break;
                case "ORANGE":
                    if ($currentAlertLevel === "yellow") {
                        $createAlertMessage->bindParam(":description", $alertLowerMsg);
                    } else {
                        $redMsg = "Alert state has been updated to the HIGHEST level. Please be careful.";
                        $createAlertMessage->bindParam(":description", $redMsg);
                    }
                    break;
                case "RED":
                    $createAlertMessage->bindParam(":description", $alertLowerMsg);
                    break;
            }

            $createAlertMessage->bindParam(":oldAlertLevel", $_POST["oldAlertLevel"]);
            $createAlertMessage->bindParam(":newAlertLevel", $currentAlertLevel);
            $createAlertMessage->execute();

            $insertedAlertMessageId = $conn->lastInsertId();

            $guidelineMsg = "With the alert level changed, please follow the following recommendations.";
            $createGuidelineMessage->bindParam(":description", $guidelineMsg);
            $createGuidelineMessage->bindParam(":oldAlertLevel", $_POST["oldAlertLevel"]);
            $createGuidelineMessage->bindParam(":newAlertLevel", $currentAlertLevel);
            $createGuidelineMessage->execute();

            $insertedGuidelineMessage = $conn->lastInsertId();

            $fetchAllRecommendation = $conn->prepare('SELECT * FROM recommendation');
            $fetchAllRecommendation->execute();

            while ($recommendation = $fetchAllRecommendation->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                $createOfType = $conn->prepare('INSERT INTO ofType(recommendationId,messageId) VALUES(:recommendationId, :messageId)');

                $createOfType->bindParam(":recommendationId", $recommendation["recommendationId"]);
                $createOfType->bindParam(":messageId", $insertedGuidelineMessage);
                $createOfType->execute();
            }

            $findPerson = $conn->prepare('SELECT p.medicareNumber
            FROM person p, livesAt la, address a, situatedIn si, region r
            WHERE p.medicareNumber = la.medicareNumber 
            AND la.civicNumber = a.civicNumber AND la.streetName = a.streetName AND la.city = a.city
            AND a.civicNumber = si.civicNumber AND a.streetName = si.streetName AND a.city = si.city
            AND si.regionId = r.regionId
            AND r.name = :regionName;');
            $findPerson->bindParam(":regionName", $regionName);
            $findPerson->execute();

            while ($person = $findPerson->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                $personMedicareNumber = $person["medicareNumber"];
                $createNotifiesAlertMessage = $conn->prepare("INSERT INTO notifies(messageId, regionId, medicareNumber) VALUES(:messageId, :regionId, :medicareNumber)");
                $createNotifiesAlertMessage->bindParam(":messageId", $insertedAlertMessageId);
                $createNotifiesAlertMessage->bindParam(":regionId", $_POST["regionId"]);
                $createNotifiesAlertMessage->bindParam(":medicareNumber", $personMedicareNumber);
                $createNotifiesAlertMessage->execute();

                $createNotifiesGuidelineMessage = $conn->prepare("INSERT INTO notifies(messageId, regionId, medicareNumber) VALUES(:messageId, :regionId, :medicareNumber)");
                $createNotifiesGuidelineMessage->bindParam(":messageId", $insertedGuidelineMessage);
                $createNotifiesGuidelineMessage->bindParam(":regionId", $_POST["regionId"]);
                $createNotifiesGuidelineMessage->bindParam(":medicareNumber", $personMedicareNumber);
                $createNotifiesGuidelineMessage->execute();
            }

            unset($_POST);
            ob_start();
            //Redirect to manage region when completed
            header("location: https://aec353.encs.concordia.ca/admin-manage-region.php");
            ob_end_flush();
            die();
        }
    }

    unset($_POST);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="admin-HomeButton.css">
    <title>Update Region</title>
</head>

<body>
    <h1 class="title">Update Region</h1>
    <div class="homeButtonDiv">
        <a href="https://aec353.encs.concordia.ca/admin-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="regionName">Region Name</label>
        <input type="text" id="regionName" name="regionName" value="<?= $region["name"] ?>">
        <br />
        <p>Current Alert Level: <?= $region["alertLevel"] ?></p>
        <label for="currentAlertLevel">New Alert Level</label>
        <select name="currentAlertLevel" id="currentAlertLevel" ?>">
            <?php
            if ($region["alertLevel"] === "GREEN") {
                echo '<option value="yellow">Yellow</option>';
            } else if ($region["alertLevel"] === "YELLOW") {
                echo '<option value="green">Green</option>
                    <option value="orange">Orange</option>';
            } else if ($region["alertLevel"] === "ORANGE") {
                echo '<option value="yellow">Yellow</option>
                    <option value="red">Red</option>';
            } else if ($region["alertLevel"] === "RED") {
                echo '<option value="orange">Orange</option>';
            }
            ?>
        </select>
        <input type="hidden" name="oldAlertLevel" value="<?= $region["alertLevel"] ?>">
        <br />
        <input type="hidden" name="regionId" value="<?= $region["regionId"] ?>">
        <input type="submit" value="Update">
        <input type="button" onClick="document.location.href='https://aec353.encs.concordia.ca/admin-manage-region.php'" value="Cancel" />
    </form>
</body>

</html>