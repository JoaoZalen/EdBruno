<?php
// Start the session to access session data
session_start();

// Remove all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Redirect to the login page or home page
header("Location: ../index.php");
exit();
?>