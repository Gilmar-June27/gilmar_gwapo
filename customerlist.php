<?php 
@include("./db/database.php"); 

session_start();
$collector_id = $_SESSION['collector_id'];



if (!isset($collector_id)) {
  header('Location: login.php');
  exit;
}

// Handle Approve, Decline, and Complete actions for Pickup Requests
// Handle Approve, Decline, and Complete actions for Pickup Requests
if (isset($_POST['approve_request'])) {
    $request_id = (int) $_POST['request_id'];
    
    // Update the status to 'Approved'
    mysqli_query($conn, "UPDATE pickup_requests SET status = 'Approved' WHERE id = $request_id");

    // Fetch customer and collector info for collector_notifications
    $pickup_request = mysqli_fetch_assoc(mysqli_query($conn, "SELECT customer_id FROM pickup_requests WHERE id = $request_id"));
    $customer_id = $pickup_request['customer_id'];
   

    // Send collector_notification
    $msg = "Your pickup request has been approved.";
    mysqli_query($conn, "INSERT INTO collector_notification (customer_id, message) 
                         VALUES ($customer_id, '$msg')");
    
    // echo "<script>alert('Pickup request approved successfully!'); window.location.href='';</script>";
    $_SESSION['message'] = 'Pickup request approved successfully!';
}







if (isset($_POST['decline_request'])) {
    $request_id = (int) $_POST['request_id'];
    
    // Update the status to 'Declined'
    mysqli_query($conn, "UPDATE pickup_requests SET status = 'Declined' WHERE id = $request_id");

    // Fetch customer and collector info for collector_notifications
    $pickup_request = mysqli_fetch_assoc(mysqli_query($conn, "SELECT customer_id FROM pickup_requests WHERE id = $request_id"));
    $customer_id = $pickup_request['customer_id'];
  

    // Send collector_notification
    $msg = "Your pickup request has been declined.";
    mysqli_query($conn, "INSERT INTO collector_notification ( customer_id, message) 
                         VALUES ( $customer_id,  '$msg')");
    
    // echo "<script>alert('Pickup request declined successfully!'); window.location.href='';</script>";
    $_SESSION['message'] = 'Pickup request declined successfully!';
}

if (isset($_POST['complete_request'])) {
  $request_id = (int) $_POST['request_id'];
  $paid = (float) mysqli_real_escape_string($conn, $_POST['paid']);
  $kl = mysqli_real_escape_string($conn, $_POST['kl']);

  // Get collector ID
  $pickup_request = mysqli_fetch_assoc(mysqli_query($conn, "SELECT customer_id, collector_id FROM pickup_requests WHERE id = $request_id"));
  $collector_id = $pickup_request['collector_id'];

  // Fetch latest capital and deduction
  $money_query = "SELECT capital_money, deduction_of_capital_money FROM total_money WHERE collector_id = '$collector_id' ORDER BY created_at DESC LIMIT 1";
  $money_result = mysqli_query($conn, $money_query);
  $money_data = mysqli_fetch_assoc($money_result);

  if ($money_data) {
      $capital = (float) $money_data['capital_money'];
      $deduction = (float) $money_data['deduction_of_capital_money'];
      $remaining = $capital - $deduction;

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

      // Check if paid amount exceeds remaining
      if ($paid > $remaining) {
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
    <i class="fas fa-exclamation-triangle"></i> Cannot pay ₱' . $paid . '. Only ₱' . number_format($remaining, 2) . ' remaining.
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
  }

  // Step 1: Update pickup_requests
  $update_sql = "UPDATE pickup_requests 
                 SET status = 'Completed', 
                     paid = '$paid', 
                     kl = '$kl', 
                     paid_at = NOW() 
                 WHERE id = $request_id";
  mysqli_query($conn, $update_sql);

  // Step 2: Update deduction in total_money
  $update_deduction_sql = "UPDATE total_money 
                           SET deduction_of_capital_money = deduction_of_capital_money + $paid 
                           WHERE collector_id = '$collector_id'";
  mysqli_query($conn, $update_deduction_sql);

  // Redirect or show success
   // Step 7: Redirect with session message
  $_SESSION['message'] = 'Pickup request completed and deduction saved!';
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}






// if (isset($_POST['complete_request'])) {
//   $request_id = (int) $_POST['request_id'];
//   $paid = mysqli_real_escape_string($conn, $_POST['paid']);
//   $kl = mysqli_real_escape_string($conn, $_POST['kl']);

//   // Update the status, paid, kl, and paid_at
//   $update_sql = "UPDATE pickup_requests 
//                  SET status = 'Completed', 
//                      paid = '$paid', 
//                      kl = '$kl', 
//                      paid_at = NOW() 
//                  WHERE id = $request_id";
//   mysqli_query($conn, $update_sql);

//   // Notify customer
//   $pickup_request = mysqli_fetch_assoc(mysqli_query($conn, "SELECT customer_id FROM pickup_requests WHERE id = $request_id"));
//   $customer_id = $pickup_request['customer_id'];

//   $msg = "Your pickup request has been marked as completed.";
//   mysqli_query($conn, "INSERT INTO collector_notification (customer_id, message) 
//                        VALUES ($customer_id, '$msg')");

//   $_SESSION['message'] = 'Pickup request marked as completed!';
//   header("Location: ".$_SERVER['PHP_SELF']);
//   exit;
// }







// Pagination & Search logic
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM pickup_requests WHERE collector_id='$collector_id'";
if ($search != '') {
    $count_sql .= " AND (name LIKE '%$search%' OR contact_number LIKE '%$search%' OR junk_type LIKE '%$search%' OR address LIKE '%$search%')";
}
$total_result = mysqli_query($conn, $count_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Fetch filtered & paginated data
$query = "SELECT * FROM pickup_requests WHERE collector_id='$collector_id'";
if ($search != '') {
    $query .= " AND (name LIKE '%$search%' OR contact_number LIKE '%$search%' OR junk_type LIKE '%$search%' OR address LIKE '%$search%')";
}
$query .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$requests = mysqli_query($conn, $query);





// Handle Complete (using JavaScript modal instead of redirect)
// if (isset($_POST['complete_request'])) {
//     $request_id = (int) $_POST['request_id'];
    
//     // Update status
//     mysqli_query($conn, "UPDATE pickup_requests SET status = 'Completed' WHERE id = $request_id");

//     // Get info
//     $pickup_request = mysqli_fetch_assoc(mysqli_query($conn, "SELECT customer_id FROM pickup_requests WHERE id = $request_id"));
//     $customer_id = $pickup_request['customer_id'];
//   

//     // Send collector_notification
//     $msg = "Your pickup request has been marked as completed.";
//     mysqli_query($conn, "INSERT INTO collector_notification (customer_id, message) 
//                          VALUES ($customer_id,  '$msg')");
    
//     // Set a flag in JavaScript to trigger modal
//     echo "<script>window.onload = function() { $('#completedModal').modal('show'); };</script>";
// }





if (isset($_POST['add_documentation'])) {
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);
    $customer_id = (int) $_POST['customer_id'];
    $collector_id = (int) $_POST['collector_id'];

    // Handle image upload
    $image_path = '';
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = 'uploads/' . $image_name;

        if (!move_uploaded_file($image_tmp, $image_path)) {
            echo "<script>alert('Failed to upload image');</script>";
            return;
        }
    }

    // Insert into documentation table
    $insert = "INSERT INTO documentation (description, review, image, collector_id, customer_id) 
               VALUES ('$description', '$review', '$image_path', '$collector_id',  $customer_id)";
    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('Documentation added successfully!'); window.location.href='';</script>";
        header("location:postcateg.php");
        exit;
    } else {
        //
        $_SESSION['message'] = 'Failed to save documentation.';
    }
}

?>
<?php
 @include("header.php");
 @include("navbar.php");
?>
<div class="container mt-5">
<div class="az-content-breadcrumb">
            <span>Junk</span>
            <span>Pickup Requests</span>           
          </div>
          <h2 class="az-content-title">Pickup Requests</h2>
          <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info text-center">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div style="text-align: right; margin-bottom: 10px;">
   <a href="customerlist.php"  class="btn btn-primary" onclick="filterLoans('pending')">All</a>
   <a href="pending.php"  class="btn btn-secondary" onclick="filterLoans('pending')">Pending</a>
   <a href="approved.php"  class="btn btn-secondary" onclick="filterLoans('confirm')">Approved</a>
   <a href="completed.php"  class="btn btn-secondary" onclick="filterLoans('complete')">Completed</a>
   
</div>


    <form method="GET" class="mb-3 d-flex justify-content-between align-items-center">
    <div>
            <select name="limit" class="form-select" onchange="this.form.submit()">
                <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5 entries</option>
                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 entries</option>
                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20 entries</option>
            </select>
        </div>
        <div  style="display:flex;justify-content:space-evenly; position: relative;">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">&nbsp;
            <button type="submit" class="btn btn-primary">Apply</button>
        </div>
      
        
    </form>
    <div class="table-responsive">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Junk Type</th>
                <th>Garbage Kg/G</th>
                <th>Description</th>
                <th>Preferred Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($requests) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($requests)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['contact_number'] ?></td>
                    <td><span style='color:blue;cursor:pointer;text-decoration:underline;' onclick="redirectToMap('<?= $row['address'] ?>')"><?= $row['address'] ?></span></td>
                    <td><?= $row['junk_type'] ?></td>
                    <td><?= $row['kl'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><?= $row['preferred_date'] ?></td>
                    <td>
                    <span class='badge bg-<?= 
    $row['status'] === 'Pending' ? 'warning text-dark' : 
    ($row['status'] === 'Approved' ? 'success' : 
    ($row['status'] === 'Declined' ? 'danger' : 'primary')) 
?>'><?= $row['status'] ?></span>

            </td>
            <td>
  <form method="post">
    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">

    <!-- Approve Button -->
    <button type="submit" name="approve_request" class="btn btn-success "
      <?= ($row['status'] === 'Approved' || $row['status'] === 'Completed') ? 'disabled' : '' ?>>
      Approve
    </button>

    <!-- Decline Button -->
    <button type="submit" name="decline_request" class="btn btn-danger "
    <?= ($row['status'] === 'Declined' || $row['status'] === 'Completed' || $row['status'] === 'Declined') ? 'disabled' : '' ?>
      >
      Decline
    </button>

    <!-- Complete Button -->
    <form method="post" class="d-inline-block">
    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
    <button type="button" class="btn btn-primary"
        data-bs-toggle="modal" 
        data-bs-target="#completeModal<?= $row['id'] ?>"
        <?= ($row['status'] === 'Completed' || $row['status'] === 'Declined') ? 'disabled' : '' ?>>
        Complete
    </button>
</form>
<!-- Modal -->
<div class="modal fade" id="completeModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="completeModalLabel<?= $row['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post">
      <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="completeModalLabel<?= $row['id'] ?>">Complete Pickup Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="paid" class="form-label">Paid Amount</label>
            <input type="text" class="form-control" name="paid" required>
          </div>
          <div class="mb-3">
            <label for="kl" class="form-label">Weight (KG / G)</label>
            <input type="text" class="form-control" name="kl" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="complete_request" class="btn btn-success">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Generate Documentation Button -->
<!-- <button type="button" class="btn btn-secondary "  data-bs-toggle="modal" data-bs-target="#completeModal"
data-customer-id="<?= $row['customer_id'] ?>">
  Generate Documentation
</button> -->

<button 
  type="button" 
  class="btn btn-secondary"
  data-bs-toggle="modal" 
  data-bs-target="#completeModal"
  data-customer-id="<?= $row['customer_id'] ?>"
  <?= ($row['status'] !== 'Completed') ? 'disabled title="Complete the request first."' : '' ?>>
  Generate Documentation
</button>



<!-- Modal -->


  </form>






</td>

                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-center">No requests found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<!-- Modal for Adding Documentation -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="completeModalLabel">Add Documentation for Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" enctype="multipart/form-data">

        <p>marked as completed. Do you want to generate documentation?</p>
          <input type="hidden" name="customer_id" id="customer_id" value="">
          <input type="hidden" name="collector_id" value="<?= $collector_id ?>">

          <div class="mb-3" style="opacity:0">
            <label for="description" class="form-label">Description</label>
            <input type="hidden" class="form-control" id="description" name="description" rows="3" >
          </div>

          <div class="mb-3" style="opacity:0">
            <label for="review" class="form-label">Review</label>
            <input type="hidden"  class="form-control" id="review" name="review" >
          </div>

          <div class="mb-3" style="opacity:0">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image">
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" name="add_documentation">Save Documentation</button>
            
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

    <!-- Pagination Links -->
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&search=<?= $search ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>&search=<?= $search ?>"><?= $i ?></a></li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&search=<?= $search ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>


<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.forEach(function (tooltipTriggerEl) {
  new bootstrap.Tooltip(tooltipTriggerEl);
});




document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('completeForm');
  const triggerButton = document.querySelectorAll('.c');
  const modal = new bootstrap.Modal(document.getElementById('completeModal'));

  // Intercept initial submit
  triggerButton.addEventListener('click', function (e) {
    e.preventDefault(); // prevent default submit
    modal.show();       // show modal instead
  });
});



  // JavaScript to handle setting the customer_id when the modal is opened
  var completeButtons = document.querySelectorAll('[data-bs-target="#completeModal"]');
  completeButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      var customerId = this.getAttribute('data-customer-id');
      document.getElementById('customer_id').value = customerId;
    });
  });


function redirectToMap(address) {
    localStorage.setItem("mapPlace", address);
    window.location.href = "index.php";
}
</script>

<?php @include("footer.php"); ?>
