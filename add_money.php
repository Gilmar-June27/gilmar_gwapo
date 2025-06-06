<?php
include './db/database.php';
session_start();

$collector_id = $_SESSION['collector_id'];

if (!isset($collector_id)) {
    header('location:./login.php');
    exit;
}

// Add Capital Money
if (isset($_POST['add'])) {
    $capital_money = mysqli_real_escape_string($conn, $_POST['capital_money']);
    $deduction_of_capital_money = 0;

    $insert_query = "INSERT INTO total_money (capital_money, deduction_of_capital_money, collector_id) 
                     VALUES ('$capital_money', '$deduction_of_capital_money', '$collector_id')";

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
    $delete_query = "DELETE FROM total_money WHERE  collector_id = '$collector_id'";

    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['message'] = "Capital money entry deleted.";
    } else {
        $_SESSION['message'] = "Error deleting entry: " . mysqli_error($conn);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Check for existing money entry
$has_money_query = "SELECT COUNT(*) AS total FROM total_money WHERE collector_id = '$collector_id'";
$has_money_result = mysqli_query($conn, $has_money_query);
$has_money_row = mysqli_fetch_assoc($has_money_result);
$has_money = $has_money_row['total'] > 0;

$query = "SELECT * FROM total_money WHERE collector_id = '$collector_id' ORDER BY created_at DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
?>

<?php @include("header.php") ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php @include("navbar.php") ?>

<!-- Print Styles -->
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
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php } ?>

    <!-- Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMoneyModal" <?= $has_money ? 'disabled' : ''; ?>>
            Add Money
        </button>
        <?php if ($row) { ?>
        <button onclick="window.print()" class="btn btn-outline-secondary no-print">🖨️ Print</button>
        <?php } ?>
    </div>

    <!-- Add Money Modal -->
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

    <!-- Printable Section -->
    <div id="printable">
        <?php if ($row) { ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">Money Summary</h4>
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-light border rounded">
                            <h6>Capital Money</h6>
                            <h4 class="text-success">₱<?= number_format($row['capital_money'], 2); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-light border rounded">
                            <h6>Deduction</h6>
                            <h4 class="text-danger">₱<?= number_format($row['deduction_of_capital_money'], 2); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-light border rounded">
                            <h6>Total Remaining</h6>
                            <!-- <h4 class="text-primary">₱<?= number_format((float)($row['total_money'] ?? 0), 2); ?></h4> -->
                            <?php
                                $capital = (float)$row['capital_money'];
                                $deduction = (float)$row['deduction_of_capital_money'];
                                $remaining = $capital - $deduction;
                                $remaining = $remaining < 0 ? 0 : $remaining;
                            ?>
                            <h4 class="text-primary">₱<?= number_format($remaining, 2); ?></h4>

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
  

    <!-- Loans Section -->
    <?php
    $query = "SELECT * FROM loan WHERE collector_id='$collector_id' AND status = 1 ORDER BY date_created DESC";
    $result = mysqli_query($conn, $query);
    $total_amount = 0;
    ?>
    <h4 class="mt-5 mb-4">Confirmed Loans</h4>
    
    <div class="row">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($loan = mysqli_fetch_assoc($result)) {
            $total_amount += $loan['amount'];
    ?>
     <h3 class="mt-4 text-primary">Total Confirmed Loan Amount: ₱<?= number_format($total_amount, 2); ?></h3>
  
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 border-0 shadow-lg rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title text-primary mb-0">
                        <i class="fas fa-receipt me-2"></i>Ref #: <?= htmlspecialchars($loan['ref_no']); ?>
                    </h5>
                    <span class="badge bg-success">Confirmed</span>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <strong><i class="fas fa-bullseye me-2 text-secondary"></i>Purpose:</strong>
                        <?= htmlspecialchars($loan['purpose']); ?>
                    </li>
                    <li class="mb-2">
                        <strong><i class="fas fa-money-bill-wave me-2 text-secondary"></i>Amount:</strong>
                        ₱<?= number_format($loan['amount'], 2); ?>
                    </li>
                    <li class="mb-2">
                        <strong><i class="fas fa-layer-group me-2 text-secondary"></i>Loan Type ID:</strong>
                        <?= $loan['ltype_id']; ?>
                    </li>
                    <li class="mb-2">
                        <strong><i class="fas fa-user me-2 text-secondary"></i>Borrower ID:</strong>
                        <?= $loan['borrower_id']; ?>
                    </li>
                    <li>
                        <strong><i class="fas fa-calendar-alt me-2 text-secondary"></i>Released:</strong>
                        <?= date("M j, Y", strtotime($loan['date_released'])); ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php
        }
    } else {
        echo '<div class="col-12 text-center text-muted py-4 fs-5">No confirmed loans found.</div>';
    }
    ?>
</div>

 <!-- Signature Area -->
 <!-- <div class="mt-5">
            <h5>Collector Signature:</h5>
            <div style="border-top: 1px solid #000; width: 250px; margin-top: 40px;"></div>
            <p class="mt-2"><?= $_SESSION['collector_name'] ?? '________________' ?></p>
        </div> -->
     </div>
</div>

<?php @include("footer.php") ?>
