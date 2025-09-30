<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

$sql = "SELECT * FROM ad_plans";
$result = mysqli_query($link, $sql);
$plans = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ad Plan Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>Ad Plan Management</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addPlanModal">Add New Plan</button>

    <table class="table table-bordered" id="plansTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Duration (Days)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plans as $plan): ?>
            <tr id="plan-<?php echo $plan['id']; ?>">
                <td><?php echo $plan['id']; ?></td>
                <td class="name"><?php echo htmlspecialchars($plan['name']); ?></td>
                <td class="price"><?php echo htmlspecialchars($plan['price']); ?></td>
                <td class="duration"><?php echo htmlspecialchars($plan['duration']); ?></td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $plan['id']; ?>" data-name="<?php echo htmlspecialchars($plan['name']); ?>" data-price="<?php echo $plan['price']; ?>" data-duration="<?php echo $plan['duration']; ?>">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $plan['id']; ?>">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Plan Modal -->
<div class="modal fade" id="addPlanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Plan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="addPlanForm">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Duration (Days)</label>
                        <input type="number" name="duration" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Plan Modal -->
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Plan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editPlanForm">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" id="edit-price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Duration (Days)</label>
                        <input type="number" name="duration" id="edit-duration" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add plan
    $('#addPlanForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../src/create_plan.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#addPlanModal').modal('hide');
                    $('#addPlanForm')[0].reset();
                    var plan = response.data;
                    var newRow = `
                        <tr id="plan-${plan.id}">
                            <td>${plan.id}</td>
                            <td class="name">${plan.name}</td>
                            <td class="price">${plan.price}</td>
                            <td class="duration">${plan.duration}</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-btn" data-id="${plan.id}" data-name="${plan.name}" data-price="${plan.price}" data-duration="${plan.duration}">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${plan.id}">Delete</button>
                            </td>
                        </tr>`;
                    $('#plansTable tbody').append(newRow);
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Edit plan - show modal
    $('#plansTable').on('click', '.edit-btn', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-name').val($(this).data('name'));
        $('#edit-price').val($(this).data('price'));
        $('#edit-duration').val($(this).data('duration'));
        $('#editPlanModal').modal('show');
    });

    // Update plan
    $('#editPlanForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '../../src/update_plan.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editPlanModal').modal('hide');
                    var id = $('#edit-id').val();
                    var row = $('#plan-' + id);
                    row.find('.name').text($('#edit-name').val());
                    row.find('.price').text($('#edit-price').val());
                    row.find('.duration').text($('#edit-duration').val());
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Delete plan
    $('#plansTable').on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this plan?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/delete_plan.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#plan-' + id).remove();
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