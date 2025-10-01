<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

$sql = "SELECT ap.*, u.name as user_name FROM ad_providers ap JOIN users u ON ap.user_id = u.id";
$result = mysqli_query($link, $sql);
$providers = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql_users = "SELECT * FROM users WHERE role = 'Ad Provider'";
$result_users = mysqli_query($link, $sql_users);
$users = mysqli_fetch_all($result_users, MYSQLI_ASSOC);

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">Ad Provider Management</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addProviderModal">Add New Provider</button>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="providersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>User</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($providers as $provider): ?>
                    <tr id="provider-<?php echo $provider['id']; ?>">
                        <td><?php echo $provider['id']; ?></td>
                        <td class="company_name"><?php echo htmlspecialchars($provider['company_name']); ?></td>
                        <td class="user_name"><?php echo htmlspecialchars($provider['user_name']); ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $provider['id']; ?>" data-company_name="<?php echo htmlspecialchars($provider['company_name']); ?>" data-user_id="<?php echo $provider['user_id']; ?>">Edit</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $provider['id']; ?>">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Provider Modal -->
<div class="modal fade" id="addProviderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Provider</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="addProviderForm">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>User</label>
                        <select name="user_id" class="form-control" required>
                            <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Provider Modal -->
<div class="modal fade" id="editProviderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Provider</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editProviderForm">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" id="edit-company_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>User</label>
                        <select name="user_id" id="edit-user_id" class="form-control" required>
                            <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
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
    // Add provider
    $('#addProviderForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../src/create_provider.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#addProviderModal').modal('hide');
                    $('#addProviderForm')[0].reset();
                    var provider = response.data;
                    var userName = $('#addProviderForm').find('select[name="user_id"] option:selected').text();
                    var newRow = `
                        <tr id="provider-${provider.id}">
                            <td>${provider.id}</td>
                            <td class="company_name">${provider.company_name}</td>
                            <td class="user_name">${userName}</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-btn" data-id="${provider.id}" data-company_name="${provider.company_name}" data-user_id="${provider.user_id}">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${provider.id}">Delete</button>
                            </td>
                        </tr>`;
                    $('#providersTable tbody').append(newRow);
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Edit provider - show modal
    $('#providersTable').on('click', '.edit-btn', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-company_name').val($(this).data('company_name'));
        $('#edit-user_id').val($(this).data('user_id'));
        $('#editProviderModal').modal('show');
    });

    // Update provider
    $('#editProviderForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '../../src/update_provider.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editProviderModal').modal('hide');
                    var id = $('#edit-id').val();
                    var row = $('#provider-' + id);
                    row.find('.company_name').text($('#edit-company_name').val());
                    row.find('.user_name').text($('#edit-user_id option:selected').text());
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Delete provider
    $('#providersTable').on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this provider?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../../src/delete_provider.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#provider-' + id).remove();
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