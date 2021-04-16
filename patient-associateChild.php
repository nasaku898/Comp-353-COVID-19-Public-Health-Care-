<?php
require_once 'db/db_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parentMedicare = $_POST["parentMedicare"];
    $childMedicare = $_POST["childMedicare"];

    if (empty(trim($parentMedicare)) || empty(trim($childMedicare))) {
        $error = "error";
        echo $error;
    } else {
        $createParentOf = $conn->prepare("INSERT INTO parentOf(parentId, childId) VALUES(:parentMedicare,:childMedicare)");
        $createParentOf->bindParam(":parentMedicare", $parentMedicare);
        $createParentOf->bindParam(":childMedicare", $childMedicare);

        if ($createParentOf->execute()) {
            unset($_POST);
            ob_start();
            header("location: https://aec353.encs.concordia.ca/patient-home.php");
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
    <link rel="stylesheet" href="patient-registration.css">
    <link rel="stylesheet" href="admin-HomeButton.css">
    <link rel="stylesheet" href="index.css">
    <title>Patient Associate Child</title>
</head>

<body>
    <div class="homeButtonDiv">
        <a href="https://aec353.encs.concordia.ca/patient-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
    <h1 class="title">Associate Child</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="parentMedicare">Parent Medicare</label>
        <input type="text" id="parentMedicare" name="parentMedicare">
        <br />
        <label for="childMedicare">Child Medicare</label>
        <input type="text" id="childMedicare" name="childMedicare">
        <br />
        <p>**Make sure to create your child first**</p>
        <input type="submit" value="Submit">
    </form>
</body>

</html>