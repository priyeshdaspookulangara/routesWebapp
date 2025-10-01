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

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">Playlist Management</h2>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addPlaylistModal">Add New Playlist</button>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="playlistsTable">
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
                            <a href="edit_playlist.php?id=<?php echo $playlist['id']; ?>" class="btn btn-info btn-sm">Manage Songs/Ads</a>
                            <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $playlist['id']; ?>" data-name="<?php echo htmlspecialchars($playlist['name']); ?>" data-route_id="<?php echo $playlist['route_id']; ?>">Edit Details</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $playlist['id']; ?>">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Playlist Modal -->
<div class="modal fade" id="addPlaylistModal" tabindex="-1" aria-labelledby="addPlaylistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPlaylistModalLabel">Add Playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPlaylistForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Playlist Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Assign to Route</label>
                        <select name="route_id" class="form-select">
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
<div class="modal fade" id="editPlaylistModal" tabindex="-1" aria-labelledby="editPlaylistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPlaylistModalLabel">Edit Playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPlaylistForm">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Playlist Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-route_id" class="form-label">Assign to Route</label>
                        <select name="route_id" id="edit-route_id" class="form-select">
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
                    location.reload();
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
        var modal = new bootstrap.Modal(document.getElementById('editPlaylistModal'));
        modal.show();
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
                    location.reload();
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

<?php
require_once 'includes/footer.php';
?>