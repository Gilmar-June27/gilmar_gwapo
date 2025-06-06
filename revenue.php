<?php @include("header.php")?>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php @include("navbar.php")?>


<div class="az-content az-content-dashboard">
    <div class="container">

        <div class="az-content-body">
        <div class="az-content-breadcrumb">
            <span>Junk</span>
            <span>Dashboard</span>           
          </div>
          <h2 class="az-content-title">Dashboard</h2>
            <div class="az-dashboard-one-title">
            
               <div class="col-lg-5 mg-t-20 mg-lg-t-0">
               <a href="add_money.php" class="m-3 btn btn-primary">Add Money</a>
              <div class="row row-sm">
                <div class="col-sm-6">
                  <div class="card card-dashboard-two">
                    <div class="card-header">
                      
                    <?php
                      $completed_query = mysqli_query($conn, "SELECT * FROM `pickup_requests` WHERE status = 'Completed' AND collector_id = $collector_id") or die('query failed');
                      $completed_count = mysqli_num_rows($completed_query);

                      // Count all pickup requests for this collector
                      // $total_query = mysqli_query($conn, "SELECT * FROM `pickup_requests` WHERE collector_id = $collector_id") or die('query failed');
                      // $total_count = mysqli_num_rows($total_query);

                      $Approved_query = mysqli_query($conn, "SELECT * FROM `pickup_requests` WHERE status = 'Approved' AND collector_id = $collector_id") or die('query failed');
                      $Approved_count = mysqli_num_rows($Approved_query);

                      // Count all pickup requests for this collector
                      $total_query = mysqli_query($conn, "SELECT * FROM `pickup_requests` WHERE collector_id = $collector_id") or die('query failed');
                      $total_count = mysqli_num_rows($total_query);

                      // Calculate percentage
                      $completed_percentage = $total_count > 0 ? ($completed_count / $total_count) * 100 : 0;
                      $Approved_percentage = $total_count > 0 ? ($Approved_count / $total_count) * 100 : 0;
                      ?>
                      <h6> <?php echo number_format($Approved_percentage, 2); ?>% <i class="icon ion-md-trending-up tx-success"></i> <small><?php echo number_format($completed_percentage, 2); ?>%</small></h6>
                      <p>Pay Customer</p>
                    </div><!-- card-header -->
                    <div class="card-body">
                      <div class="chart-wrapper">
                        <div id="flotChart1" class="flot-chart"></div>
                      </div><!-- chart-wrapper -->
                    </div><!-- card-body -->
                  </div><!-- card -->
                </div><!-- col -->
                <div class="col-sm-6 mg-t-20 mg-sm-t-0">
                  <div class="card card-dashboard-two">
                    <div class="card-header">
                    <?php
// Get total capital money
$capital_query = mysqli_query($conn, "SELECT SUM(capital_money) AS total_capital FROM total_money WHERE collector_id = '$collector_id'") or die('Query failed');
$capital_result = mysqli_fetch_assoc($capital_query);
$total_capital = $capital_result['total_capital'] ?? 0;

// Get total deduction
$deduction_query = mysqli_query($conn, "SELECT SUM(deduction_of_capital_money) AS total_deduction FROM total_money WHERE collector_id = '$collector_id'") or die('Query failed');
$deduction_result = mysqli_fetch_assoc($deduction_query);
$total_deduction = $deduction_result['total_deduction'] ?? 0;
?>
<h6>
  ₱<?php echo number_format($total_capital, 0); ?> 
  <i class="icon ion-md-trending-down tx-danger"></i> 
  <small>Deducted: ₱<?php echo number_format($total_deduction, 0); ?></small>
</h6>


<p>Total Money</p>


                    </div><!-- card-header -->
                    <div class="card-body">
                      <div class="chart-wrapper">
                        <div id="flotChart2" class="flot-chart"></div>
                      </div><!-- chart-wrapper -->
                    </div><!-- card-body -->
                  </div><!-- card -->
                </div><!-- col -->
                <div class="col-sm-12 mg-t-20">
                  <div class="card card-dashboard-three">
                    <div class="card-header">
                      <p>All Sessions</p>
                      
                      <?php 
$select_customer_active = mysqli_query($conn, "SELECT * FROM users WHERE status = 'activate' AND user_type='customer'") or die('query failed');
$number_of_customer_active = mysqli_num_rows($select_customer_active);

$select_customer_deactive = mysqli_query($conn, "SELECT * FROM users WHERE status = 'deactivate' AND user_type='customer'") or die('query failed');
$number_of_customer_deactive = mysqli_num_rows($select_customer_deactive);

// Calculate total customers
$total_customers = $number_of_customer_active + $number_of_customer_deactive;

// Calculate percentages
$active_percentage = $total_customers > 0 ? ($number_of_customer_active / $total_customers) * 100 : 0;
$deactive_percentage = $total_customers > 0 ? ($number_of_customer_deactive / $total_customers) * 100 : 0;
?>

                      
                      <h6><?php //echo $number_of_customer_active; ?><?php echo number_format($active_percentage, 1); ?>% <small class="tx-success"><i class="icon ion-md-arrow-up"></i> <?php //echo $number_of_customer_deactive; ?><?php echo number_format($deactive_percentage, 1); ?>%</small></h6>
                      <small>The total number of sessions within the date range. It is the period time a customer is actively engaged to collect their junk.</small>
                    </div><!-- card-header -->
                    <div class="card-body">
                      <div class="chart"><canvas id="chartBar5"></canvas></div>
                    </div>
                  </div>
                </div>
              </div><!-- row -->
            </div><!--col -->



            <div class="col-lg-7 col-xl-8 mg-t-20 mg-lg-t-0">
              <div class="card card-table-one">
                <h6 class="card-title">Transaction Completed</h6>
                <p class="az-content-text mg-b-20">Customer Done Collecting Junk</p>
                <div class="table-responsive">
                <?php
// Entries per page
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Base query with filter
$search_query = "";
if (!empty($search)) {
    $search_query = " AND (u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR pr.junk_type LIKE '%$search%' OR pr.description LIKE '%$search%')";
}

// Count total rows
$count_result = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM pickup_requests pr 
    JOIN users u ON pr.customer_id = u.id 
    WHERE pr.status = 'Completed' AND pr.collector_id = $collector_id $search_query
");
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch actual data
$query = mysqli_query($conn, "
    SELECT pr.*, u.first_name, u.last_name, u.image
    FROM pickup_requests pr
    JOIN users u ON pr.customer_id = u.id
    WHERE pr.status = 'Completed' AND pr.collector_id = $collector_id $search_query
    ORDER BY pr.id DESC
    LIMIT $offset, $limit
");
?>

<!-- Filter Controls -->
<form method="get" class="row mb-3">
  <div class="col-md-3">
    <select name="limit" class="form-select" onchange="this.form.submit()">
      <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5 entries</option>
      <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 entries</option>
      <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25 entries</option>
    </select>
  </div>
  <div class="col-md-5"></div>

 <form method="GET" class="row g-2 mb-3">
  <div class="col-md-4">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search..." onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary w-100">Search</button>
  </div>
</form>

</form>

<!-- Data Table -->
<table class="table">
  <thead>
    <tr>
      <th>#</th>
      <th>Customer</th>
      <th>Junk Category</th>
      <th>Junk Kg</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $counter = $offset + 1;
    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $pickup_id = $row['id'];
            $full_name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
            $image = !empty($row['image']) ? htmlspecialchars($row['image']) : '1.png';
            $junk_type = htmlspecialchars($row['junk_type']);
            $desc = htmlspecialchars($row['kl']);
            $address = htmlspecialchars($row['address']);
            $contact = htmlspecialchars($row['contact_number']);
            $date =  date("M j, Y", strtotime($row['preferred_date']));
            
            ?>
            <tr>
              <td><?= $counter++; ?></td>
              <td>
                <img src="images/<?= $image ?>" alt="User" style="width:30px; height:30px; border-radius:50%; object-fit:cover; margin-right:8px;">
                <strong><?= $full_name ?></strong>
              </td>
              <td><?= $junk_type ?></td>
              <td><?= $desc ?></td>
              <td>
                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal<?= $pickup_id ?>">
                  View
                </button>
              </td>
            </tr>

            <!-- Modal -->
            <div class="modal fade" id="modal<?= $pickup_id ?>" tabindex="-1" aria-labelledby="modalLabel<?= $pickup_id ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel<?= $pickup_id ?>">Pickup Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="d-flex align-items-center mb-3">
                      <img src="images/<?= $image ?>" alt="Customer Image" style="width:50px; height:50px; border-radius:50%; object-fit:cover; margin-right:10px;">
                      <strong><?= $full_name ?></strong>
                    </div>
                    <p><strong>Junk Category:</strong> <?= $junk_type ?></p>
                    <p><strong>Junk Kg:</strong> <?= $desc ?></p>
                    <p><strong>Address:</strong> <?= $address ?></p>
                    <p><strong>Contact Number:</strong> <?= $contact ?></p>
                    <p><strong>Preferred Date:</strong> <?= $date ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-success"><?= $row['status'] ?></span></p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
            <?php
        }
    } else {
        echo '<tr><td colspan="5" class="text-center">No completed pickups found.</td></tr>';
    }
    ?>
  </tbody>
</table>

<!-- Pagination -->
<nav class="m-3">
  <ul class="pagination justify-content-end">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <li class="page-item <?= $i == $page ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>



                </div><!-- table-responsive -->
              </div><!-- card -->
            </div><!-- col-lg -->
            </div>
        </div>
    </div>
</div>

<?php @include("footer.php") ?>
