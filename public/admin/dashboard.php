<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

// --- Fetch KPI Data ---

// Total Ad Providers
$result = mysqli_query($link, "SELECT COUNT(*) as total FROM ad_providers");
$total_providers = mysqli_fetch_assoc($result)['total'];

// Total Buses (Vehicles)
$result = mysqli_query($link, "SELECT COUNT(*) as total FROM vehicles");
$total_vehicles = mysqli_fetch_assoc($result)['total'];

// Total Routes
$result = mysqli_query($link, "SELECT COUNT(*) as total FROM routes");
$total_routes = mysqli_fetch_assoc($result)['total'];

// Total Active Subscriptions
$result = mysqli_query($link, "SELECT COUNT(*) as total FROM subscriptions WHERE end_date >= CURDATE()");
$active_subscriptions = mysqli_fetch_assoc($result)['total'];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h2 mb-4">Dashboard</h1>

    <!-- KPI Cards Row -->
    <div class="row">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Ad Providers</h5>
                            <p class="card-text fs-1 fw-bold"><?php echo $total_providers; ?></p>
                        </div>
                        <i class="fas fa-building fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Buses</h5>
                            <p class="card-text fs-1 fw-bold"><?php echo $total_vehicles; ?></p>
                        </div>
                        <i class="fas fa-bus fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Routes</h5>
                            <p class="card-text fs-1 fw-bold"><?php echo $total_routes; ?></p>
                        </div>
                        <i class="fas fa-route fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Active Subscriptions</h5>
                            <p class="card-text fs-1 fw-bold"><?php echo $active_subscriptions; ?></p>
                        </div>
                        <i class="fas fa-tags fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Placeholder for future charts -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Subscription Growth (Chart Placeholder)
                </div>
                <div class="card-body text-center">
                    <p class="card-text text-muted">A chart showing subscription growth over time could be implemented here using a library like Chart.js.</p>
                    <i class="fas fa-chart-line fa-5x text-light"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>