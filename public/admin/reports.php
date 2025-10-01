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

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">Ad Play Reports</h2>

    <div class="card mb-4">
        <div class="card-header">Filter Report</div>
        <div class="card-body">
            <form action="reports.php" method="get">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="route_id" class="form-label">Filter by Route</label>
                        <select name="route_id" id="route_id" class="form-select">
                            <option value="">All Routes</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?php echo $route['id']; ?>" <?php if ($filter_route_id == $route['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($route['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($filter_start_date); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($filter_end_date); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
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
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>