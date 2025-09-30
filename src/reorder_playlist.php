<?php
require_once 'db.php';
$link = get_db_connection();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $playlist_id = isset($_POST['playlist_id']) ? (int)$_POST['playlist_id'] : 0;
    $item_ids = isset($_POST['item_ids']) && is_array($_POST['item_ids']) ? $_POST['item_ids'] : [];

    if ($playlist_id <= 0 || empty($item_ids)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
        exit;
    }

    $success = true;
    foreach ($item_ids as $index => $item_id) {
        $order = $index + 1;
        $item_id = (int)$item_id;
        $sql = "UPDATE playlist_items SET sort_order = $order WHERE id = $item_id AND playlist_id = $playlist_id";
        if (!mysqli_query($link, $sql)) {
            $success = false;
            // Log error, but don't stop; try to update the rest
        }
    }

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Playlist reordered successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'An error occurred while reordering.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>