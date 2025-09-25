<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Admin Panel</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href="admin/users.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="admin/routes.php">Routes</a></li>
            <li class="nav-item"><a class="nav-link" href="admin/vehicles.php">Vehicles</a></li>
            <li class="nav-item"><a class="nav-link" href="admin/plans.php">Ad Plans</a></li>
            <li class="nav-item"><a class="nav-link" href="admin/providers.php">Ad Providers</a></li>
            <li class="nav-item"><a class="nav-link" href="admin/mp3s.php">MP3s</a></li>
            <li class="nav-item"><a class="nav-link" href="admin/complaints.php">Complaints</a></li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="../src/logout.php" class="btn btn-danger">Sign Out</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <div class="jumbotron">
        <h1 class="display-4">Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h1>
        <p class="lead">This is your admin dashboard. You can manage the system from here.</p>
    </div>
</div>

</body>
</html>