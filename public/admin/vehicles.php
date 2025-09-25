<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

$sql = "SELECT v.*, r.name as route_name FROM vehicles v LEFT JOIN routes r ON v.route_id = r.id";
$result = mysqli_query($link, $sql);
$vehicles = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql = "SELECT * FROM routes";
$result = mysqli_query($link, $sql);
$routes = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>Vehicle Management</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addVehicleModal">Add New Vehicle</button>

    <table class="table table-bordered" id="vehiclesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Route</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vehicles as $vehicle): ?>
            <tr id="vehicle-<?php echo $vehicle['id']; ?>">
                <td><?php echo $vehicle['id']; ?></td>
                <td class="name"><?php echo htmlspecialchars($vehicle['name']); ?></td>
                <td class="route_name"><?php echo htmlspecialchars($vehicle['route_name']); ?></td>
                <td class="status"><?php echo htmlspecialchars($vehicle['status']); ?></td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $vehicle['id']; ?>" data-name="<?php echo htmlspecialchars($vehicle['name']); ?>" data-route_id="<?php echo $vehicle['route_id']; ?>" data-status="<?php echo $vehicle['status']; ?>">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $vehicle['id']; ?>">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Vehicle</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="addVehicleForm">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Route</label>
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

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Vehicle</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editVehicleForm">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Route</label>
                        <select name="route_id" id="edit-route_id" class="form-control">
                            <option value="">None</option>
                            <?php foreach ($routes as $route): ?>
                            <option value="<?php echo $route['id']; ?>"><?php echo htmlspecialchars($route['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="In Maintenance">In Maintenance</option>
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
    // Add vehicle
    $('#addVehicleForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../src/create_vehicle.php',
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

    // Edit vehicle - show modal
    $('#vehiclesTable').on('click', '.edit-btn', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-name').val($(this).data('name'));
        $('#edit-route_id').val($(this).data('route_id'));
        $('#edit-status').val($(this).data('status'));
        $('#editVehicleModal').modal('show');
    });

    // Update vehicle
    $('#editVehicleForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '../../src/update_vehicle.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editVehicleModal').modal('hide');
                    var id = $('#edit-id').val();
                    var row = $('#vehicle-' + id);
                    row.find('.name').text($('#edit-name').val());
                    row.find('.route_name').text($('#edit-route_id option:selected').text());
                    row.find('.status').text($('#edit-status').val());
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Delete vehicle
    $('#vehiclesTable').on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this vehicle?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/delete_vehicle.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#vehicle-' + id).remove();
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