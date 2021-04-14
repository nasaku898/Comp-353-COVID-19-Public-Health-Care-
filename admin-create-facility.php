<?php require_once 'db/db_connection.php';

// define variables and set to empty values 
$centerNameErr = $phoneNumberErr = $websiteErr = $installmentTypeErr = $appointmentMethodErr = "";
$centerName = $phoneNumber = $website = $installmentType = $appointmentMethod = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["center_name"])) {
    $centerNameErr = "* Facility name is required.";
  } else {
    $centerName = format_input($_POST["center_name"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[A-Za-z0-9 ]*[A-Za-z0-9][A-Za-z0-9 ]*$/",$centerName)) {
      $centerNameErr = " * Only letters, numbers and white spaces allowed";
    }
  }
      
  if (empty($_POST["website"])) {
    $websiteErr = "* Website URL is required.";
  } else {
    $website = format_input($_POST["website"]);
    // check if URL address syntax is valid (this regular expression also allows dashes in the URL)
    if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
      $websiteErr = " * Invalid URL";
    }
  }

  if (empty($_POST["phone_number"])) {
    $phoneNumberErr = "* Phone number is required.";
  } else {
    $phoneNumber = format_input($_POST["phone_number"]);
    // check if e-mail address is well-formed
    if (!preg_match("/^\(?([0-9]{3})\)?[-.●]?([0-9]{3})[-.●]?([0-9]{4})$/", $phoneNumber)) {
      $phoneNumberErr = " * Invalid phone number format";
    }
  }

  if (empty($_POST["installment_type"])) {
    $installmentTypeErr = "* Installment type is required.";
  } else {
    $installmentType = format_input($_POST["installment_type"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z-' ]*$/",$installmentType)) {
      $installmentTypeErr = " * Only letters and white spaces allowed.";
    }
  }

  if (empty($_POST["appointment_method"])) {
    $appointmentMethodErr = " * Appointment method is required";
  } else {
    $appointmentMethod = format_input($_POST["appointment_method"]);
  }
}

function format_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  return $data;
}

if (isset($_POST["center_name"]) && !empty($_POST["center_name"]) && isset($_POST["website"]) && !empty($_POST["website"]) && isset($_POST["phone_number"]) && !empty($_POST["phone_number"]) && isset($_POST["installment_type"]) && !empty($_POST["installment_type"]) && isset($_POST["drive_thru"]) && !empty($_POST["drive_thru"])) {
  $statement = $conn->prepare("INSERT INTO publicHealthCenter(installmentType, appointmentMethod, phoneNumber, website, centerName, hasDriveThru)
                              VALUES (:installmentType, :appointmentMethod, :phoneNumber, :website, :centerName, :driveThrough);");
  $statement->bindParam(":installmentType", $installmentType);
  $statement->bindParam(":appointmentMethod", $appointmentMethod);
  $statement->bindParam(":phoneNumber", $phoneNumber);
  $statement->bindParam(":website", $website);
  $statement->bindParam(":centerName", $centerName);
  $statement->bindParam(":driveThrough", $_POST["drive_thru"]);

  if ($statement->execute()) {
      ob_start();
      header("location: https://aec353.encs.concordia.ca/admin-home.php");
      ob_end_flush();
      die();
  }else{
      $error_msg = "Failed to add to the database.";
      echo $error_msg;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-create-facility.css">
    <title>Create Facility</title>
</head>

<body>
    <h1 class="title">Create Facility</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
        Facility Name: <input type="text" name="center_name" value="<?php echo $name;?>">
        <span class="error"><?php echo $centerNameErr;?></span>
        <br><br>
        Website: <input type="text" name="website" value="<?php echo $website;?>">
        <span class="error"><?php echo $websiteErr;?></span>
        <br><br>
        Phone Number: <input type="text" name="phone_number" value="<?php echo $phoneNumber;?>">
        <span class="error"><?php echo $phoneNumberErr;?></span>
        <br><br>
        Installment Type: <input type="text" name="installment_type" value="<?php echo $installmentType;?>">
        <span class="error"><?php echo $installmentTypeErr;?></span>
        <br><br>
        Appointment Method: 
            <select name="appointment_method">
                <option value="Walk-In">Walk-In</option>
                <option value="Appointment-Only">Appointment Only</option>
                <option value="Both">Both</option>
            </select>
        <br><br>
        Has Drive Through?:
            <select name="drive_thru">
                <option value=1>Yes</option>
                <option value=0>No</option>
            </select>
        <span class="error"><?php echo $driveThruErr;?></span>
        <br><br>
        <input type="button" onClick="document.location.href='https://aec353.encs.concordia.ca/admin-home.php'" value="Cancel" />
        <input type="submit" name="submit" value="Create New Facility">  
    </form>
</body>

</html>