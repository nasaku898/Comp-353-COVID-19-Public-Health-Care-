<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    ob_start();
    header("location: https://aec353.encs.concordia.ca/admin-home.php");
    ob_end_flush();
    die();
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        if ($username == "admin" && $password == "Password123!") {
            session_start();

            $_SESSION["loggedin"] = true;

            ob_start();
            // Redirect user to welcome page
            header("location: https://aec353.encs.concordia.ca/admin-home.php");
            ob_end_flush();
            die();
        } else {
            // Password is not valid, display a generic error message
            $login_err = "Invalid username or password.";
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
    <title>Admin Login</title>
</head>

<body>
    <h1 class="title">Admin Login</h1>
    <?php
    if (!empty($login_err)) {
        echo '<div class="alert">' . $login_err . '</div>';
    } else if (!empty($username_err)) {
        echo '<div class="alert">' . $username_err . '</div>';
    } else if (!empty($password_err)) {
        echo '<div class="alert">' . $password_err . '</div>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="usernameDiv">
            <label for="username">Username:</label>
            <input type="text" name="username" /> <br /><br />
        </div>
        <div class="passwordDiv">
            <label for="password">Password:</label>
            <input type="password" name="password" /><br /><br />
        </div>
        <input type="submit" value="Login" />
    </form>
</body>

</html>