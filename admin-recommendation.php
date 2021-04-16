<?php
require_once './db/db_connection.php';
require './admin-HomeButton.php';

$guideline = "GUIDELINE";

$displayRecommendation = $conn->prepare('SELECT * FROM recommendation;');
$displayRecommendation->execute();

$messageGuidelineList = $conn->prepare('SELECT m.messageId FROM message m WHERE m.messageType = :guideline;');
$messageGuidelineList->bindParam(':guideline', $guideline);
$messageGuidelineList->execute();

if(isset($_POST["recommendationCreate"]) && isset($_POST["create"]) ){
    $create = $conn->prepare("INSERT INTO recommendation(description) VALUES (:description);");
    $create->bindParam(':description', $_POST["recommendationCreate"]);
    $create->execute();
}

if(isset($_POST["deleteId"]) && isset($_POST["delete"]) ){
    $delete = $conn->prepare("DELETE FROM recommendation WHERE (recommendationId = :deleteId);");
    $delete->bindParam(':deleteId', $_POST["deleteId"]);
    $delete->execute();
}

if(isset($_POST["editId"]) && isset($_POST["edit"]) && isset($_POST["recommendationEdit"])){
    $update = $conn->prepare("UPDATE recommendation SET description = :recommendationEdit WHERE (recommendationId = :id);");
    $update->bindParam(':recommendationEdit', $_POST["recommendationEdit"]);
    $update->bindParam(':id', $_POST["editId"]);
    $update->execute();
}

if(isset($_POST["selectedGuideline"]) && isset($_POST["linkGuideline"]) && isset($_POST["link"])){
    $link = $conn->prepare('INSERT INTO ofType (recommendationId, messageId) VALUES (:recommendationId, :messageId);');
    $link->bindParam(':recommendationId', $_POST["linkGuideline"]);
    $link->bindParam(':messageId', $_POST["selectedGuideline"]);
    $link->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-table.css">
    <title>Admin Message</title>
</head>

<body>
    <h1 class="title">Manage Recommendation</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h3>Create Recommendation</h3>
        <p>Enter new Recommendation</p>
        <input type="text" name="recommendationCreate" id="input_box">
        <input type="submit" name="create" value="Create" id="button"></input>
        </br>
        <h3>Edit Recommendation</h3>
        <p>Enter Id</p>
        <input type="text" name="editId">
        <p>Enter new Recommendation</p>
        <input type="text" name="recommendationEdit" id="input_box">
        <input type="submit" name="edit" value="Edit" id="button"></input>
        </br>
        <h3>Delete Recommendation</h3>
        <p>Enter Id</p>
        <input type="text" name="deleteId">
        <input type="submit" name="delete" value="Delete" id="button"></input>
        </br>
        <h3>Link Recommendation To Message Guidelines</h3>
        <p>Select Guideline</p>
        <select name="selectedGuideline">
        <?php while ($row = $messageGuidelineList->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <option value="<?= $row["messageId"] ?>"> <?= $row["messageId"] ?> </option>
            <?php } ?>
        </select>
        <p>Enter Id</p>
        <input type="text" name="linkGuideline">
        <input type="submit" name="link" value="Link" id="button"></input>
    </form>

    <table id="admin-table">
        <thead>
            <tr>
                <th>
                    Recommendation Id
                </th>
                <th>
                    Description
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $displayRecommendation->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["recommendationId"] ?>
                    </td>
                    <td>
                        <?= $row["description"] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>