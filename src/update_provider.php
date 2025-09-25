<?php
require_once 'db.php';
$link = get_db_connection();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $company_name = mysqli_real_escape_string($link, $_POST['company_name']);
    $user_id = (int)$_POST['user_id'];

    if ($id > 0) {
        $sql = "UPDATE ad_providers SET company_name='$company_name', user_id=$user_id WHERE id=$id";

        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Provider updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating provider.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid provider ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>