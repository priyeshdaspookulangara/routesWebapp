<?php
// Function to determine if a navigation link is active
function isActive($page, $currentPage) {
    if (is_array($page)) {
        return in_array($currentPage, $page) ? 'active' : '';
    }
    return $page === $currentPage ? 'active' : '';
}

// Get the current page name from the URL
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Define breadcrumb trails for each page
$breadcrumbs = [
    'dashboard' => [['name' => 'Dashboard', 'link' => 'dashboard.php']],
    'users' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Users', 'link' => 'users.php']],
    'routes' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Routes', 'link' => 'routes.php']],
    'vehicles' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Vehicles', 'link' => 'vehicles.php']],
    'plans' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Ad Plans', 'link' => 'plans.php']],
    'providers' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Ad Providers', 'link' => 'providers.php']],
    'subscriptions' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Subscriptions', 'link' => 'subscriptions.php']],
    'mp3s' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'MP3s', 'link' => 'mp3s.php']],
    'playlists' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Playlists', 'link' => 'playlists.php']],
    'edit_playlist' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Playlists', 'link' => 'playlists.php'], ['name' => 'Edit Playlist', 'link' => '#']],
    'reports' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Ad Reports', 'link' => 'reports.php']],
    'expiries' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Subscription Expiries', 'link' => 'expiries.php']],
    'complaints' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Complaints', 'link' => 'complaints.php']],
    'view_complaint' => [['name' => 'Dashboard', 'link' => 'dashboard.php'], ['name' => 'Complaints', 'link' => 'complaints.php'], ['name' => 'View Complaint', 'link' => '#']],
];

$currentBreadcrumbs = isset($breadcrumbs[$currentPage]) ? $breadcrumbs[$currentPage] : $breadcrumbs['dashboard'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords(str_replace('_', ' ', $currentPage)); ?> - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 280px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            color: #fff;
            background-color: #495057;
        }
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar flex-shrink-0 p-3 bg-dark text-white">
        <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <i class="fas fa-cogs fa-2x me-2"></i>
            <span class="fs-4">Admin Panel</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item"><a href="dashboard.php" class="nav-link <?php echo isActive('dashboard', $currentPage); ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
            <li class="nav-item"><a href="users.php" class="nav-link <?php echo isActive('users', $currentPage); ?>"><i class="fas fa-users me-2"></i>Users</a></li>
            <li class="nav-item"><a href="routes.php" class="nav-link <?php echo isActive('routes', $currentPage); ?>"><i class="fas fa-route me-2"></i>Routes</a></li>
            <li class="nav-item"><a href="vehicles.php" class="nav-link <?php echo isActive('vehicles', $currentPage); ?>"><i class="fas fa-bus me-2"></i>Vehicles</a></li>
            <li class="nav-item"><a href="plans.php" class="nav-link <?php echo isActive('plans', $currentPage); ?>"><i class="fas fa-tags me-2"></i>Ad Plans</a></li>
            <li class="nav-item"><a href="providers.php" class="nav-link <?php echo isActive('providers', $currentPage); ?>"><i class="fas fa-building me-2"></i>Ad Providers</a></li>
            <li class="nav-item"><a href="subscriptions.php" class="nav-link <?php echo isActive('subscriptions', $currentPage); ?>"><i class="fas fa-file-invoice-dollar me-2"></i>Subscriptions</a></li>
            <li class="nav-item"><a href="mp3s.php" class="nav-link <?php echo isActive('mp3s', $currentPage); ?>"><i class="fas fa-music me-2"></i>MP3s</a></li>
            <li class="nav-item"><a href="playlists.php" class="nav-link <?php echo isActive(['playlists', 'edit_playlist'], $currentPage); ?>"><i class="fas fa-list-ol me-2"></i>Playlists</a></li>
            <li class="nav-item"><a href="reports.php" class="nav-link <?php echo isActive('reports', $currentPage); ?>"><i class="fas fa-chart-bar me-2"></i>Ad Reports</a></li>
            <li class="nav-item"><a href="expiries.php" class="nav-link <?php echo isActive('expiries', $currentPage); ?>"><i class="fas fa-calendar-times me-2"></i>Expiries</a></li>
            <li class="nav-item"><a href="complaints.php" class="nav-link <?php echo isActive(['complaints', 'view_complaint'], $currentPage); ?>"><i class="fas fa-exclamation-triangle me-2"></i>Complaints</a></li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fa-2x me-2"></i>
                <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="../../src/logout.php">Sign out</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content p-4 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php foreach ($currentBreadcrumbs as $index => $crumb): ?>
                    <?php if ($index == count($currentBreadcrumbs) - 1): ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($crumb['name']); ?></li>
                    <?php else: ?>
                        <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($crumb['link']); ?>"><?php echo htmlspecialchars($crumb['name']); ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
        <hr>
</body>
</html>