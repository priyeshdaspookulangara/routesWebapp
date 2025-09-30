<?php
require_once 'db.php';
$link = get_db_connection();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $route_id = !empty($_POST['route_id']) ? (int)$_POST['route_id'] : 'NULL';

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Playlist name is required.']);
        exit;
    }

    $sql = "INSERT INTO playlists (name, route_id) VALUES ('$name', $route_id)";

    if (mysqli_query($link, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Playlist created successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating playlist: ' . mysqli_error($link)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>