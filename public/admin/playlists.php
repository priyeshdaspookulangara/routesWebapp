<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

// Fetch playlists with their assigned route names
$sql = "SELECT p.*, r.name as route_name FROM playlists p LEFT JOIN routes r ON p.route_id = r.id";
$result = mysqli_query($link, $sql);
$playlists = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch all routes for the modal dropdown
$sql_routes = "SELECT * FROM routes";
$result_routes = mysqli_query($link, $sql_routes);
$routes = mysqli_fetch_all($result_routes, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Playlist Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>Playlist Management</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addPlaylistModal">Add New Playlist</button>

    <table class="table table-bordered" id="playlistsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Playlist Name</th>
                <th>Assigned Route</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($playlists as $playlist): ?>
            <tr id="playlist-<?php echo $playlist['id']; ?>">
                <td><?php echo $playlist['id']; ?></td>
                <td class="name"><?php echo htmlspecialchars($playlist['name']); ?></td>
                <td class="route_name"><?php echo htmlspecialchars($playlist['route_name'] ?? 'None'); ?></td>
                <td>
                    <a href="edit_playlist.php?id=<?php echo $playlist['id']; ?>" class="btn btn-info btn-sm">Edit Items</a>
                    <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $playlist['id']; ?>" data-name="<?php echo htmlspecialchars($playlist['name']); ?>" data-route_id="<?php echo $playlist['route_id']; ?>">Edit Name/Route</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $playlist['id']; ?>">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Playlist Modal -->
<div class="modal fade" id="addPlaylistModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Playlist</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="addPlaylistForm">
                    <div class="form-group">
                        <label>Playlist Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Assign to Route</label>
                        <select name="route_id" class="form-control">
                            <option value="">None</option>
                            <?php foreach ($routes as $route): ?>
                            <option value="<?php echo $route['id']; ?>"><?php echo htmlspecialchars($route['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Playlist Modal -->
<div class="modal fade" id="editPlaylistModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Playlist</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editPlaylistForm">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label>Playlist Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Assign to Route</label>
                        <select name="route_id" id="edit-route_id" class="form-control">
                            <option value="">None</option>
                            <?php foreach ($routes as $route): ?>
                            <option value="<?php echo $route['id']; ?>"><?php echo htmlspecialchars($route['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add playlist
    $('#addPlaylistForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../src/create_playlist.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload(); // Easiest way to show the new playlist
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Edit playlist - show modal
    $('#playlistsTable').on('click', '.edit-btn', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-name').val($(this).data('name'));
        $('#edit-route_id').val($(this).data('route_id'));
        $('#editPlaylistModal').modal('show');
    });

    // Update playlist
    $('#editPlaylistForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../src/update_playlist.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload(); // Easiest way to reflect changes
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Delete playlist
    $('#playlistsTable').on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this playlist? This cannot be undone.')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/delete_playlist.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#playlist-' + id).remove();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    });
});
</script>

</body>
</html>