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

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">Route Management</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addRouteModal">Add New Route</button>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="routesTable">
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
    </div>
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
    console.log("Route management script loaded and document is ready.");

    // Add route
    $('#addRouteForm').on('submit', function(e) {
        e.preventDefault();
        console.log("Add route form submitted.");
        $.ajax({
            url: '../../src/create_route.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log("Add route response:", response);
                if (response.success) {
                    $('#addRouteModal').modal('hide');
                    $('#addRouteForm')[0].reset();
                    var route = response.data;
                    var newRow = `
                        <tr id="route-${route.id}">
                            <td>${route.id}</td>
                            <td class="name">${route.name}</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-btn" data-id="${route.id}" data-name="${route.name}">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${route.id}">Delete</button>
                            </td>
                        </tr>`;
                    $('#routesTable tbody').append(newRow);
                } else {
                    console.error('Error adding route: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error on add route: ' + textStatus + ' - ' + errorThrown);
                console.error(jqXHR.responseText);
            }
        });
    });

    // Edit route - show modal
    $('#routesTable').on('click', '.edit-btn', function() {
        console.log("Edit button clicked for route ID:", $(this).data('id'));
        $('#edit-id').val($(this).data('id'));
        $('#edit-name').val($(this).data('name'));
        $('#editRouteModal').modal('show');
    });

    // Update route
    $('#editRouteForm').on('submit', function(e) {
        e.preventDefault();
        console.log("Update route form submitted.");
        var formData = $(this).serialize();
        $.ajax({
            url: '../../src/update_route.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log("Update route response:", response);
                if (response.success) {
                    $('#editRouteModal').modal('hide');
                    var id = $('#edit-id').val();
                    var row = $('#route-' + id);
                    row.find('.name').text($('#edit-name').val());
                } else {
                    console.error('Error updating route: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error on update route: ' + textStatus + ' - ' + errorThrown);
                console.error(jqXHR.responseText);
            }
        });
    });

    // Delete route
    $('#routesTable').on('click', '.delete-btn', function() {
        console.log("Delete button clicked for route ID:", $(this).data('id'));
        if (confirm('Are you sure you want to delete this route?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/delete_route.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    console.log("Delete route response:", response);
                    if (response.success) {
                        $('#route-' + id).remove();
                    } else {
                        console.error('Error deleting route: ' + response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error on delete route: ' + textStatus + ' - ' + errorThrown);
                    console.error(jqXHR.responseText);
                }
            });
        }
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>