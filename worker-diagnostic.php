<?php
require_once 'db/db_connection.php';

$select = $conn->prepare('SELECT centerName FROM publicHealthCenter;');
$select->execute();

if (isset($_POST["result"]) && isset($_POST["datePerform"]) && isset($_POST["workerMedicareNumber"]) && isset($_POST["patientMedicareNumber"]) && isset($_POST["center"])) {

    $statement = $conn->prepare("INSERT INTO diagnostic(datePerformed, resultDate, result) VALUES (:datePerform, :todayDate, :result);");
    $statement->bindParam(':datePerform', $_POST["datePerform"]);
    $statement->bindParam(':result', $_POST["result"]);
    $statement->bindParam(':todayDate', date("Y-m-d h:i:s"));
    $statement->execute();
    $last_id = $conn->lastInsertId();

    $symptoms = $conn->prepare("INSERT INTO detects(diagnosticId, name) VALUES (:diagnosticId, :name);");
    if(isset($_POST["fever"])){
        $symptom = "Fever";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["cough"])){
        $symptom = "Cough";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["shortness"])){
        $symptom = "Shortness";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["lossTaste"])){
        $symptom = "Loss Taste";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["nausea"])){
        $symptom = "Nausea";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["stomach"])){
        $symptom = "Stomach Ache";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["vomit"])){
        $symptom = "Vomit";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["headache"])){
        $symptom = "Headache";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["muscle"])){
        $symptom = "Muscle Pain";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["diarrhea"])){
        $symptom = "Diarrhea";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    if(isset($_POST["sore"])){
        $symptom = "Sore Throat";
        $symptoms->bindParam(':diagnosticId', $last_id);
        $symptoms->bindParam(':name', $symptom);
        $symptoms->execute();
    }
    $test = $conn->prepare("INSERT INTO test (testId) VALUES (NULL);");
    $test->execute();
    $last_id_test = $conn->lastInsertId();

    $tested = $conn->prepare("INSERT INTO tested (testId,medicareNumberPerson,medicareNumberWorker) VALUES (:testId, :medicareNumberPerson, :medicareNumberWorker);");
    $tested->bindParam(':testId', $last_id_test);
    $tested->bindParam(':medicareNumberPerson', $_POST["patientMedicareNumber"]);
    $tested->bindParam(':medicareNumberWorker', $_POST["workerMedicareNumber"]);
    $tested->execute();


    $receive = $conn->prepare("INSERT INTO receive(diagnosticId, medicareNumber, centerName) VALUES (:diagnosticId, :medicareNumber, :centerName);");
    $receive->bindParam(':diagnosticId', $last_id);
    $receive->bindParam(':medicareNumber', $_POST["patientMedicareNumber"]);
    $receive->bindParam(':centerName', $_POST["center"]);

    if($receive->execute()){
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
    <link rel="stylesheet" href="worker-diagnostic.css">
    <title>Diagnostic</title>
</head>

<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h1 class="title">Enter Diagnostic</h1>
        <p>Health Care Worker Information</p>
        <div id="form">
            <p>Medicare Number</p>
            <input type="text" name="workerMedicareNumber" id="input_box">
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
        <p>Patient Information</p>
        <div id="form">
            <p>Patient Medicare Number</p>
            <input type="text" name="patientMedicareNumber" id="input_box">
        </div>
        <div id="form">
            <p>Patient Diagnostic Result (Positive/Negative)</p>
            <input type="text" name="result" id="input_box">
        </div>
        <div id="form">
            <p>Date Perform</p>
            <input type="date" name="datePerform" id="input_box">
        </div>
        </br>
        <p>Symptoms</p>
        <div id="form">
            <p>Fever</p>
            <input type="checkbox" id="check" name="fever">
            <p>Cough</p>
            <input type="checkbox" id="check" name="cough">
            <p>Shortness/Breath</p>
            <input type="checkbox" id="check" name="shortness">
            <p>Loss Taste</p>
            <input type="checkbox" id="check" name="lossTaste">
            <p>Nausea</p>
            <input type="checkbox" id="check" name="nausea">
            <p>Stomach Aches</p>
            <input type="checkbox" id="check" name="stomach">
        </div>
        <div id="form">
            <p>Vomiting</p>
            <input type="checkbox" id="check" name="vomit">
            <p>Headache</p>
            <input type="checkbox" id="check" name="headache">
            <p>Muscle Pain</p>
            <input type="checkbox" id="check" name="muscle">
            <p>Diarrhea</p>
            <input type="checkbox" id="check" name="diarrhea">
            <p>Sore Throat</p>
            <input type="checkbox" id="check" name="sore">
        </div>
        </br>
        <Button type="submit">Create</Button>
    </form>
</body>

</html>