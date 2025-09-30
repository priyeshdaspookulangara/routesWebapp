<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($link, $_POST['name']);

    $sql = "INSERT INTO routes (name) VALUES ('$name')";

    if (mysqli_query($link, $sql)) {
        $last_id = mysqli_insert_id($link);
        echo json_encode(['success' => true, 'message' => 'Route created successfully.', 'data' => ['id' => $last_id, 'name' => $_POST['name']]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating route: ' . mysqli_error($link)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>