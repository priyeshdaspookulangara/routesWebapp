<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

$sql = "SELECT * FROM users";
$result = mysqli_query($link, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>User Management</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addUserModal">Add New User</button>

    <table class="table table-bordered" id="usersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr id="user-<?php echo $user['id']; ?>">
                <td><?php echo $user['id']; ?></td>
                <td class="name"><?php echo htmlspecialchars($user['name']); ?></td>
                <td class="email"><?php echo htmlspecialchars($user['email']); ?></td>
                <td class="role"><?php echo htmlspecialchars($user['role']); ?></td>
                <td class="status"><?php echo htmlspecialchars($user['status']); ?></td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $user['id']; ?>" data-name="<?php echo htmlspecialchars($user['name']); ?>" data-email="<?php echo htmlspecialchars($user['email']); ?>" data-role="<?php echo $user['role']; ?>" data-status="<?php echo $user['status']; ?>">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $user['id']; ?>">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add User</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="Staff">Staff</option>
                            <option value="Agent">Agent</option>
                            <option value="Vehicle Owner">Vehicle Owner</option>
                            <option value="Ad Provider">Ad Provider</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" id="edit-role" class="form-control">
                            <option value="Staff">Staff</option>
                            <option value="Agent">Agent</option>
                            <option value="Vehicle Owner">Vehicle Owner</option>
                            <option value="Ad Provider">Ad Provider</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="Suspended">Suspended</option>
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
    // Add user
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../src/create_user.php',
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

    // Edit user - show modal
    $('#usersTable').on('click', '.edit-btn', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-name').val($(this).data('name'));
        $('#edit-email').val($(this).data('email'));
        $('#edit-role').val($(this).data('role'));
        $('#edit-status').val($(this).data('status'));
        $('#editUserModal').modal('show');
    });

    // Update user
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '../../src/update_user.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editUserModal').modal('hide');
                    var id = $('#edit-id').val();
                    var row = $('#user-' + id);
                    row.find('.name').text($('#edit-name').val());
                    row.find('.email').text($('#edit-email').val());
                    row.find('.role').text($('#edit-role').val());
                    row.find('.status').text($('#edit-status').val());
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Delete user
    $('#usersTable').on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this user?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/delete_user.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#user-' + id).remove();
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