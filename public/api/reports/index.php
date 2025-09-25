<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../src/db.php';
$link = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    $report_type = isset($data->report_type) ? $data->report_type : '';
    $provider_id = isset($data->provider_id) ? (int)$data->provider_id : 0;

    if (empty($report_type) || $provider_id <= 0) {
        http_response_code(400);
        echo json_encode(["message" => "Report type and provider ID are required."]);
        exit;
    }

    // This is a more realistic, yet still simplified, implementation.
    // In a real-world scenario, you would have a dedicated table for tracking ad plays.
    // Here, we'll simulate it based on subscriptions.

    $sql = "SELECT COUNT(s.id) as totalPlays
            FROM subscriptions s
            WHERE s.provider_id = $provider_id";

    $result = mysqli_query($link, $sql);
    $total_plays = mysqli_fetch_assoc($result)['totalPlays'];

    // Let's simulate a conversion rate.
    $conversionRate = $total_plays > 0 ? (mt_rand(10, 50) / $total_plays) : 0;


    http_response_code(200);
    echo json_encode([
        "totalPlays" => (int)$total_plays * 100, // Simulate more plays
        "conversionRate" => round($conversionRate, 2)
    ]);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
}
?>