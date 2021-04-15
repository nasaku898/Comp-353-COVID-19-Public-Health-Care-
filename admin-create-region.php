<?php require_once './db/db_connection.php';
require_once './admin-HomeButton.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $regionName = $_POST["regionName"];
    $currentAlertLevel = $_POST["currentAlertLevel"];

    if (empty(trim($regionName)) || empty(trim($currentAlertLevel))) {
        echo '<p>Please fill the form completely</p>';
    } else {
        $createRegion = $conn->prepare("INSERT INTO region(name,alertLevel) VALUES(:regionName, :currentAlertLevel)");
        $createRegion->bindParam(":regionName", $regionName);
        $createRegion->bindParam(":currentAlertLevel", $currentAlertLevel);
        if ($createRegion->execute()) {
            echo '<p>Successfully created new region</p>';
            unset($_POST);
            // ob_start();
            // //Redirect to manage region when completed
            // header("location: https://aec353.encs.concordia.ca/admin-home.php");
            // ob_end_flush();
            // die();
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
    <title>Create Region</title>
</head>

<body>
    <h1 class="title">Create Region</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="regionName">Region Name</label>
        <input type="text" id="regionName" name="regionName">
        <br />
        <label for="currentAlertLevel">Current Alert Level</label>
        <select name="currentAlertLevel" id="currentAlertLevel">
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