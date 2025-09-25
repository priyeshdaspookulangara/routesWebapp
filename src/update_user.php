<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $role = mysqli_real_escape_string($link, $_POST['role']);
    $status = mysqli_real_escape_string($link, $_POST['status']);

    if ($id > 0) {
        $sql = "UPDATE users SET name='$name', email='$email', role='$role', status='$status' WHERE id=$id";

        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'User updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating user.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>