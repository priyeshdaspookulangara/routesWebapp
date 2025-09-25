<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

$sql = "SELECT * FROM routes";
$result = mysqli_query($link, $sql);
$routes = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Route Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>Route Management</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addRouteModal">Add New Route</button>

    <table class="table table-bordered" id="routesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($routes as $route): ?>
            <tr id="route-<?php echo $route['id']; ?>">
                <td><?php echo $route['id']; ?></td>
                <td class="name"><?php echo htmlspecialchars($route['name']); ?></td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $route['id']; ?>" data-name="<?php echo htmlspecialchars($route['name']); ?>">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $route['id']; ?>">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Route Modal -->
<div class="modal fade" id="addRouteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Route</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="addRouteForm">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Route Modal -->
<div class="modal fade" id="editRouteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Route</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editRouteForm">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add route
    $('#addRouteForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../src/create_route.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Edit route - show modal
    $('#routesTable').on('click', '.edit-btn', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-name').val($(this).data('name'));
        $('#editRouteModal').modal('show');
    });

    // Update route
    $('#editRouteForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '../../src/update_route.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editRouteModal').modal('hide');
                    var id = $('#edit-id').val();
                    var row = $('#route-' + id);
                    row.find('.name').text($('#edit-name').val());
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Delete route
    $('#routesTable').on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this route?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/delete_route.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#route-' + id).remove();
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