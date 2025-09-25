<?php
require_once 'db.php';
$link = get_db_connection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["mp3_file"])) {
    $target_dir = "../public/uploads/mp3/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $filename = basename($_FILES["mp3_file"]["name"]);
    $target_file = $target_dir . $filename;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($_FILES['mp3_file']['tmp_name']);

    // Check if file is an actual MP3
    if ($file_type != "mp3" || $mime_type != 'audio/mpeg') {
        die("ERROR: Only MP3 files are allowed.");
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        die("ERROR: File already exists.");
    }

    if (move_uploaded_file($_FILES["mp3_file"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO mp3_files (filename) VALUES ('" . mysqli_real_escape_string($link, $filename) . "')";
        if (mysqli_query($link, $sql)) {
            header("location: ../public/admin/mp3s.php");
        } else {
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
        }
    } else {
        echo "ERROR: There was an error uploading your file.";
    }
}
?>