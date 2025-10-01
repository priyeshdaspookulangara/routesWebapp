<?php
// This page now serves as a redirect to the new admin dashboard.
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Redirect to the new admin dashboard
header("location: admin/dashboard.php");
exit;
?>