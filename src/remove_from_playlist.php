<?php
require_once 'db.php';
$link = get_db_connection();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

    if ($item_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Item ID.']);
        exit;
    }

    $sql = "DELETE FROM playlist_items WHERE id = $item_id";

    if (mysqli_query($link, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Item removed from playlist.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error removing item: ' . mysqli_error($link)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>