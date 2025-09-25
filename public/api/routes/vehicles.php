<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../src/db.php';
$link = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['route_id'])) {
    $route_id = (int)$_GET['route_id'];
    $sql = "SELECT id, name FROM vehicles WHERE route_id = $route_id";
    $result = mysqli_query($link, $sql);
    $vehicles = mysqli_fetch_all($result, MYSQLI_ASSOC);

    http_response_code(200);
    echo json_encode($vehicles);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Bad Request"]);
}
?>