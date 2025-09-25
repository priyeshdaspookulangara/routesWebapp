<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../src/db.php';
$link = get_db_connection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Assuming id refers to route_id for complaints
    $sql = "SELECT id, description, created_at as date FROM complaints WHERE route_id = $id";
    $result = mysqli_query($link, $sql);
    $complaints = mysqli_fetch_all($result, MYSQLI_ASSOC);

    http_response_code(200);
    echo json_encode($complaints);

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->routeId) || !isset($data->description) || !isset($data->userId)) {
        http_response_code(400);
        echo json_encode(["message" => "Route ID, description and user ID are required."]);
        exit;
    }

    $route_id = (int)$data->routeId;
    $description = mysqli_real_escape_string($link, $data->description);
    $user_id = (int)$data->userId;

    $sql = "INSERT INTO complaints (route_id, description, user_id) VALUES ($route_id, '$description', $user_id)";

    if (mysqli_query($link, $sql)) {
        http_response_code(201);
        echo json_encode((object)[]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to log complaint."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
}
?>