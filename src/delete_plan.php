<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        $sql = "DELETE FROM ad_plans WHERE id = $id";

        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Plan deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting plan.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid plan ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>