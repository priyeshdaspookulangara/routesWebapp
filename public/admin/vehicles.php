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

$sql_routes = "SELECT * FROM routes";
$result_routes = mysqli_query($link, $sql_routes);
$routes = mysqli_fetch_all($result_routes, MYSQLI_ASSOC);

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">Vehicle Management</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addVehicleModal">Add New Vehicle</button>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="vehiclesTable">
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
    </div>
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
    console.log("Vehicle management script loaded and document is ready.");

    // Add vehicle
    $('#addVehicleForm').on('submit', function(e) {
        e.preventDefault();
        console.log("Add vehicle form submitted.");
        $.ajax({
            url: '../../src/create_vehicle.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log("Add vehicle response:", response);
                if (response.success) {
                    $('#addVehicleModal').modal('hide');
                    $('#addVehicleForm')[0].reset();
                    var vehicle = response.data;
                    var routeName = $('#addVehicleForm').find('select[name="route_id"] option:selected').text();
                    var newRow = `
                        <tr id="vehicle-${vehicle.id}">
                            <td>${vehicle.id}</td>
                            <td class="name">${vehicle.name}</td>
                            <td class="route_name">${routeName}</td>
                            <td class="status">${vehicle.status}</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-btn" data-id="${vehicle.id}" data-name="${vehicle.name}" data-route_id="${vehicle.route_id}" data-status="${vehicle.status}">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${vehicle.id}">Delete</button>
                            </td>
                        </tr>`;
                    $('#vehiclesTable tbody').append(newRow);
                } else {
                    console.error('Error adding vehicle: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error on add vehicle: ' + textStatus + ' - ' + errorThrown);
                console.error(jqXHR.responseText);
            }
        });
    });

    // Edit vehicle - show modal
    $('#vehiclesTable').on('click', '.edit-btn', function() {
        console.log("Edit button clicked for vehicle ID:", $(this).data('id'));
        $('#edit-id').val($(this).data('id'));
        $('#edit-name').val($(this).data('name'));
        $('#edit-route_id').val($(this).data('route_id'));
        $('#edit-status').val($(this).data('status'));
        $('#editVehicleModal').modal('show');
    });

    // Update vehicle
    $('#editVehicleForm').on('submit', function(e) {
        e.preventDefault();
        console.log("Update vehicle form submitted.");
        var formData = $(this).serialize();
        $.ajax({
            url: '../../src/update_vehicle.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log("Update vehicle response:", response);
                if (response.success) {
                    $('#editVehicleModal').modal('hide');
                    var id = $('#edit-id').val();
                    var row = $('#vehicle-' + id);
                    row.find('.name').text($('#edit-name').val());
                    row.find('.route_name').text($('#edit-route_id option:selected').text());
                    row.find('.status').text($('#edit-status').val());
                } else {
                    console.error('Error updating vehicle: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error on update vehicle: ' + textStatus + ' - ' + errorThrown);
                console.error(jqXHR.responseText);
            }
        });
    });

    // Delete vehicle
    $('#vehiclesTable').on('click', '.delete-btn', function() {
        console.log("Delete button clicked for vehicle ID:", $(this).data('id'));
        if (confirm('Are you sure you want to delete this vehicle?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/delete_vehicle.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    console.log("Delete vehicle response:", response);
                    if (response.success) {
                        $('#vehicle-' + id).remove();
                    } else {
                        console.error('Error deleting vehicle: ' + response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error on delete vehicle: ' + textStatus + ' - ' + errorThrown);
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