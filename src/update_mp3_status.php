<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status = mysqli_real_escape_string($link, $_POST['status']);

    if ($id > 0) {
        $sql = "UPDATE mp3_files SET status='$status' WHERE id=$id";

        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'MP3 status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating MP3 status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid MP3 ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>