<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../src/db.php';
$link = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $subscription_id = (int)$_GET['id'];
    $sql = "SELECT campaign_name, end_date as validity FROM subscriptions WHERE id = $subscription_id";
    $result = mysqli_query($link, $sql);
    $subscription = mysqli_fetch_assoc($result);

    if ($subscription) {
        http_response_code(200);
        echo json_encode($subscription);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Subscription not found."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Bad Request"]);
}
?>