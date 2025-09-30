<?php
require_once 'db.php';
$link = get_db_connection();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $route_id = !empty($_POST['route_id']) ? (int)$_POST['route_id'] : 'NULL';

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Playlist ID.']);
        exit;
    }
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Playlist name is required.']);
        exit;
    }

    $sql = "UPDATE playlists SET name = '$name', route_id = $route_id WHERE id = $id";

    if (mysqli_query($link, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Playlist updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating playlist: ' . mysqli_error($link)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>