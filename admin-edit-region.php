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
        $createRegion = $conn->prepare('UPDATE region SET name=:regionName, alertLevel = :currentAlertLevel WHERE region.regionId = :regionId');
        $createRegion->bindParam(":regionName", $regionName);
        $createRegion->bindParam(":currentAlertLevel", $currentAlertLevel);
        echo $region["regionId"];
        $createRegion->bindParam(":regionId", $region["regionId"]);
        if ($createRegion->execute()) {
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
        <label for="currentAlertLevel">Current Alert Level</label>
        <select name="currentAlertLevel" id="currentAlertLevel" value="<?= $region["alertLevel"] ?>">
            <option value="green">Green</option>
            <option value="orange">Orange</option>
            <option value="yellow">Yellow</option>
            <option value="red">Red</option>
        </select>
        <br />
        <input type="submit" value="Create">
    </form>
</body>

</html>