<?php
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
ob_start();
// Redirect user to welcome page
header("location: https://aec353.encs.concordia.ca/");
ob_end_flush();
die();
