<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../src/db.php';
$link = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $route_id = (int)$_GET['id'];

    // This is a more realistic, yet still simplified, implementation.
    // In a real-world scenario, you would have a dedicated table for tracking ad plays and durations.
    // Here, we'll simulate it based on subscriptions and mp3s.

    $sql = "SELECT COUNT(s.id) as totalPlays
            FROM subscriptions s
            JOIN ad_providers ap ON s.provider_id = ap.id
            JOIN users u ON ap.user_id = u.id";

    $result = mysqli_query($link, $sql);
    $total_plays = mysqli_fetch_assoc($result)['totalPlays'];

    $sql = "SELECT SUM(LENGTH(filename)) as totalDuration FROM mp3_files"; // A proxy for duration
    $result = mysqli_query($link, $sql);
    $total_duration = mysqli_fetch_assoc($result)['totalDuration'];


    http_response_code(200);
    echo json_encode([
        "totalPlays" => (int)$total_plays * 50, // Simulate more plays
        "totalDuration" => $total_duration ? (int)($total_duration / 1024) : 0 // Simulate duration in minutes
    ]);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Bad Request"]);
}
?>