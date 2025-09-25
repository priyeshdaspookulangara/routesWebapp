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
    $user_id = (int)$_SESSION['id'];
    $reply = mysqli_real_escape_string($link, $_POST['reply']);

    $sql = "INSERT INTO complaint_replies (complaint_id, user_id, reply) VALUES ($complaint_id, $user_id, '$reply')";

    if (mysqli_query($link, $sql)) {
        // Also update the complaint status to 'Open' if it's 'New'
        $sql_update = "UPDATE complaints SET status = 'Open' WHERE id = $complaint_id AND status = 'New'";
        mysqli_query($link, $sql_update);

        header("location: ../public/admin/view_complaint.php?id=$complaint_id");
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
}
?>