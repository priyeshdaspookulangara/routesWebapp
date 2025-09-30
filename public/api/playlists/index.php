<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../src/db.php';
$link = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['route_id'])) {
    http_response_code(400);
    echo json_encode(["message" => "Bad Request. Route ID is required."]);
    exit;
}

$route_id = (int)$_GET['route_id'];

// Find the playlist associated with the route
$sql_playlist = "SELECT id FROM playlists WHERE route_id = $route_id LIMIT 1";
$result_playlist = mysqli_query($link, $sql_playlist);
$playlist_data = mysqli_fetch_assoc($result_playlist);

if (!$playlist_data) {
    http_response_code(404);
    echo json_encode(["message" => "No playlist found for this route."]);
    exit;
}

$playlist_id = (int)$playlist_data['id'];

// Fetch all items for this playlist
$sql_items = "SELECT m.id, m.title, m.artist, m.filename, pi.type
              FROM playlist_items pi
              JOIN mp3_files m ON pi.mp3_id = m.id
              WHERE pi.playlist_id = $playlist_id
              ORDER BY pi.sort_order ASC";

$result_items = mysqli_query($link, $sql_items);
$items = mysqli_fetch_all($result_items, MYSQLI_ASSOC);

// Dynamically determine the base URL for file links
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'];
$upload_path = '/uploads/mp3/';

$songs_array = [];
foreach ($items as $item) {
    $songs_array[] = [
        'id' => ($item['type'] === 'song' ? 's' : 'a') . $item['id'],
        'title' => $item['title'],
        'artist' => $item['artist'],
        'url' => $base_url . $upload_path . rawurlencode($item['filename']),
        'type' => $item['type']
    ];
}

$response = [
    "songs" => $songs_array
];

http_response_code(200);
echo json_encode($response);
?>