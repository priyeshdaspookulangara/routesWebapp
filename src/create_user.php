<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($link, $_POST['role']);

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";

    if (mysqli_query($link, $sql)) {
        $last_id = mysqli_insert_id($link);
        echo json_encode(['success' => true, 'message' => 'User created successfully.', 'data' => ['id' => $last_id, 'name' => $_POST['name'], 'email' => $_POST['email'], 'role' => $_POST['role'], 'status' => 'Active']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating user: ' . mysqli_error($link)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>