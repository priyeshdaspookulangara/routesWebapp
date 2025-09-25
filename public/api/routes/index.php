<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../src/db.php';
$link = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id, name FROM routes";
    $result = mysqli_query($link, $sql);
    $routes = mysqli_fetch_all($result, MYSQLI_ASSOC);

    http_response_code(200);
    echo json_encode($routes);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
}
?>