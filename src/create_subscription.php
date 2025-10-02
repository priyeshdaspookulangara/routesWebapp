<?php
require_once 'db.php';
$link = get_db_connection();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Data Validation ---
    $provider_id = isset($_POST['provider_id']) ? (int)$_POST['provider_id'] : 0;
    $plan_id = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;
    $campaign_name = isset($_POST['campaign_name']) ? mysqli_real_escape_string($link, $_POST['campaign_name']) : '';
    $start_date_str = isset($_POST['start_date']) ? $_POST['start_date'] : '';

    if ($provider_id <= 0 || $plan_id <= 0 || empty($campaign_name) || empty($start_date_str)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // --- Calculate End Date ---
    $sql_plan = "SELECT duration FROM ad_plans WHERE id = $plan_id";
    $result_plan = mysqli_query($link, $sql_plan);
    if (mysqli_num_rows($result_plan) == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Ad Plan selected.']);
        exit;
    }
    $plan = mysqli_fetch_assoc($result_plan);
    $duration_days = (int)$plan['duration'];

    try {
        $start_date = new DateTime($start_date_str);
        $end_date = clone $start_date;
        $end_date->add(new DateInterval("P{$duration_days}D"));
        $end_date_str = $end_date->format('Y-m-d');
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Invalid start date format.']);
        exit;
    }

    // --- Insert into Database ---
    $sql_insert = "INSERT INTO subscriptions (provider_id, plan_id, campaign_name, start_date, end_date)
                   VALUES ($provider_id, $plan_id, '$campaign_name', '{$start_date->format('Y-m-d')}', '$end_date_str')";

    if (mysqli_query($link, $sql_insert)) {
        echo json_encode(['success' => true, 'message' => 'Subscription created successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($link)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>