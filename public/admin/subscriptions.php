<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();

// Fetch all subscriptions with details
$sql = "SELECT
            s.id,
            s.campaign_name,
            s.start_date,
            s.end_date,
            ap.company_name,
            p.name as plan_name
        FROM subscriptions s
        JOIN ad_providers ap ON s.provider_id = ap.id
        JOIN ad_plans p ON s.plan_id = p.id
        ORDER BY s.start_date DESC";
$result = mysqli_query($link, $sql);
$subscriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch ad providers for the modal
$sql_providers = "SELECT id, company_name FROM ad_providers ORDER BY company_name ASC";
$result_providers = mysqli_query($link, $sql_providers);
$providers = mysqli_fetch_all($result_providers, MYSQLI_ASSOC);

// Fetch ad plans for the modal
$sql_plans = "SELECT id, name, duration FROM ad_plans ORDER BY name ASC";
$result_plans = mysqli_query($link, $sql_plans);
$plans = mysqli_fetch_all($result_plans, MYSQLI_ASSOC);

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="h2 mb-4">Manage Subscriptions</h2>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addSubscriptionModal">
        <i class="fas fa-plus me-2"></i>Create New Subscription
    </button>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Provider</th>
                        <th>Campaign</th>
                        <th>Plan</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscriptions)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No subscriptions found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subscriptions as $sub): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sub['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($sub['campaign_name']); ?></td>
                            <td><?php echo htmlspecialchars($sub['plan_name']); ?></td>
                            <td><?php echo $sub['start_date']; ?></td>
                            <td><?php echo $sub['end_date']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Subscription Modal -->
<div class="modal fade" id="addSubscriptionModal" tabindex="-1" aria-labelledby="addSubscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubscriptionModalLabel">Create New Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSubscriptionForm">
                    <div class="mb-3">
                        <label for="provider_id" class="form-label">Ad Provider</label>
                        <select name="provider_id" id="provider_id" class="form-select" required>
                            <option value="">Select a Provider</option>
                            <?php foreach ($providers as $provider): ?>
                                <option value="<?php echo $provider['id']; ?>"><?php echo htmlspecialchars($provider['company_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="plan_id" class="form-label">Ad Plan</label>
                        <select name="plan_id" id="plan_id" class="form-select" required>
                            <option value="">Select a Plan</option>
                            <?php foreach ($plans as $plan): ?>
                                <option value="<?php echo $plan['id']; ?>" data-duration="<?php echo $plan['duration']; ?>"><?php echo htmlspecialchars($plan['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="campaign_name" class="form-label">Campaign Name</label>
                        <input type="text" name="campaign_name" id="campaign_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Subscription</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#addSubscriptionForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '../../src/create_subscription.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload(); // Easiest way to show the new subscription
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('An AJAX error occurred: ' + textStatus + ' - ' + errorThrown);
            }
        });
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>