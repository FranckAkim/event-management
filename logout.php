<?php
// logout.php - Logout and redirect to welcome page
session_start();
session_destroy();     // Clear all session data
header("Location: index.php");   // Redirect to welcome/landing page
exit;
