<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

// Fetch all routes for the filter dropdown
$sql_routes = "SELECT * FROM routes ORDER BY name ASC";
$result_routes = mysqli_query($link, $sql_routes);
$routes = mysqli_fetch_all($result_routes, MYSQLI_ASSOC);

// ---- Filtering Logic ----
$filter_route_id = isset($_GET['route_id']) ? (int)$_GET['route_id'] : 0;
$filter_start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$filter_end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$sql = "SELECT
            ap.played_at,
            m.title as ad_title,
            m.artist as advertiser,
            r.name as route_name
        FROM ad_plays ap
        JOIN mp3_files m ON ap.mp3_id = m.id
        JOIN routes r ON ap.route_id = r.id
        WHERE m.id IN (SELECT mp3_id FROM playlist_items WHERE type = 'ad')";

$conditions = [];
if ($filter_route_id > 0) {
    $conditions[] = "ap.route_id = $filter_route_id";
}
if (!empty($filter_start_date)) {
    $conditions[] = "ap.played_at >= '" . mysqli_real_escape_string($link, $filter_start_date) . " 00:00:00'";
}
if (!empty($filter_end_date)) {
    $conditions[] = "ap.played_at <= '" . mysqli_real_escape_string($link, $filter_end_date) . " 23:59:59'";
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY ap.played_at DESC";

$result_logs = mysqli_query($link, $sql);
$ad_logs = mysqli_fetch_all($result_logs, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ad Play Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Ad Play Reports</h2>

    <!-- Filter Form -->
    <form action="reports.php" method="get" class="border p-3 mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="route_id">Filter by Route</label>
                    <select name="route_id" id="route_id" class="form-control">
                        <option value="">All Routes</option>
                        <?php foreach ($routes as $route): ?>
                            <option value="<?php echo $route['id']; ?>" <?php if ($filter_route_id == $route['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($route['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($filter_start_date); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($filter_end_date); ?>">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Report Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Ad Title</th>
                <th>Advertiser</th>
                <th>Route</th>
                <th>Played At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ad_logs)): ?>
                <tr>
                    <td colspan="4" class="text-center">No records found for the selected filters.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($ad_logs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['ad_title']); ?></td>
                    <td><?php echo htmlspecialchars($log['advertiser']); ?></td>
                    <td><?php echo htmlspecialchars($log['route_name']); ?></td>
                    <td><?php echo $log['played_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
     <a href="../dashboard.php" class="btn btn-secondary mt-3">Back to dashboard</a>
</div>

</body>
</html>