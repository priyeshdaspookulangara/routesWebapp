<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($link, $_GET['status']) : '';
$sql = "SELECT c.*, u.name as user_name FROM complaints c JOIN users u ON c.user_id = u.id";
if (!empty($status_filter)) {
    $sql .= " WHERE c.status = '$status_filter'";
}
$result = mysqli_query($link, $sql);
$complaints = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaint Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Complaint Management</h2>
    <div class="mb-3">
        <a href="complaints.php" class="btn btn-secondary">All</a>
        <a href="complaints.php?status=New" class="btn btn-info">New</a>
        <a href="complaints.php?status=Open" class="btn btn-warning">Open</a>
        <a href="complaints.php?status=Resolved" class="btn btn-success">Resolved</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($complaints as $complaint): ?>
            <tr>
                <td><?php echo $complaint['id']; ?></td>
                <td><?php echo htmlspecialchars($complaint['user_name']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></td>
                <td><?php echo htmlspecialchars($complaint['status']); ?></td>
                <td><?php echo $complaint['created_at']; ?></td>
                <td>
                    <a href="view_complaint.php?id=<?php echo $complaint['id']; ?>" class="btn btn-primary btn-sm">View/Reply</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>