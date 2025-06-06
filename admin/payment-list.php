<?php
include '../db/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:./login.php');
    exit;
}
?>
<?php @include("header.php"); ?>
<?php @include("navbar.php"); ?>

<div class="container mt-4">
<div class="az-content-breadcrumb">
        <span>Junk</span>
        <span>Payment Records</span>           
    </div>
    <h2 class="az-content-title">Payment Records</h2>
   

    <?php
    
    $sql = "SELECT * FROM payment ORDER BY date_created DESC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
        <table class="table table-striped table-bordered">
        <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Loan ID</th>
                        
                        <th>Amount</th>
                        <th>Penalty</th>
                        <th>Overdue</th>
                        <th>Is Paid</th>
                        <th>Date Created</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['payment_id']; ?></td>
                            <td><?= $row['loan_id']; ?></td>
                      
                            <td>₱<?= number_format($row['pay_amount'], 2); ?></td>
                            <td>₱<?= number_format($row['penalty'], 2); ?></td>
                            <td>
                                <?= $row['overdue'] ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-success">No</span>'; ?>
                            </td>
                            <td>
                                <?= $row['is_paid'] ? '<span class="badge bg-success">Paid</span>' : '<span class="badge bg-warning">Unpaid</span>'; ?>
                            </td>
                            <td><?= date('F j, Y h:i A', strtotime($row['date_created'])); ?></td>
                            <td>
                                <a href="print_receipt.php?payment_id=<?= $row['payment_id']; ?>" target="_blank" class="btn btn btn-primary">Reciept</a>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No payment records found.</div>
    <?php endif; ?>
</div>

<?php @include("footer.php") ?>
