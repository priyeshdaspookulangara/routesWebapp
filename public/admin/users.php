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

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">User Management</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addUserModal">Add New User</button>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="usersTable">
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
    </div>
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
// NOTE: This script assumes jQuery is loaded in the footer.
// It might be better to move all page-specific scripts to the footer as well.
$(document).ready(function() {
    console.log("User management script loaded and document is ready.");

    // Add user
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        console.log("Add user form submitted.");
        $.ajax({
            url: '../../src/create_user.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log("Add user response:", response);
                if (response.success) {
                    $('#addUserModal').modal('hide');
                    $('#addUserForm')[0].reset();
                    var user = response.data;
                    var newRow = `
                        <tr id="user-${user.id}">
                            <td>${user.id}</td>
                            <td class="name">${user.name}</td>
                            <td class="email">${user.email}</td>
                            <td class="role">${user.role}</td>
                            <td class="status">${user.status}</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-btn" data-id="${user.id}" data-name="${user.name}" data-email="${user.email}" data-role="${user.role}" data-status="${user.status}">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${user.id}">Delete</button>
                            </td>
                        </tr>`;
                    $('#usersTable tbody').append(newRow);
                } else {
                    console.error('Error adding user: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error on add user: ' + textStatus + ' - ' + errorThrown);
                console.error(jqXHR.responseText);
            }
        });
    });

    // Edit user - show modal
    $('#usersTable').on('click', '.edit-btn', function() {
        console.log("Edit button clicked for user ID:", $(this).data('id'));
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
        console.log("Update user form submitted.");
        var formData = $(this).serialize();
        $.ajax({
            url: '../../src/update_user.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log("Update user response:", response);
                if (response.success) {
                    $('#editUserModal').modal('hide');
                    var id = $('#edit-id').val();
                    var row = $('#user-' + id);
                    row.find('.name').text($('#edit-name').val());
                    row.find('.email').text($('#edit-email').val());
                    row.find('.role').text($('#edit-role').val());
                    row.find('.status').text($('#edit-status').val());
                } else {
                    console.error('Error updating user: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error on update user: ' + textStatus + ' - ' + errorThrown);
                console.error(jqXHR.responseText);
            }
        });
    });

    // Delete user
    $('#usersTable').on('click', '.delete-btn', function() {
        console.log("Delete button clicked for user ID:", $(this).data('id'));
        if (confirm('Are you sure you want to delete this user?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/delete_user.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    console.log("Delete user response:", response);
                    if (response.success) {
                        $('#user-' + id).remove();
                    } else {
                        console.error('Error deleting user: ' + response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error on delete user: ' + textStatus + ' - ' + errorThrown);
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