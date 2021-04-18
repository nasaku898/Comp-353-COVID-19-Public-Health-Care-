<?php require_once 'db/db_connection.php';

// define variables and set to empty values 
$centerNameErr = $phoneNumberErr = $websiteErr = $installmentTypeErr = $appointmentMethodErr = $civicNumberErr = $streetNameErr = $cityErr = $postalCodeErr = $provinceErr = "";
$centerName = $phoneNumber = $website = $installmentType = $appointmentMethod = $civicNumber = $streetName = $city = $postalCode = $region = $province = "";

$statementRegion = $conn->prepare('SELECT distinct name, regionId FROM region');
$statementRegion->execute();

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

  if (empty($_POST["civic_number"])) {
    $civicNumberErr = "* Civic number is required.";
  } else {
    $civicNumber = format_input($_POST["civic_number"]);
    if (!preg_match("/^[0-9.-]*$/", $civicNumber)) {
      $civicNumberErr = " * Only numbers, dashes and dots are allowed.";
    }
  }

  if (empty($_POST["street_name"])) {
    $streetNameErr = " * Street name is required";
  } else {
    $streetName = format_input($_POST["street_name"]);
  }

  if (empty($_POST["city"])) {
    $cityErr = " * City is required";
  } else {
    $city = format_input($_POST["city"]);
  }

  if (empty($_POST["postal_code"])) {
    $postalCodeErr = " * Postal code is required";
  } else {
    $postalCode = format_input($_POST["postal_code"]);
  }

  if (empty($_POST["province"])) {
    $provinceErr = " * Province is required";
  } else {
    $province = format_input($_POST["province"]);
  }

  $region = $_POST["region"];
}

function format_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  return $data;
}

if (isset($_POST["center_name"]) && !empty($_POST["center_name"]) && isset($_POST["website"]) && !empty($_POST["website"]) && isset($_POST["phone_number"]) && !empty($_POST["phone_number"]) && isset($_POST["installment_type"]) && !empty($_POST["installment_type"]) && isset($_POST["drive_thru"]) && !empty($_POST["drive_thru"])) {
  $statementFacility = $conn->prepare("INSERT IGNORE INTO publicHealthCenter(installmentType, appointmentMethod, phoneNumber, website, centerName, hasDriveThru)
                              VALUES (:installmentType, :appointmentMethod, :phoneNumber, :website, :centerName, :driveThrough);");
  $statementFacility->bindParam(":installmentType", $installmentType);
  $statementFacility->bindParam(":appointmentMethod", $appointmentMethod);
  $statementFacility->bindParam(":phoneNumber", $phoneNumber);
  $statementFacility->bindParam(":website", $website);
  $statementFacility->bindParam(":centerName", $centerName);
  $statementFacility->bindParam(":driveThrough", $_POST["drive_thru"]);

  $statementAddress = $conn->prepare("INSERT IGNORE INTO address(city, streetName, civicNumber)
                            VALUES (:city, :streetName, :civicNumber);");
  $statementAddress->bindParam(":city", $city);
  $statementAddress->bindParam(":streetName", $streetName);
  $statementAddress->bindParam(":civicNumber", $civicNumber);

  $statementPostalCode = $conn->prepare("INSERT IGNORE INTO postalArea(postalCode, province)
  VALUES (:postalCode, :province);");
  $statementPostalCode->bindParam(":postalCode", $postalCode);
  $statementPostalCode->bindParam(":province", $province);

  $statementLocated = $conn->prepare("INSERT IGNORE INTO located(centerName, streetName, civicNumber, city)
                            VALUES (:centerName, :streetName, :civicNumber, :city);");
  $statementLocated->bindParam(":centerName", $centerName);
  $statementLocated->bindParam(":streetName", $streetName);
  $statementLocated->bindParam(":civicNumber", $civicNumber);
  $statementLocated->bindParam(":city", $city);

  $statementInside = $conn->prepare("INSERT IGNORE INTO inside(city, streetName, civicNumber, postalCode)
  VALUES (:city, :streetName, :civicNumber, :postalCode);");
  $statementInside->bindParam(":city", $city);
  $statementInside->bindParam(":streetName", $streetName);
  $statementInside->bindParam(":civicNumber", $civicNumber);
  $statementInside->bindParam(":postalCode", $postalCode);

  $statementSituatedIn = $conn->prepare("INSERT IGNORE INTO situatedIn(regionId, city, streetName, civicNumber)
  VALUES (:regionId, :city, :streetName, :civicNumber);");
  $statementSituatedIn->bindParam(":regionId", $region);
  $statementSituatedIn->bindParam(":city", $city);
  $statementSituatedIn->bindParam(":streetName", $streetName);
  $statementSituatedIn->bindParam(":civicNumber", $civicNumber);

  if ($statementFacility->execute() && $statementAddress->execute() && $statementPostalCode->execute()) {
    if ($statementLocated->execute() && $statementInside->execute() && $statementSituatedIn->execute()) {
      unset($_POST);
      ob_start();
      header("location: https://aec353.encs.concordia.ca/admin-home.php");
      ob_end_flush();
      die();
    }
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
        <h3> Address </h3>
        Civic Number: 
        <input type="text" name="civic_number" value="<?php echo $civicNumber;?>">
        <span class="error"><?php echo $civicNumberErr;?></span>
        <br><br>
        Street Name: <input type="text" name="street_name" value="<?php echo $streetName;?>">
        <span class="error"><?php echo $streetNameErr;?></span>
        <br><br>
        City: <input type="text" name="city" value="<?php echo $city;?>">
        <span class="error"><?php echo $cityErr;?></span>
        <br><br>
        Postal Code: <input type="text" name="postal_code" value="<?php echo $postalCode;?>">
        <span class="error"><?php echo $postalCodeErr;?></span>
        <br><br>
        Province: <input type="text" name="province" value="<?php echo $province;?>">
        <span class="error"><?php echo $provinceErr;?></span>
        <br><br>
        Region: 
        <select name="region" id="region">
            <?php while ($row = $statementRegion->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                <option value="<?= $row["regionId"] ?>"> <?= $row["name"] ?> </option>
            <?php } ?>
        </select>
        <br><br>
        <input type="button" onClick="document.location.href='https://aec353.encs.concordia.ca/admin-home.php'" value="Cancel" />
        <input type="submit" name="submit" value="Create New Facility">  
    </form>
</body>

</html>