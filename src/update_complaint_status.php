<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../public/login.php");
    exit;
}

require_once 'db.php';
$link = get_db_connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $complaint_id = (int)$_POST['complaint_id'];
    $status = mysqli_real_escape_string($link, $_POST['status']);

    $sql = "UPDATE complaints SET status = '$status' WHERE id = $complaint_id";

    if (mysqli_query($link, $sql)) {
        header("location: ../public/admin/view_complaint.php?id=$complaint_id");
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
}
?>