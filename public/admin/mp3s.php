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

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">MP3 Management</h2>

    <div class="card mb-4">
        <div class="card-header">Upload New MP3</div>
        <div class="card-body">
            <form action="../../src/upload_mp3.php" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Artist / Advertiser</label>
                            <input type="text" name="artist" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                         <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Upload</button>
                    </div>
                    <div class="col-12">
                         <label>MP3 File</label>
                        <input type="file" name="mp3_file" class="form-control-file" required>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="mp3sTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Artist</th>
                        <th>Filename</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mp3s as $mp3): ?>
                    <tr id="mp3-<?php echo $mp3['id']; ?>">
                        <td><?php echo $mp3['id']; ?></td>
                        <td><?php echo htmlspecialchars($mp3['title']); ?></td>
                        <td><?php echo htmlspecialchars($mp3['artist']); ?></td>
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
    </div>
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

<?php
require_once 'includes/footer.php';
?>