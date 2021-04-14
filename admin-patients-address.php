<?php
require_once './db/db_connection.php';
require 'admin-HomeButton.php';

// Initialize the session
session_start();

// Define variables and initialize with empty values
$civic = $street = $city = $postalCode = $province = "";
$civic_err = $street_err = $city_err = $postalCode_err = $province_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if civic is empty
    if (empty(trim($_POST["civic"]))) {
        $civic_err = "Please enter a civic number.";
    } else {
        $civic = trim($_POST["civic"]);
    }

    // Check if street is empty
    if (empty(trim($_POST["street"]))) {
        $street_err = "Please enter a street name.";
    } else {
        $street = trim($_POST["street"]);
    }

    // Check if city is empty
    if (empty(trim($_POST["city"]))) {
        $city_err = "Please enter a city name.";
    } else {
        $city = trim($_POST["city"]);
    }

    // Check if postalCode is empty
    if (empty(trim($_POST["postalCode"]))) {
        $postalCode_err = "Please enter a postal code.";
    } else {
        $postalCode = trim($_POST["postalCode"]);
    }

    // Check if province is empty
    if (empty(trim($_POST["province"]))) {
        $province_err = "Please enter a province.";
    } else {
        $province = trim($_POST["province"]);
    }

    if (!empty($civic_err) || !empty($street_err) || !empty($city_err) || !empty($postalCode_err) || !empty($province_err)) {
        $statement = $conn->prepare("SELECT p.firstName, p.lastName, p.dateOfBirth, p.medicareNumber, p.telephoneNumber, p.citizenship, p.emailAddress, group_concat(p2.firstName, concat('  ', p2.lastName),p2.gender) as parentNames
        FROM
        (SELECT p.medicareNumber, po.parentId
        FROM person p
        LEFT JOIN parentOf po on p.medicareNumber = po.childId)  as family
        LEFT JOIN person p2 on p2.medicareNumber = family.parentId
        LEFT JOIN person p on p.medicareNumber = family.medicareNumber
        GROUP BY p.firstName, p.lastName, p.dateOfBirth, p.medicareNumber, p.telephoneNumber, p.citizenship, p.emailAddress;");
        $statement->execute();
    }

    // Validate
    if (empty($civic_err) && empty($street_err) && empty($city_err) && empty($postalCode_err) && empty($province_err)) {
        $statement = $conn->prepare("SELECT p.firstName, p.lastName, p.dateOfBirth, p.medicareNumber, p.telephoneNumber, p.citizenship, p.emailAddress, group_concat(p2.firstName, concat('  ', p2.lastName),p2.gender) as parentNames
        FROM
        (SELECT p.medicareNumber, po.parentId
        FROM person p
        LEFT JOIN parentOf po on p.medicareNumber = po.childId
        WHERE p.medicareNumber IN 
        (
            SELECT p.medicareNumber
            FROM person p, livesAt la, inside i, postalArea pa
            WHERE la.medicareNumber = p.medicareNumber AND la.civicNumber = :civic AND la.streetName = :street AND la.city= :city 
            AND i.civicNumber = la.civicNumber AND i.streetName = la.streetName AND i.city=la.city AND pa.postalCode = i.postalCode
            AND pa.postalCode = :postalCode AND pa.province = :province
        ))  as family
        LEFT JOIN person p2 on p2.medicareNumber = family.parentId
        LEFT JOIN person p on p.medicareNumber = family.medicareNumber
        GROUP BY p.firstName, p.lastName, p.dateOfBirth, p.medicareNumber, p.telephoneNumber, p.citizenship, p.emailAddress;");
        $statement->bindParam(":civic", $civic);
        $statement->bindParam(":street", $street);
        $statement->bindParam(":city", $city);
        $statement->bindParam(":postalCode", $postalCode);
        $statement->bindParam(":province", $province);
        $statement->execute();
    }
} else {
    $statement = $conn->prepare("SELECT p.firstName, p.lastName, p.dateOfBirth, p.medicareNumber, p.telephoneNumber, p.citizenship, p.emailAddress, group_concat(p2.firstName, concat('  ', p2.lastName),p2.gender) as parentNames
    FROM
    (SELECT p.medicareNumber, po.parentId
    FROM person p
    LEFT JOIN parentOf po on p.medicareNumber = po.childId)  as family
    LEFT JOIN person p2 on p2.medicareNumber = family.parentId
    LEFT JOIN person p on p.medicareNumber = family.medicareNumber
    GROUP BY p.firstName, p.lastName, p.dateOfBirth, p.medicareNumber, p.telephoneNumber, p.citizenship, p.emailAddress;");
    $statement->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-patients-address.css">
    <title>Admin Patient Address</title>
</head>

<body>
    <h1 class="title">Patients By Address</h1>
    <?php
    if (!empty($civic_err)) {
        echo '<div class="alert">' . $civic_err . '</div>';
    } else if (!empty($street_err)) {
        echo '<div class="alert">' . $street_err . '</div>';
    } else if (!empty($city_err)) {
        echo '<div class="alert">' . $city_err . '</div>';
    } else if (!empty($postalCode_err)) {
        echo '<div class="alert">' . $postalCode_err . '</div>';
    } else if (!empty($province_err)) {
        echo '<div class="alert">' . $province_err . '</div>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="civic">Civic Number:</label>
        <input type="number" id="civic" name="civic" min="0" /> <br /><br />

        <label for="street">Street Name:</label>
        <input type="text" id="street" name="street" /> <br /><br />

        <label for="city">City:</label>
        <input type="text" id="city" name="city" /> <br /><br />

        <label for="postalCode">Postal Code:</label>
        <input type="text" id="postalCode" name="postalCode" /> <br /><br />

        <label for="province">Province:</label>
        <input type="text" id="province" name="province" /> <br /><br />

        <input type="submit" value="Search" />
    </form>

    <table id="patients-address">
        <thead>
            <tr>
                <th>
                    First Name
                </th>
                <th>
                    Last Name
                </th>
                <th>
                    Date Of Birth
                </th>
                <th>
                    Medicare
                </th>
                <th>
                    Telephone Number
                </th>
                <th>
                    Citizenship
                </th>
                <th>
                    Email Address
                </th>
                <th>
                    Father's Full Name
                </th>
                <th>
                    Mother's Full Name
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            ?>
                <tr>
                    <td>
                        <?= $row["firstName"] ?>
                    </td>
                    <td>
                        <?= $row["lastName"] ?>
                    </td>
                    <td>
                        <?= $row["dateOfBirth"] ?>
                    </td>
                    <td>
                        <?= $row["medicareNumber"] ?>
                    </td>
                    <td>
                        <?= $row["telephoneNumber"] ?>
                    </td>
                    <td>
                        <?= $row["citizenship"] ?>
                    </td>
                    <td>
                        <?= $row["emailAddress"] ?>
                    </td>
                    <td>
                        <? 
                            if(isset($row["parentNames"]))
                            {
                                $done = false;
                                $parents = explode(",", trim($row["parentNames"]));
                                foreach ($parents as $value) {
                                    if (substr($value, -1) == 'M') {
                                        $name = rtrim($value, "M ");
                                        echo $name;
                                        $done = true;
                                    }
                                }
                                if(!$done)
                                {
                                    echo 'None';
                                }
                            } else{
                                echo 'None';
                            }
                        ?>
                    </td>
                    <td>
                        <?
                            if(isset($row["parentNames"]))
                            {
                                $done = false;
                                $parents = explode(",", trim($row["parentNames"]));
                                foreach ($parents as $value) {
                                    if (substr($value, -1) == 'F') {
                                        $name = rtrim($value, "F ");
                                        echo $name;
                                        $done = true;
                                    }
                                }
                                if(!$done)
                                {
                                    echo 'None';
                                }
                            } else{
                                echo 'None';
                            } 
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>