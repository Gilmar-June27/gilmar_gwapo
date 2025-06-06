<?php
include '../db/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:../login.php');
    exit;
}

// Add Capital Money
if (isset($_POST['add'])) {
    $capital_money = mysqli_real_escape_string($conn, $_POST['capital_money']);
    $deduction_of_capital_money = 0;

    $insert_query = "INSERT INTO total_money (capital_money, deduction_of_capital_money, admin_id) 
                     VALUES ('$capital_money', '$deduction_of_capital_money', '$admin_id')";

    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['message'] = "Capital money added successfully.";
    } else {
        $_SESSION['message'] = "Error: " . mysqli_error($conn);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Delete Capital Money
if (isset($_POST['delete'])) {
    $delete_id = mysqli_real_escape_string($conn, $_POST['delete_id']);
    $delete_query = "DELETE FROM total_money WHERE id = '$delete_id' ";

    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['message'] = "Capital money entry deleted.";
    } else {
        $_SESSION['message'] = "Error deleting entry: " . mysqli_error($conn);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Check for existing money entry
$has_money_query = "SELECT COUNT(*) AS total FROM total_money WHERE admin_id = '$admin_id'";
$has_money_result = mysqli_query($conn, $has_money_query);
$has_money_row = mysqli_fetch_assoc($has_money_result);
$has_money = $has_money_row['total'] > 0;

// Get latest money record
$query = "SELECT * FROM total_money WHERE admin_id = '$admin_id' ORDER BY created_at DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Sum all deductions for this admin
$deduction_sum_query = "SELECT SUM(deduction_of_capital_money) AS total_deduction FROM total_money WHERE admin_id = '$admin_id'";
$deduction_sum_result = mysqli_query($conn, $deduction_sum_query);
$deduction_row = mysqli_fetch_assoc($deduction_sum_result);
$total_deduction = (float)$deduction_row['total_deduction'];

// Define total capital from latest entry
$total_capital = isset($row['capital_money']) ? (float)$row['capital_money'] : 0;

// Calculate remaining money
$remaining_money = $total_capital - $total_deduction;
$remaining_money = $remaining_money < 0 ? 0 : $remaining_money;
?>

<?php @include("header.php") ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php @include("navbar.php") ?>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printable, #printable * {
        visibility: visible;
    }
    #printable {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none;
    }
}
</style>

<div class="container mt-5">
    <div class="az-content-breadcrumb">
        <span>Add Money</span><span>Your Money</span>           
    </div>
    <h2 class="az-content-title mb-4">Your Money</h2>

    <?php if (isset($_SESSION['message'])) { ?>
        <div class="alert alert-info text-center">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php } ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMoneyModal" <?= $has_money ? 'disabled' : ''; ?>>
            Add Money
        </button>
        <?php if ($row) { ?>
        <button onclick="window.print()" class="btn btn-outline-secondary no-print">🖨️ Print</button>
        <?php } ?>
    </div>

    <div class="modal fade" id="addMoneyModal" tabindex="-1" aria-labelledby="addMoneyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Capital Money</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="capital_money">Enter Capital Money:</label>
                        <input type="number" step="0.01" class="form-control" name="capital_money" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="printable">
        <?php if ($row) { ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">Money Summary</h4>
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-light border rounded">
                            <h6>Capital Money</h6>
                            <h4 class="text-success">₱<?= number_format($total_capital, 2); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-light border rounded">
                            <h6>Deduction</h6>
                            <h4 class="text-danger">₱<?= number_format($total_deduction, 2); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-light border rounded">
                            <h6>Total Remaining</h6>
                            <h4 class="text-primary">₱<?= number_format($remaining_money, 2); ?></h4>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3">Added on <?= date("M j, Y", strtotime($row['created_at'])); ?></p>
                <form method="POST" class="no-print" onsubmit="return confirm('Are you sure you want to delete this entry?');">
                    <input type="hidden" name="delete_id" value="<?= $row['id']; ?>">
                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete Entry</button>
                </form>
            </div>
        </div>
        <?php } ?>
    </div><?php

// Fetch deductions where the user is a collector
$deduction_query = "SELECT d.deduction_of_capital_money, d.created_at,
                           CONCAT(u.first_name, ' ', u.last_name) AS collector_name 
                    FROM total_money d 
                    JOIN users u ON d.collector_id = u.id 
                    WHERE d.admin_id = '$admin_id' 
                      AND u.user_type = 'collector'
                    ORDER BY d.created_at DESC";

$deduction_result = mysqli_query($conn, $deduction_query);

// Initialize total
$total_deduction = 0;
?>

<!-- Deduction Table -->
<div class="table-responsive mt-4">
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Collector Name</th>
                <th>Deduction Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($deduction_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($deduction_result)): ?>
                    <?php $total_deduction += floatval($row['deduction_of_capital_money']); ?>
                    <tr>
                        <td><?= htmlspecialchars($row['collector_name']) ?></td>
                        <td class="text-danger">₱<?= number_format($row['deduction_of_capital_money'], 2) ?></td>
                        <td><?= date("F j, Y g:i A", strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
                <!-- Total Row -->
                <tr class="table-success">
                    <td><strong>Total</strong></td>
                    <td class="text-danger"><strong>₱<?= number_format($total_deduction, 2) ?></strong></td>
                    <td></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">No deduction records found for collectors.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


</div>

<?php @include("footer.php") ?>
