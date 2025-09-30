<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../src/db.php';
$link = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Invalid request method."]);
    exit;
}

// Get the posted data
$data = json_decode(file_get_contents("php://input"));

// Validate the data
if (!isset($data->ad_id) || !isset($data->timestamp) || !isset($data->route_id)) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Missing required data: ad_id, timestamp, and route_id."]);
    exit;
}

// The ad_id from the client will be in the format 'a{id}', so we need to extract the numeric part.
$ad_id_parts = sscanf($data->ad_id, "a%d");
$mp3_id = isset($ad_id_parts[0]) ? (int)$ad_id_parts[0] : 0;

if ($mp3_id <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid ad_id format."]);
    exit;
}

// Convert the client timestamp to a format MySQL understands
// The client might send it in various formats, so we'll try to parse it.
// Assuming ISO 8601 format (e.g., "2024-07-28T10:30:00Z")
$played_at_timestamp = strtotime($data->timestamp);
if ($played_at_timestamp === false) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid timestamp format."]);
    exit;
}
$played_at_mysql = date("Y-m-d H:i:s", $played_at_timestamp);
$route_id = (int)$data->route_id;


// Insert the record into the database
$sql = "INSERT INTO ad_plays (mp3_id, route_id, played_at) VALUES ($mp3_id, $route_id, '$played_at_mysql')";

if (mysqli_query($link, $sql)) {
    http_response_code(201); // Created
    echo json_encode(["message" => "Ad play event logged successfully."]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Failed to log ad play event.", "error" => mysqli_error($link)]);
}
?>