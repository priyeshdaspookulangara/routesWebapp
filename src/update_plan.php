<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $price = (float)$_POST['price'];
    $duration = (int)$_POST['duration'];

    if ($id > 0) {
        $sql = "UPDATE ad_plans SET name='$name', price=$price, duration=$duration WHERE id=$id";

        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Plan updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating plan.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid plan ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>