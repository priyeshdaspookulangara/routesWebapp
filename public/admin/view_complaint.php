<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

if (!isset($_GET['id'])) {
    header("location: complaints.php");
    exit;
}

$complaint_id = (int)$_GET['id'];

// Get complaint details
$sql = "SELECT c.*, u.name as user_name, r.name as route_name
        FROM complaints c
        JOIN users u ON c.user_id = u.id
        LEFT JOIN routes r ON c.route_id = r.id
        WHERE c.id = $complaint_id";
$result = mysqli_query($link, $sql);
$complaint = mysqli_fetch_assoc($result);

if (!$complaint) {
    echo "Complaint not found.";
    exit;
}

// Get complaint replies
$sql_replies = "SELECT cr.*, u.name as user_name
                FROM complaint_replies cr
                JOIN users u ON cr.user_id = u.id
                WHERE cr.complaint_id = $complaint_id
                ORDER BY cr.created_at ASC";
$result_replies = mysqli_query($link, $sql_replies);
$replies = mysqli_fetch_all($result_replies, MYSQLI_ASSOC);

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">Complaint #<?php echo $complaint['id']; ?></h2>

    <div class="card mb-3">
        <div class="card-header">
            <strong>User:</strong> <?php echo htmlspecialchars($complaint['user_name']); ?> |
            <strong>Route:</strong> <?php echo htmlspecialchars($complaint['route_name'] ?? 'N/A'); ?> |
            <strong>Status:</strong> <span class="badge bg-<?php echo strtolower($complaint['status']); ?>"><?php echo htmlspecialchars($complaint['status']); ?></span>
        </div>
        <div class="card-body">
            <p class="card-text"><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
        </div>
        <div class="card-footer text-muted">
            <?php echo $complaint['created_at']; ?>
        </div>
    </div>

    <hr>

    <h4>Replies</h4>
    <?php if (empty($replies)): ?>
        <p>No replies yet.</p>
    <?php else: ?>
        <?php foreach ($replies as $reply): ?>
        <div class="card mb-2">
            <div class="card-body">
                <p class="card-text"><?php echo nl2br(htmlspecialchars($reply['reply'])); ?></p>
            </div>
            <div class="card-footer text-muted">
                <strong>By:</strong> <?php echo htmlspecialchars($reply['user_name']); ?> at <?php echo $reply['created_at']; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h4>Add Reply</h4></div>
                <div class="card-body">
                    <form action="../../src/add_complaint_reply.php" method="post">
                        <input type="hidden" name="complaint_id" value="<?php echo $complaint_id; ?>">
                        <div class="mb-3">
                            <textarea name="reply" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Reply</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
             <div class="card">
                <div class="card-header"><h4>Update Status</h4></div>
                <div class="card-body">
                    <form action="../../src/update_complaint_status.php" method="post">
                        <input type="hidden" name="complaint_id" value="<?php echo $complaint_id; ?>">
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <option value="New" <?php if ($complaint['status'] == 'New') echo 'selected'; ?>>New</option>
                                <option value="Open" <?php if ($complaint['status'] == 'Open') echo 'selected'; ?>>Open</option>
                                <option value="Resolved" <?php if ($complaint['status'] == 'Resolved') echo 'selected'; ?>>Resolved</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <a href="complaints.php" class="btn btn-secondary mt-3">Back to Complaints List</a>
</div>

<?php
require_once 'includes/footer.php';
?>