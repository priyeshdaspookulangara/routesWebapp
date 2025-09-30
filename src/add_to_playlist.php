<?php
require_once 'db.php';
$link = get_db_connection();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $playlist_id = isset($_POST['playlist_id']) ? (int)$_POST['playlist_id'] : 0;
    $mp3_id = isset($_POST['mp3_id']) ? (int)$_POST['mp3_id'] : 0;
    $type = isset($_POST['type']) && in_array($_POST['type'], ['song', 'ad']) ? $_POST['type'] : '';

    if ($playlist_id <= 0 || $mp3_id <= 0 || empty($type)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
        exit;
    }

    $sql = "INSERT INTO playlist_items (playlist_id, mp3_id, type) VALUES ($playlist_id, $mp3_id, '$type')";

    if (mysqli_query($link, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Item added to playlist.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding item: ' . mysqli_error($link)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>