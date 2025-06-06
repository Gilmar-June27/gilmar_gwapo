<?php
include '../db/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:./login.php');
    exit;
}

function generateReferenceNumber() {
    // Generate a 6-digit reference number
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function isReferenceNumberUnique($ref_no, $conn) {
    // Check if the reference number already exists in the loan table
    $query = "SELECT COUNT(*) as count FROM loan WHERE ref_no = '$ref_no'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] == 0; // If count is 0, the reference number is unique
}

if (isset($_POST['apply_loan'])) {
  // Auto-generate a unique reference number
  do {
      $ref_no = generateReferenceNumber();
  } while (!isReferenceNumberUnique($ref_no, $conn));

  // Get input values from the form
  $ltype_id = $_POST['ltype_id'];
  $borrower_id = $_POST['borrower_id'];
  $purpose = $_POST['purpose'];
  $amount = $_POST['amount'];
  $lplan_id = $_POST['lplan_id'];
  $status = 0; // default request status
  $date_created = date('Y-m-d H:i:s');
  $date_released = $date_created; // same as created initially
  $admin_id = $_SESSION['admin_id'];

  // 🔍 Fetch collector_id from borrower table
  $collector_query = "SELECT collector_id FROM borrower WHERE borrower_id = '$borrower_id'";
  $collector_result = mysqli_query($conn, $collector_query);
  $collector_id = 0;

  if ($collector_result && mysqli_num_rows($collector_result) > 0) {
      $collector_row = mysqli_fetch_assoc($collector_result);
      $collector_id = $collector_row['collector_id'];
  }

  // 💾 Insert loan into database
  $query = "INSERT INTO loan (ref_no, ltype_id, borrower_id, purpose, amount, lplan_id, status, date_released, date_created, collector_id, admin_id) 
                   VALUES ('$ref_no', '$ltype_id', '$borrower_id', '$purpose', '$amount', '$lplan_id', '$status', '$date_released', '$date_created', '$collector_id', '$admin_id')";

if (mysqli_query($conn, $query)) {
  echo "<script>alert('Loan successfully added!'); window.location.href='';</script>";
} else {
  echo "Error: " . mysqli_error($conn);
}
}


if (isset($_POST['delete'])) {
  $delete_id = $_POST['delete_id'];
  mysqli_query($conn, "DELETE FROM loan WHERE loan_id = '$delete_id' ");
  $_SESSION['message'] = 'loan deleted.';
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}


if (isset($_POST['deny_loan'])) {
  $loan_id = $_POST['loan_id'];
  mysqli_query($conn, "UPDATE loan SET status = 4 WHERE loan_id = $loan_id");

  $loan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT admin_id, collector_id FROM loan WHERE loan_id = $loan_id"));
  $admin_id = $loan['admin_id'];
  $collector_id = $loan['collector_id'];

  $msg = "Your loan has been denied.";
  mysqli_query($conn, "INSERT INTO admin_notification (loan_id, admin_id, collector_id, message) VALUES ($loan_id, $admin_id, $collector_id, '$msg')");
  echo "<script>alert('Loan successfully added!'); window.location.href='';</script>";

}


$limit = isset($_GET['limit']) ? $_GET['limit'] : 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination Logic
$offset = ($page - 1) * $limit;
$query = "SELECT COUNT(*) as total FROM loan WHERE ref_no LIKE '%$search%' OR borrower_id LIKE '%$search%'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total = $row['total'];
$pages = ceil($total / $limit);

$query = "SELECT * FROM loan WHERE ref_no LIKE '%$search%' OR borrower_id LIKE '%$search%' LIMIT $limit OFFSET $offset";
$loan_result = mysqli_query($conn, $query);







?>


<?php @include("header.php"); ?>
<?php @include("navbar.php"); ?>
<style>
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99; }
.modal-content { background: white; margin: 10% auto; padding: 20px; width: 400px; border-radius: 8px; position: relative; }
.modal-close { position: absolute; top: 10px; right: 15px; cursor: pointer; font-weight: bold; }
.btn { padding: 8px 12px; border: none; cursor: pointer; border-radius: 4px; }
.add-btn { background:#3366ff; color: white; margin-bottom: 10px; }
.table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.table th, .table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
.table th { background-color: #f2f2f2; }
.edit-btn { background: #3366ff; color: white; }
.delete-btn { background: red; color: white; }
.search-bar { display: flex; justify-content: space-between; margin: 10px 0; align-items: center; gap: 10px; flex-wrap: wrap; }
.s { display: flex; justify-content: space-between; }
select.form-control {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

</style>
<div class="container mt-4">
<div class="az-content-breadcrumb">
        <span>Junk</span>
        <span>Completed Loan</span>           
    </div>
    <h2 class="az-content-title">Completed Loan</h2>
    <?php if (isset($_SESSION['message'])) { ?>
        <div class="alert alert-info text-center">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php } ?>
<button class="btn add-btn" id="openLoanForm">Add New Loan</button>

<div class="modal" id="loanFormModal">
  <div class="modal-content">
    <span class="modal-close" id="closeLoanForm">&times;</span>
    <h4>Apply for Loan</h4>
    <form method="POST" id="loanForm">
      <div class="row">
        <div class="col-md-6 mb-3" style="display:none">
          <label for="ref_no">Reference Number</label>
          <input type="text" class="form-control" name="ref_no" id="ref_no" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="ltype_id">Loan Type</label>
          <select name="ltype_id" class="form-control" required>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM loan_type");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['ltype_id']}'>{$row['ltype_name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label for="borrower_id">Borrower</label>
          <select name="borrower_id" class="form-control" required>
            <?php
            // JOIN borrower with users table to get collector name
            $query = "SELECT borrower.borrower_id, borrower.tax_id, users.first_name, users.last_name
                      FROM borrower
                      JOIN users ON borrower.collector_id = users.id";

            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $borrowerId = $row['borrower_id'];
                $taxId = $row['tax_id'];
                $collectorName = $row['first_name'] . ' ,' . $row['last_name'];
                echo "<option value='$borrowerId'>$collectorName</option>";
            }
            ?>
          </select>
        </div>
      </div>

    

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="amount">Amount</label>
          <input type="number" class="form-control" name="amount" id="loanAmount" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="lplan_id">Loan Plan</label>
          <select name="lplan_id" id="loanPlanSelect" class="form-control" required>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM loan_plan");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['lplan_id']}' data-months='{$row['lplan_month']}' data-interest='{$row['lplan_interest']}' data-penalty='{$row['lplan_penalty']}'>
                        {$row['lplan_month']} months | Interest: {$row['lplan_interest']}% | Penalty: {$row['lplan_penalty']}%
                      </option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label for="purpose">Loan Purpose</label>
          <textarea class="form-control" name="purpose" rows="4" required></textarea>
        </div>
      </div>

      <button type="button" class="btn add-btn" onclick="calculateLoan()">Calculate</button>

      <div id="loanSummary" style="margin-top: 15px;">
        <p><strong>Total Payable:</strong> ₱<span id="totalPayable">0.00</span></p>
        <p><strong>Monthly Payment:</strong> ₱<span id="monthlyPayable">0.00</span></p>
        <p><strong>Penalty Amount:</strong> ₱<span id="penaltyAmount">0.00</span></p>
      </div>

      <input type="hidden" name="status" value="0">
      <input type="hidden" name="date_created" value="<?= date('Y-m-d H:i:s') ?>">
      <input type="hidden" name="date_released" value="<?= date('Y-m-d H:i:s') ?>">

      <div class="d-flex justify-content-end mt-3">
        <button type="submit" name="apply_loan" class="btn add-btn">Apply</button>
      </div>
    </form>
  </div>
</div>
<div style="text-align: right; margin-bottom: 10px;">
<a href="add-loan.php"  class="btn btn-secondary" onclick="filterLoans('pending')">All</a>
<a href="confirmed_loans.php"  class="btn btn-secondary" onclick="filterLoans('confirm')">Confirmed</a>
 <a href="released_loan.php"  class="btn btn-secondary" onclick="filterLoans('released')">Released</a>
   
   <a href="completed_loan.php"  class="btn btn-primary" onclick="filterLoans('complete')">Completed</a>
   
</div>

<!-- Search and Entries Form -->
<form method="GET" class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            Show 
            <select name="limit" onchange="this.form.submit()">
                <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
            </select>
            entries
        </div>
        <div style="display:flex;justify-content:space-evenly; position: relative;">
            <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-sm btn-primary">Search</button>
        </div>
    </form>






<h4 class="mt-4">Loan Applications</h4>

<table class="table table-striped table-bordered">
  <thead>
  <tr>

            <th>Borrower</th>
            <th>Loan Detail</th>
            <th>Payment Detail</th>
            <th>Status</th>
            <th>Action</th>
    </tr>
  </thead>
  <?php
// Handle "Mark as Paid"
if (isset($_POST['mark_paid'])) {
  $loan_id = $_POST['loan_id'];
  $payee = $_POST['payee'];
  $pay_amount = $_POST['pay_amount'];
  $penalty = $_POST['penalty'];
  $overdue = $_POST['overdue'];

  // ✅ Check if payment already exists for this loan_id
  $check_sql = "SELECT * FROM payment WHERE loan_id = '$loan_id'";
  $check_result = mysqli_query($conn, $check_sql);

  if (mysqli_num_rows($check_result) == 0) {
      // ✅ Insert only if no existing payment
      $insert_payment = "INSERT INTO payment (loan_id, payee, pay_amount, penalty, overdue, is_paid) 
                         VALUES ('$loan_id', '$payee', '$pay_amount', '$penalty', '$overdue', 1)";
      mysqli_query($conn, $insert_payment);

      // ✅ Update loan as paid
      $update_loan = "UPDATE loan SET is_paid = 1 WHERE loan_id = '$loan_id'";
      mysqli_query($conn, $update_loan);
  }
}




// Compute or get from DB
?>


<tbody>
<?php
$query = "SELECT loan.*, 
    lt.ltype_name, 
    lp.lplan_month, lp.lplan_interest, lp.lplan_penalty,
    collector.first_name AS collector_fname, 
    collector.last_name AS collector_lname,
    collector.address AS collector_address,
    collector.number AS collector_number
FROM loan
JOIN loan_type lt ON loan.ltype_id = lt.ltype_id
JOIN loan_plan lp ON loan.lplan_id = lp.lplan_id
JOIN borrower b ON loan.borrower_id = b.borrower_id
JOIN users collector ON b.collector_id = collector.id
WHERE loan.admin_id = '$admin_id' AND
    (collector.first_name LIKE '%$search%' OR collector.last_name LIKE '%$search%' OR loan.ref_no LIKE '%$search%') 
    AND loan.status = 3
ORDER BY loan.date_created DESC
LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $loan_id = $row['loan_id'];
        $total_payable = $row['amount'] + ($row['amount'] * ($row['lplan_interest'] / 100));
        $monthly_payable = $total_payable / $row['lplan_month'];
        $overdue_amount = isset($row['overdue_amount']) ? $row['overdue_amount'] : 0;
        $is_paid = $row['is_paid'] == 1;

        $button_color = $is_paid ? "lightgreen" : "yellow";
        $button_text = $is_paid ? "Paid" : "Unpaid";
        $button_disabled = $is_paid ? "disabled" : "";

        echo "<tr>";

        echo "<td>
            <p><small>Collector Name: <strong>" . htmlspecialchars($row['collector_lname'] . ', ' . $row['collector_fname']) . "</strong></small></p>
            <p><small>Contact: <strong>" . htmlspecialchars($row['collector_number']) . "</strong></small></p>
            <p><small>Address: <strong>" . htmlspecialchars($row['collector_address']) . "</strong></small></p>
        </td>";

        echo "<td>
            <p><small>Reference no: <strong>" . htmlspecialchars($row['ref_no']) . "</strong></small></p>
            <p><small>Loan Type: <strong>" . htmlspecialchars($row['ltype_name']) . "</strong></small></p>
            <p><small>Loan Plan: <strong>{$row['lplan_month']} months [{$row['lplan_interest']}%, {$row['lplan_penalty']}%]</strong> interest, penalty</small></p>
            <p><small>Amount: <strong>&#8369; " . number_format($row['amount'], 2) . "</strong></small></p>
            <p><small>Total Payable Amount: <strong>&#8369; " . number_format($total_payable, 2) . "</strong></small></p>
            <p><small>Monthly Payable Amount: <strong>&#8369; " . number_format($monthly_payable, 2) . "</strong></small></p>
            <p><small>Overdue Payable Amount: <strong>&#8369; " . number_format($overdue_amount, 2) . "</strong></small></p>
        </td>";

        echo "<td><span style='font-weight: bold; color: green;'></span></td>";
        echo "<td><span class='badge badge-primary'>Completed</span></td>";

        echo "<td>
            <form method='post' style='display:inline;'>
                                    <input type='hidden' name='delete_id' value='{$loan_id}'>
                                    <button type='submit' name='delete' style='padding: 5px 10px; background-color: red; color: white;' class='btn delete-btn'>Delete</button>
                                </form>
         

            <form method='POST' style='display:inline-block;'>
                <input type='hidden' name='loan_id' value='{$loan_id}'>
                <input type='hidden' name='payee' value='" . htmlspecialchars($row['payee'] ?? '') . "'>
                <input type='hidden' name='pay_amount' value='{$total_payable}'>
                <input type='hidden' name='penalty' value='{$row['lplan_penalty']}'>
                <input type='hidden' name='overdue' value='0'>
                <button type='submit' name='mark_paid' class='btn' style='background-color: {$button_color};' {$button_disabled}>
                    {$button_text}
                </button>
            </form>
        </td>";

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='9' style='text-align:center;'>No loan records found.</td></tr>";
}
?>
</tbody>


</table>




 <!-- Pagination -->
 <nav>
        <ul class="pagination">
            <?php if($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?search=<?= $search ?>&limit=<?= $limit ?>&page=<?= $page - 1 ?>">Previous</a></li>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= $search ?>&limit=<?= $limit ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if($page < $pages): ?>
                <li class="page-item"><a class="page-link" href="?search=<?= $search ?>&limit=<?= $limit ?>&page=<?= $page + 1 ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

<script>
document.getElementById("loanForm").addEventListener("submit", function(e) {
  const isValid = true; // add validation if needed
  if (!isValid) {
    e.preventDefault();
  }
});



// Open the modal when the button is clicked
document.getElementById("openLoanForm").onclick = function () {
  document.getElementById("loanFormModal").style.display = "block";

  // Generate and set the reference number in the input field
  generateReferenceNumber();
};

// Close the modal when the close button is clicked
document.getElementById("closeLoanForm").onclick = function () {
  document.getElementById("loanFormModal").style.display = "none";
};

// Close the modal if clicked outside
window.onclick = function (event) {
  if (event.target.id === "loanFormModal") {
    document.getElementById("loanFormModal").style.display = "none";
  }
};

// Function to generate and set the reference number in the input field
function generateReferenceNumber() {
  // Send an AJAX request to PHP to get a unique reference number
  fetch('generate_ref_no.php')
    .then(response => response.text())
    .then(data => {
      document.getElementById('ref_no').value = data;
    });
}

function calculateLoan() {
  let amount = parseFloat(document.getElementById("loanAmount").value);
  let plan = document.getElementById("loanPlanSelect");
  let selected = plan.options[plan.selectedIndex];
  let months = parseInt(selected.getAttribute("data-months"));
  let interest = parseFloat(selected.getAttribute("data-interest"));
  let penalty = parseFloat(selected.getAttribute("data-penalty"));

  if (!amount || !months || isNaN(interest) || isNaN(penalty)) return;

  let totalPayable = amount + (amount * (interest / 100));
  let monthlyPayable = totalPayable / months;
  let penaltyAmount = amount * (penalty / 100);

  document.getElementById("totalPayable").innerText = totalPayable.toFixed(2);
  document.getElementById("monthlyPayable").innerText = monthlyPayable.toFixed(2);
  document.getElementById("penaltyAmount").innerText = penaltyAmount.toFixed(2);
}


</script>


<?php @include("footer.php") ?>
