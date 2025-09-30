<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

// Fetch all subscriptions with provider and plan details, ordered by expiry date
$sql = "SELECT
            s.campaign_name,
            s.end_date,
            ap.company_name,
            p.name as plan_name
        FROM subscriptions s
        JOIN ad_providers ap ON s.provider_id = ap.id
        JOIN ad_plans p ON s.plan_id = p.id
        ORDER BY s.end_date ASC";

$result = mysqli_query($link, $sql);
$subscriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get today's date to check for expiry
$today = new DateTime();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ad Package Expiry List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Ad Package Expiry List</h2>
    <p>This report shows all active ad subscriptions, sorted by the soonest expiry date.</p>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Ad Provider</th>
                <th>Campaign Name</th>
                <th>Subscription Plan</th>
                <th>Expiry Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subscriptions)): ?>
                <tr>
                    <td colspan="5" class="text-center">No active subscriptions found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($subscriptions as $sub): ?>
                    <?php
                        $expiry_date = new DateTime($sub['end_date']);
                        $interval = $today->diff($expiry_date);
                        $days_left = (int)$interval->format('%r%a'); // %r gives sign, %a gives total days

                        $row_class = '';
                        $status_badge = '';

                        if ($days_left < 0) {
                            $row_class = 'table-secondary';
                            $status_badge = '<span class="badge badge-dark">Expired</span>';
                        } elseif ($days_left <= 30) {
                            $row_class = 'table-warning';
                            $status_badge = '<span class="badge badge-warning">Expires Soon</span>';
                        } else {
                            $status_badge = '<span class="badge badge-success">Active</span>';
                        }
                    ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td><?php echo htmlspecialchars($sub['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($sub['campaign_name']); ?></td>
                        <td><?php echo htmlspecialchars($sub['plan_name']); ?></td>
                        <td><?php echo $expiry_date->format('Y-m-d'); ?></td>
                        <td><?php echo $status_badge; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="../dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

</body>
</html>