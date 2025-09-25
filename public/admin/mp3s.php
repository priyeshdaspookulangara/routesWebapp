<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

$sql = "SELECT * FROM mp3_files";
$result = mysqli_query($link, $sql);
$mp3s = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MP3 Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>MP3 Management</h2>
    <form action="../../src/upload_mp3.php" method="post" enctype="multipart/form-data" class="mb-3">
        <div class="form-group">
            <label>Upload MP3 File</label>
            <input type="file" name="mp3_file" class="form-control-file" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>

    <table class="table table-bordered" id="mp3sTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Filename</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mp3s as $mp3): ?>
            <tr id="mp3-<?php echo $mp3['id']; ?>">
                <td><?php echo $mp3['id']; ?></td>
                <td><a href="../uploads/mp3/<?php echo htmlspecialchars($mp3['filename']); ?>" target="_blank"><?php echo htmlspecialchars($mp3['filename']); ?></a></td>
                <td class="status"><?php echo htmlspecialchars($mp3['status']); ?></td>
                <td>
                    <?php if ($mp3['status'] == 'Pending'): ?>
                    <button class="btn btn-success btn-sm approve-btn" data-id="<?php echo $mp3['id']; ?>">Approve</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#mp3sTable').on('click', '.approve-btn', function() {
        if (confirm('Are you sure you want to approve this MP3?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/update_mp3_status.php',
                type: 'POST',
                data: { id: id, status: 'Approved' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var row = $('#mp3-' + id);
                        row.find('.status').text('Approved');
                        row.find('.approve-btn').remove();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    });
});
</script>

</body>
</html>