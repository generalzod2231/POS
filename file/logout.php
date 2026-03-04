<?php
session_start();
session_unset();    // Clear session variables
session_destroy();  // Destroy the session completely

// Send them back to the login page
header("Location: login.php");
exit();
?>