<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $route_id = !empty($_POST['route_id']) ? (int)$_POST['route_id'] : 'NULL';
    $status = mysqli_real_escape_string($link, $_POST['status']);

    if ($id > 0) {
        $sql = "UPDATE vehicles SET name='$name', route_id=$route_id, status='$status' WHERE id=$id";

        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Vehicle updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating vehicle.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>