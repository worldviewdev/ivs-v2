<?php
require_once('includes/midas.inc.php');

// Clear all session data
session_destroy();

// Redirect to login page
header("Location: my-secure-login.php");
exit;
?>