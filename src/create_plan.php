<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $price = (float)$_POST['price'];
    $duration = (int)$_POST['duration'];

    $sql = "INSERT INTO ad_plans (name, price, duration) VALUES ('$name', $price, $duration)";

    if (mysqli_query($link, $sql)) {
        $last_id = mysqli_insert_id($link);
        echo json_encode(['success' => true, 'message' => 'Plan created successfully.', 'data' => ['id' => $last_id, 'name' => $_POST['name'], 'price' => $_POST['price'], 'duration' => $_POST['duration']]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating plan: ' . mysqli_error($link)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>