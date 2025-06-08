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
  $ref_no = generateReferenceNumber($conn);
  $ltype_id = $_POST['ltype_id'];
  $borrower_id = $_POST['borrower_id'];
  $purpose = $_POST['purpose'];
  $amount = floatval($_POST['amount']);
  $lplan_id = $_POST['lplan_id'];
  $status = 0;
  $date_created = date('Y-m-d H:i:s');
  $date_released = $date_created;

  // ✅ Get collector_id from borrower
  $borrower_query = "SELECT collector_id FROM borrower WHERE borrower_id = '$borrower_id'";
  $borrower_result = mysqli_query($conn, $borrower_query);

  if ($borrower_result && mysqli_num_rows($borrower_result) > 0) {
      $borrower_row = mysqli_fetch_assoc($borrower_result);
      $collector_id = $borrower_row['collector_id'];

      // ✅ Get capital based on admin
      $money_query = "SELECT capital_money FROM total_money WHERE admin_id = '$admin_id' ORDER BY id DESC LIMIT 1";
      $money_result = mysqli_query($conn, $money_query);

      $capital_money = 0;
      if ($money_result && mysqli_num_rows($money_result) > 0) {
          $money_row = mysqli_fetch_assoc($money_result);
          $capital_money = floatval($money_row['capital_money']);
      }

      // ✅ Get total previous deductions
      $deduct_query = "SELECT SUM(deduction_of_capital_money) AS total_deduction FROM total_money WHERE admin_id = '$admin_id'";
      $deduct_result = mysqli_query($conn, $deduct_query);
      $deduct_row = mysqli_fetch_assoc($deduct_result);
      $total_deduction = floatval($deduct_row['total_deduction'] ?? 0);

      $remaining = $capital_money - $total_deduction;
      if ($remaining < 0) $remaining = 0;

      // ✅ BLOCK IF NO FUNDS
      if ($remaining <= 0) {
        echo '
        <style>
            .custom-alert {
                position: fixed;
                top: -100px;
                left: 50%;
                transform: translateX(-50%);
                background: linear-gradient(135deg, #ff4e4e, #ff7373);
                color: #fff;
                padding: 20px 30px;
                border: 2px solid #c40000;
                border-radius: 10px;
                z-index: 9999;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                font-size: 17px;
                font-weight: 500;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                opacity: 0;
                animation: slideDown 0.6s ease-out forwards;
                display: flex;
                align-items: center;
                gap: 10px;
            }
        
            .custom-alert i {
                font-size: 20px;
            }
        
            @keyframes slideDown {
                0% {
                    top: -100px;
                    opacity: 0;
                }
                100% {
                    top: 30px;
                    opacity: 1;
                }
            }
        </style>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        <div class="custom-alert">
            <i class="fas fa-exclamation-triangle"></i> You can’t add because the remaining is ₱0.
        </div>
        
        <script>
            setTimeout(function() {
                const alertBox = document.querySelector(".custom-alert");
                if (alertBox) {
                    alertBox.style.transition = "opacity 0.5s ease";
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 500);
                }
            }, 3000);
        </script>
        <script>
    setTimeout(function() {
        window.history.back();
    }, 3000); // Wait 3 seconds before going back
</script>
        ';
          exit;
      }

      // ✅ BLOCK IF REQUESTED AMOUNT EXCEEDS REMAINING
      if ($amount > $remaining) {
        echo '
        <style>
            .custom-alert {
                position: fixed;
                top: -100px;
                left: 50%;
                transform: translateX(-50%);
                background: linear-gradient(135deg, #ff4e4e, #ff7373);
                color: #fff;
                padding: 20px 30px;
                border: 2px solid #c40000;
                border-radius: 10px;
                z-index: 9999;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                font-size: 17px;
                font-weight: 500;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                opacity: 0;
                animation: slideDown 0.6s ease-out forwards;
                display: flex;
                align-items: center;
                gap: 10px;
            }
        
            .custom-alert i {
                font-size: 20px;
            }
        
            @keyframes slideDown {
                0% {
                    top: -100px;
                    opacity: 0;
                }
                100% {
                    top: 30px;
                    opacity: 1;
                }
            }
        </style>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        <div class="custom-alert">
            <i class="fas fa-exclamation-triangle"></i> Cannot pay ₱' . $amount . '. Only ₱' . number_format($remaining, 2) . ' remaining.
        </div>
        
        <script>
            setTimeout(function() {
                const alertBox = document.querySelector(".custom-alert");
                if (alertBox) {
                    alertBox.style.transition = "opacity 0.5s ease";
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 500);
                }
            }, 3000);
        </script>
        
        <script>
            setTimeout(function() {
                window.history.back();
            }, 3000);
        </script>
        ';
          exit;
      }

      // ✅ Record deduction
      $new_deductions = $total_deduction + $amount;
      $new_total_money = $capital_money - $new_deductions;

      $insert_money_query = "INSERT INTO total_money (capital_money, collector_id, deduction_of_capital_money, total_money, admin_id)
                             VALUES ('$capital_money', '$collector_id', '$new_deductions', '$new_total_money', '$admin_id')";

      if (!mysqli_query($conn, $insert_money_query)) {
          echo "<script>alert('Error inserting capital deduction.'); window.history.back();</script>";
          exit;
      }

      // ✅ Insert loan
      $insert_query = "INSERT INTO loan (ref_no, ltype_id, borrower_id, purpose, amount, lplan_id, status, date_released, date_created, collector_id, admin_id) 
                       VALUES ('$ref_no', '$ltype_id', '$borrower_id', '$purpose', '$amount', '$lplan_id', '$status', '$date_released', '$date_created', '$collector_id', '$admin_id')";

      if (mysqli_query($conn, $insert_query)) {
          $_SESSION['message'] = 'Loan applied and capital record saved!';
          echo "<script>window.location.href='';</script>";
      } else {
          echo "<script>alert('Error inserting loan.'); window.history.back();</script>";
      }
  } else {
      echo "<script>alert('Borrower not found.'); window.history.back();</script>";
  }
}





if (isset($_POST['delete'])) {
  $delete_id = $_POST['delete_id'];
  mysqli_query($conn, "DELETE FROM loan WHERE loan_id = '$delete_id' ");
  $_SESSION['message'] = 'loan deleted.';
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}


if (isset($_POST['confirm_loan'])) {
  $loan_id = $_POST['loan_id'];
  mysqli_query($conn, "UPDATE loan SET status = 1 WHERE loan_id = $loan_id");

  // Fetch admin_id and collector_id
  $loan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT admin_id, collector_id FROM loan WHERE loan_id = $loan_id"));
  $admin_id = $loan['admin_id'];
  $collector_id = $loan['collector_id'];

  $msg = "Your loan has been confirmed.";
  mysqli_query($conn, "INSERT INTO admin_notification (loan_id, admin_id, collector_id, message) VALUES ($loan_id, $admin_id, $collector_id, '$msg')");
  echo "<script>alert('Loan successfully added!'); window.location.href='';</script>";

}

if (isset($_POST['release_loan'])) {
  $loan_id = $_POST['loan_id'];
  mysqli_query($conn, "UPDATE loan SET status = 2 WHERE loan_id = $loan_id");

  $loan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT admin_id, collector_id FROM loan WHERE loan_id = $loan_id"));
  $admin_id = $loan['admin_id'];
  $collector_id = $loan['collector_id'];
  
  // Loan #$loan_id
  $msg = "Your loan has been released.";
  mysqli_query($conn, "INSERT INTO admin_notification (loan_id, admin_id, collector_id, message) VALUES ($loan_id, $admin_id, $collector_id, '$msg')");
  echo "<script>alert('Loan successfully added!'); window.location.href='';</script>";

}

if (isset($_POST['complete_loan'])) {
  $loan_id = $_POST['loan_id'];
  mysqli_query($conn, "UPDATE loan SET status = 3 WHERE loan_id = $loan_id");

  $loan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT admin_id, collector_id FROM loan WHERE loan_id = $loan_id"));
  $admin_id = $loan['admin_id'];
  $collector_id = $loan['collector_id'];

  $msg = "Your loan has been marked as completed.";
  mysqli_query($conn, "INSERT INTO admin_notification (loan_id, admin_id, collector_id, message) VALUES ($loan_id, $admin_id, $collector_id, '$msg')");
  echo "<script>alert('Loan successfully added!'); window.location.href='';</script>";

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


if (isset($_POST['update_loan'])) {
  $loan_id = $_POST['loan_id'];
  $ref_no = mysqli_real_escape_string($conn, $_POST['ref_no']);
  $borrower_id = $_POST['borrower_id'];
  $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
  $amount = floatval($_POST['amount']);
  $lplan_id = $_POST['lplan_id'];
  $status = $_POST['status'] ?? 0;
  $date_created = $_POST['date_created'];
  $date_released = $_POST['date_released'];

  // ✅ get loan type id from the existing loan (if not being changed)
  $loan_query = "SELECT ltype_id FROM loan WHERE loan_id = '$loan_id'";
  $loan_result = mysqli_query($conn, $loan_query);
  $loan_data = mysqli_fetch_assoc($loan_result);
  $ltype_id = $loan_data['ltype_id'];

  // ✅ get collector from borrower
  $borrower_query = "SELECT collector_id FROM borrower WHERE borrower_id = '$borrower_id'";
  $borrower_result = mysqli_query($conn, $borrower_query);

  if ($borrower_result && mysqli_num_rows($borrower_result) > 0) {
      $borrower_row = mysqli_fetch_assoc($borrower_result);
      $collector_id = $borrower_row['collector_id'];

      // ✅ Get admin ID from session
      $admin_id = $_SESSION['admin_id'];

      // ✅ Get current capital
      $capital_query = "SELECT capital_money FROM total_money WHERE admin_id = '$admin_id' ORDER BY created_at DESC LIMIT 1";
      $capital_result = mysqli_query($conn, $capital_query);
      $capital = 0;
      if ($capital_result && mysqli_num_rows($capital_result) > 0) {
          $capital_row = mysqli_fetch_assoc($capital_result);
          $capital = floatval($capital_row['capital_money']);
      }

      // ✅ Get total deduction
      $deduct_query = "SELECT SUM(deduction_of_capital_money) AS total_deduction FROM total_money WHERE admin_id = '$admin_id'";
      $deduct_result = mysqli_query($conn, $deduct_query);
      $deduct_row = mysqli_fetch_assoc($deduct_result);
      $total_deduction = floatval($deduct_row['total_deduction'] ?? 0);

      // ✅ Calculate remaining money
      $remaining = $capital - $total_deduction;
      if ($remaining < 0) $remaining = 0;

      // ✅ Check if loan amount is allowed
      if ($remaining <= 0) {
        echo '
        <style>
            .custom-alert {
                position: fixed;
                top: -100px;
                left: 50%;
                transform: translateX(-50%);
                background: linear-gradient(135deg, #ff4e4e, #ff7373);
                color: #fff;
                padding: 20px 30px;
                border: 2px solid #c40000;
                border-radius: 10px;
                z-index: 9999;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                font-size: 17px;
                font-weight: 500;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                opacity: 0;
                animation: slideDown 0.6s ease-out forwards;
                display: flex;
                align-items: center;
                gap: 10px;
            }
        
            .custom-alert i {
                font-size: 20px;
            }
        
            @keyframes slideDown {
                0% {
                    top: -100px;
                    opacity: 0;
                }
                100% {
                    top: 30px;
                    opacity: 1;
                }
            }
        </style>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        <div class="custom-alert">
            <i class="fas fa-exclamation-triangle"></i> You can’t add because the remaining is ₱0.
        </div>
        
        <script>
            setTimeout(function() {
                const alertBox = document.querySelector(".custom-alert");
                if (alertBox) {
                    alertBox.style.transition = "opacity 0.5s ease";
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 500);
                }
            }, 3000);
        </script>
        <script>
    setTimeout(function() {
        window.history.back();
    }, 3000); // Wait 3 seconds before going back
</script>
        ';
          exit;
      }

      if ($amount > $remaining) {
        echo '
        <style>
            .custom-alert {
                position: fixed;
                top: -100px;
                left: 50%;
                transform: translateX(-50%);
                background: linear-gradient(135deg, #ff4e4e, #ff7373);
                color: #fff;
                padding: 20px 30px;
                border: 2px solid #c40000;
                border-radius: 10px;
                z-index: 9999;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                font-size: 17px;
                font-weight: 500;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                opacity: 0;
                animation: slideDown 0.6s ease-out forwards;
                display: flex;
                align-items: center;
                gap: 10px;
            }
        
            .custom-alert i {
                font-size: 20px;
            }
        
            @keyframes slideDown {
                0% {
                    top: -100px;
                    opacity: 0;
                }
                100% {
                    top: 30px;
                    opacity: 1;
                }
            }
        </style>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        <div class="custom-alert">
            <i class="fas fa-exclamation-triangle"></i> Cannot pay ₱' . $amount . '. Only ₱' . number_format($remaining, 2) . ' remaining.
        </div>
        
        <script>
            setTimeout(function() {
                const alertBox = document.querySelector(".custom-alert");
                if (alertBox) {
                    alertBox.style.transition = "opacity 0.5s ease";
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 500);
                }
            }, 3000);
        </script>
        
        <script>
            setTimeout(function() {
                window.history.back();
            }, 3000);
        </script>
        ';
          exit;
      }

      // ✅ Proceed to update
      $update_query = "UPDATE loan SET 
          ref_no = '$ref_no',
          ltype_id = '$ltype_id',
          borrower_id = '$borrower_id',
          purpose = '$purpose',
          amount = '$amount',
          lplan_id = '$lplan_id',
          status = '$status',
          date_created = '$date_created',
          date_released = '$date_released',
          collector_id = '$collector_id'
          WHERE loan_id = '$loan_id'";

      if (mysqli_query($conn, $update_query)) {
          $_SESSION['message'] = 'Loan updated successfully!';
          echo "<script>window.location.href='';</script>";
      } else {
          echo "<script>alert('Error updating loan.'); window.location.href='';</script>";
      }
  } else {
      echo "<script>alert('Borrower not found.'); window.location.href='';</script>";
  }
}



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
        <span>Comfirmed Loan</span>           
    </div>
    <h2 class="az-content-title">Comfirmed Loan</h2>
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
<a href="confirmed_loans.php"  class="btn btn-primary" onclick="filterLoans('confirm')">Confirmed</a>
 <a href="released_loan.php"  class="btn btn-secondary" onclick="filterLoans('released')">Released</a>
   
   <a href="completed_loan.php"  class="btn btn-secondary" onclick="filterLoans('complete')">Completed</a>
   
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
<div class="table-responsive">
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
  <tbody>
    <?php
//    $query = "SELECT loan.*, 
//    lt.ltype_name, 
//    lp.lplan_month, lp.lplan_interest, lp.lplan_penalty,
//    u.first_name, u.last_name, u.address, u.number
// FROM loan
// JOIN loan_type lt ON loan.ltype_id = lt.ltype_id
// JOIN loan_plan lp ON loan.lplan_id = lp.lplan_id
// JOIN borrower b ON loan.borrower_id = b.borrower_id
// JOIN users u ON loan.admin_id = u.id
// WHERE loan.admin_id = '$admin_id' AND
//    (u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR loan.ref_no LIKE '%$search%')
// ORDER BY loan.date_created DESC
// LIMIT $limit OFFSET $offset";



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
    (collector.first_name LIKE '%$search%' OR collector.last_name LIKE '%$search%' OR loan.ref_no LIKE '%$search%') AND  loan.status = 1
ORDER BY loan.date_created DESC
LIMIT $limit OFFSET $offset";


    
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
             // Calculate totals
        $total_payable = $row['amount'] + ($row['amount'] * ($row['lplan_interest'] / 100));
        $monthly_payable = $total_payable / $row['lplan_month'];
        $overdue_amount = isset($row['overdue_amount']) ? $row['overdue_amount'] : 0;
            echo "<tr>";
            
            
            echo '<td>
  <p><small>Collector Name: <strong>' . htmlspecialchars($row['collector_lname'] . ', ' . $row['collector_fname']) . '</strong></small></p>
  <p><small>Contact: <strong>' . htmlspecialchars($row['collector_number']) . '</strong></small></p>
  <p><small>Address: <strong>' . htmlspecialchars($row['collector_address']) . '</strong></small></p>
</td>';

              echo '<td>
              <p><small>Reference no: <strong>' . htmlspecialchars($row['ref_no']) . '</strong></small></p>
              <p><small>Loan Type: <strong>' . htmlspecialchars($row['ltype_name']) . '</strong></small></p>
              <p><small>Loan Plan: <strong>' . $row['lplan_month'] . ' months [' . $row['lplan_interest'] . '%, ' . $row['lplan_penalty'] . '%]</strong> interest, penalty</small></p>
            
                <p><small>Amount: <strong>&#8369; ' . number_format($row['amount'], 2) . '</strong></small></p>
                <p><small>Total Payable Amount: <strong>&#8369; ' . number_format($total_payable, 2) . '</strong></small></p>
                <p><small>Monthly Payable Amount: <strong>&#8369; ' . number_format($monthly_payable, 2) . '</strong></small></p>
                <p><small>Overdue Payable Amount: <strong>&#8369; ' . number_format($overdue_amount, 2) . '</strong></small></p>
              </td>';
              echo '<td><span style="font-weight: bold; color: green;"></span></td>';
              // Show status badge
            switch ($row['status']) {
              case 0:
                  echo '<td><span class="badge badge-warning">Pending</span></td>';
                  break;
              case 1:
                  echo '<td><span class="badge badge-info">Approved</span></td>';
                  break;
              case 2:
                  echo '<td><span class="badge badge-success">Released</span></td>';
                  break;
              case 3:
                  echo '<td><span class="badge badge-primary">Completed</span></td>';
                  break;
              case 4:
                  echo '<td><span class="badge badge-danger">Denied</span></td>';
                  break;
          }

          echo '<td>
         
            <form method="post" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="'. $row['loan_id'].'">
                                    <button type="submit" name="delete" style="padding: 5px 10px; background-color: red; color: white;" class="btn delete-btn">Delete</button>
                                </form>';
        
        $status = $row['status'];
        $loan_id = $row['loan_id'];
        
        echo '<form method="POST" action="" id="loanForm" style="display:inline;" onsubmit="disableThis(this)">
                <input type="hidden" name="loan_id" value="' . $loan_id . '">';
        
        if ($status == 0) {
            echo '
                <button type="submit" name="confirm_loan" style="padding: 5px 10px; background-color: green; color: white;" class="btn action-btn">Confirm</button>
                <button type="submit" name="deny_loan" style="padding: 5px 10px; background-color: darkred; color: white;" class="btn action-btn">Deny</button>';
        } elseif ($status == 1) {
            echo '
                <button type="submit" name="release_loan" style="padding: 5px 10px; background-color: orange; color: white;" class="btn action-btn">Release</button>';
        } elseif ($status == 2) {
            echo '
                <button type="submit" name="complete_loan" style="padding: 5px 10px; background-color: blue; color: white;" class="btn action-btn">Complete</button>';
        } elseif ($status == 3) {
            echo '<button disabled style="padding: 5px 10px; background-color: gray; color: white;" class="btn">Completed</button>';
        } elseif ($status == 4) {
            echo '<button disabled style="padding: 5px 10px; background-color: black; color: white;" class="btn">Denied</button>';
        }
        
        echo '</form>';
        echo '</td>';
        

        
        }
    } else {
        echo "<tr><td colspan='9' style='text-align:center;'>No loan records found.</td></tr>";
    }
    ?>
  </tbody>
</table>

</div>


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


// When the user clicks the 'Calculate' button
// function calculateLoan() {
//     // Extract loan plan details from the selected option
//     var lplan = $("#loanPlanSelect option:selected").text();  // Get selected option text
//     var months = parseFloat(lplan.split('months')[0]);  // Extract the number of months
//     var splitter = lplan.split('months')[1];  // Get the remaining text after 'months'
    
//     // Extract interest and penalty values from the text
//     var findinterest = splitter.split('%')[0];  // Extract interest percentage
//     var interest = parseFloat(findinterest.replace(/[^0-9.]/g, ""));  // Clean and convert to float

//     var findpenalty = splitter.split('%')[1];  // Extract penalty percentage
//     var penalty = parseFloat(findpenalty.replace(/[^0-9.]/g, ""));  // Clean and convert to float

//     // Get the amount entered by the user
//     var amount = parseFloat($("#loanAmount").val());

//     // Calculate the total payable amount, monthly payment, and penalty
//     var totalAmount = amount + (amount * (interest / 100));  // Total payable with interest
//     var monthly = totalAmount / months;  // Monthly payment (total amount divided by months)
//     var penaltyAmount = amount * (penalty / 100);  // Calculate penalty amount

//     // Display the calculated results on the page
//     $("#totalPayable").text(totalAmount.toFixed(2));  // Display total payable
//     $("#monthlyPayable").text(monthly.toFixed(2));  // Display monthly payable
//     $("#penaltyAmount").text(penaltyAmount.toFixed(2));  // Display penalty amount
// }

</script>


<?php @include("footer.php") ?>
