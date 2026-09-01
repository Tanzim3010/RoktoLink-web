<?php
session_start();
// Destroy all session variables
$_SESSION = array();
session_destroy();
// Redirect back to the login page
header("Location: ../Html/index.html");
exit();
?>