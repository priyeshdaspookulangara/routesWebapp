<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = mysqli_real_escape_string($link, $_POST['name']);

    if ($id > 0) {
        $sql = "UPDATE routes SET name='$name' WHERE id=$id";

        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Route updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating route.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid route ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>