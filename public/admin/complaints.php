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
$sql .= " ORDER BY c.created_at DESC";
$result = mysqli_query($link, $sql);
$complaints = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">Complaint Management</h2>

    <div class="mb-3">
        <a href="complaints.php" class="btn btn-secondary">All</a>
        <a href="complaints.php?status=New" class="btn btn-info">New</a>
        <a href="complaints.php?status=Open" class="btn btn-warning">Open</a>
        <a href="complaints.php?status=Resolved" class="btn btn-success">Resolved</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
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
                    <?php if (empty($complaints)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No complaints found for the selected filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?php echo $complaint['id']; ?></td>
                            <td><?php echo htmlspecialchars($complaint['user_name']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></td>
                            <td><span class="badge bg-<?php echo strtolower($complaint['status']); ?>"><?php echo htmlspecialchars($complaint['status']); ?></span></td>
                            <td><?php echo $complaint['created_at']; ?></td>
                            <td>
                                <a href="view_complaint.php?id=<?php echo $complaint['id']; ?>" class="btn btn-primary btn-sm">View/Reply</a>
                            </td>
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