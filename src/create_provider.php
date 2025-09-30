<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = mysqli_real_escape_string($link, $_POST['company_name']);
    $user_id = (int)$_POST['user_id'];

    $sql = "INSERT INTO ad_providers (company_name, user_id) VALUES ('$company_name', $user_id)";

    if (mysqli_query($link, $sql)) {
        $last_id = mysqli_insert_id($link);
        echo json_encode(['success' => true, 'message' => 'Provider created successfully.', 'data' => ['id' => $last_id, 'company_name' => $_POST['company_name'], 'user_id' => $_POST['user_id']]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating provider: ' . mysqli_error($link)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>