<?php
session_start();

// Remove the user_details session variable
if (isset($_SESSION['user_details'])) {
    unset($_SESSION['user_details']);
}

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: /login");
exit();
?>
