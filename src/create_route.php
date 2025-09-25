<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($link, $_POST['name']);

    $sql = "INSERT INTO routes (name) VALUES ('$name')";

    if (mysqli_query($link, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Route created successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating route.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>