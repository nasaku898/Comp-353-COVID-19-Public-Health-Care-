<?php
session_start();

if (!isset($_SESSION["workerLoggedIn"]) || $_SESSION["workerLoggedIn"] !== true) {
    ob_start();
    header("location:https://aec353.encs.concordia.ca/worker-login.php");
    ob_end_flush();
    die();
}
require_once 'db/db_connection.php';

$fetchSymptoms = $conn->prepare("SELECT name from symptoms");
$fetchSymptoms->execute();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submitFollowUp"])) {
        $temperture = $_POST["temperature"];
        $followUpDate = $_POST["followUpDate"];
        $otherSymptoms = $_POST["otherSymptoms"];
        $diagnosticId = $_POST["diagnosticId"];

        if (empty(trim($temperture)) || empty(trim($followUpDate)) || empty(trim($diagnosticId))) {
            echo '<p>Please fill the form completely</p>';
        } else {
            $followUpDate = $followUpDate . ":00";
            $createFollowUp = $conn->prepare("INSERT INTO follow_up(temperature,followUpDate, otherSymptoms) VALUES(:temperature,:followUpDate,:otherSymptoms)");
            $createFollowUp->bindParam(":temperature", $temperture);
            $createFollowUp->bindParam(":followUpDate", $followUpDate);
            $createFollowUp->bindParam(":otherSymptoms", $otherSymptoms);
            $createFollowUp->execute();
            $followUpId = $conn->lastInsertId();

            $createDiagnosticFollowUp = $conn->prepare("INSERT INTO diagnosticFollowUp(diagnosticId, followUpId) VALUES(:diagnosticId, :followUpId)");
            $createDiagnosticFollowUp->bindParam(":diagnosticId", $diagnosticId);
            $createDiagnosticFollowUp->bindParam("followUpId", $followUpId);
            $createDiagnosticFollowUp->execute();

            $symptoms = $conn->prepare("INSERT INTO symptomsStatus(name, followUpId) VALUES(:name, :followUpId)");

            if (isset($_POST["fever"])) {
                $symptom = "Fever";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["cough"])) {
                $symptom = "Cough";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["shortness"])) {
                $symptom = "Shortness";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["lossTaste"])) {
                $symptom = "Loss Taste";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["nausea"])) {
                $symptom = "Nausea";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["stomach"])) {
                $symptom = "Stomach Ache";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["vomit"])) {
                $symptom = "Vomit";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["headache"])) {
                $symptom = "Headache";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["muscle"])) {
                $symptom = "Muscle Pain";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["diarrhea"])) {
                $symptom = "Diarrhea";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
            if (isset($_POST["sore"])) {
                $symptom = "Sore Throat";
                $symptoms->bindParam(':followUpId', $followUpId);
                $symptoms->bindParam(':name', $symptom);
                $symptoms->execute();
            }
        }
    } else if (isset($_POST["searchPatientDiagnostics"])) {
        $medicareNumber = $_POST["patientMedicareNumber"];
        if (empty(trim($medicareNumber))) {
            echo '<p>Please enter the patient medicare number</p>';
        } else {
            $fetchDiagnosticId = $conn->prepare("SELECT d.diagnosticId FROM diagnostic d, receive r, person p WHERE p.medicareNumber = :medicareNumber and r.medicareNumber = p.medicareNumber and r.diagnosticId = d.diagnosticId");
            $fetchDiagnosticId->bindParam(":medicareNumber", $medicareNumber);
            $fetchDiagnosticId->execute();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-login.css">
    <link rel="stylesheet" href="admin-HomeButton.css">
    <link rel="stylesheet" href="index.css">
    <title>Worker Enter Patient FollowUp</title>
</head>

<body>
    <h1 class="title">Worker Enter Patient FollowUp</h1>
    <div class="homeButtonDiv">
        <a href="https://aec353.encs.concordia.ca/worker-home.php">
            <button type="button" id="homeButton">Home</button>
        </a>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h3>Patient information</h3>
        <label>Patient medicare number</label>
        <input type="text" id="patientMedicareNumber" name="patientMedicareNumber">
        <br />
        <input type="submit" name="searchPatientDiagnostics" value="Search Diagnostic">
        <br />
        <label>Diagnostic Id</label>
        <select id="diagnosticId" name="diagnosticId">
            <?php while ($row = $fetchDiagnosticId->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <option value="<?= $row["diagnosticId"] ?>"> <?= $row["diagnosticId"] ?> </option>
            <?php } ?>
        </select>
        <br />
        <label>Temperature</label>
        <input type="number" id="temperature" name="temperature">
        <br />
        <label>Follow up date</label>
        <input type="datetime-local" id="followUpDate" name="followUpDate">
        <label>
            <h3>Symptoms</h3>
        </label>
        <br />
        <label>Fever</label>
        <input type="checkbox" id="check" name="fever">
        <br />
        <label>Cough</label>
        <input type="checkbox" id="check" name="cough">
        <br />
        <label>Shortness/Breath</label>
        <input type="checkbox" id="check" name="shortness">
        <br />
        <label>Loss Taste</label>
        <input type="checkbox" id="check" name="lossTaste">
        <br />
        <label>Nausea</label>
        <input type="checkbox" id="check" name="nausea">
        <br />
        <label>Stomach Aches</label>
        <input type="checkbox" id="check" name="stomach">
        <br />
        <label>Vomiting</label>
        <input type="checkbox" id="check" name="vomit">
        <br />
        <label>Headache</label>
        <input type="checkbox" id="check" name="headache">
        <br />
        <label>Muscle Pain</label>
        <input type="checkbox" id="check" name="muscle">
        <br />
        <label>Diarrhea</label>
        <input type="checkbox" id="check" name="diarrhea">
        <br />
        <label>Sore Throat</label>
        <input type="checkbox" id="check" name="sore">
        <br />
        <label>Other</label>
        <input type="text" id="otherSymptoms" name="otherSymptoms">
        <br />
        <input type="submit" name="submitFollowUp" value="Submit">
    </form>
</body>

</html>