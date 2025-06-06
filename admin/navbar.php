<?php
include '../db/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:./login.php');
    exit;
}


$select = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$admin_id'") or die('Query failed');
if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
}
?>
<div class="az-header">
      <div class="container">
        <div class="az-header-left">
          <a href="index.php" class="az-logo"><span></span> <img src="1.png" alt="" srcset="" style="width:81px;margin-top: 13px;filter: invert(62%) sepia(16%) saturate(1632%) hue-rotate(109deg) brightness(100%) contrast(82%);">
          <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
        </div><!-- az-header-left -->
        <div class="az-header-menu">
          <div class="az-header-menu-header">
            <a href="index.php" class="az-logo"><span></span> <img src="1.png" alt="" srcset="" style="width: 81px;margin-top: 13px;filter: invert(62%) sepia(16%) saturate(1632%) hue-rotate(109deg) brightness(100%) contrast(82%);">
            <a href="" class="close">&times;</a>
          </div><!-- az-header-menu-header -->
          <ul class="nav">
            <li class="nav-item active">
              <a href="index.php" class="nav-link"><i class="typcn typcn-chart-area-outline"></i> Dashboard</a>
            </li>
           <li class="nav-item">
              <a href="" class="nav-link with-sub"><i class="typcn typcn-attachment-outline"></i> loan</a>
              <div class="az-menu-sub">
                <div class="container">
                  <div>
                    <nav class="nav">
                      <a href="add-loan.php" class="nav-link"><i class="typcn typcn-upload-outline"></i> Add Loan</a>
                      <!-- <a href="confirmed_loans.php" class="nav-link"><i class="typcn typcn-upload-outline"></i> Confirm Loan</a>
                      <a href="released_loan.php" class="nav-link"><i class="typcn typcn-upload-outline"></i> Released Loan</a>
                      <a href="completed_loan.php" class="nav-link"><i class="typcn typcn-upload-outline"></i> Completed Loan</a> -->
                      <a href="borrower.php" class="nav-link"><i class="typcn typcn-archive"></i> Borrower</a>
                    
                      <a href="loan-plan.php" class="nav-link">	<i class="typcn typcn-home-outline"></i> Loan Plan</a>
                      <a href="loan-type.php" class="nav-link"><i class="typcn typcn-document-text"></i> Loan Type</a>
                      <a href="payment-list.php" class="nav-link"><i class="typcn typcn-credit-card"></i> Payment List</a>
                      <a href="add-collector.php" class="nav-link"><i class="typcn typcn-user"></i> Add Collector</a>
                    </nav>
                  </div>
                </div><!-- container -->
              </div>
            </li>
            <li class="nav-item">
              <a href="all.php" class="nav-link"><i class="typcn typcn-document"></i>Junk Pricing</a>
              <!-- <nav class="az-menu-sub">
                <a href="category/all.php" class="nav-link">All</a>
                <a href="category/plastic.php" class="nav-link">Plastic</a>
                <a href="category/metal.php" class="nav-link">Metal</a>
              </nav> -->
            </li>
            <li class="nav-item">
              <a href="documentation.php" class="nav-link"><i class="typcn typcn-chart-bar-outline"></i> Documentation</a>
            </li>
            <!-- <li class="nav-item">
              <a href="adminlist.php" class="nav-link"><i class="typcn typcn-book"></i> User</a>
            </li> -->

          </ul>
        </div><!-- az-header-menu -->
        <div class="az-header-right">
        <!-- az-header-message -->
          <div class="dropdown az-header-notification">
            <a href="" class="new"><i class="typcn typcn-bell"></i></a>
            <div class="dropdown-menu">
              <div class="az-dropdown-header mg-b-20 d-sm-none">
                <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
              </div>
              <h6 class="az-notification-title">Notifications</h6>
              <p class="az-notification-text">You have 2 unread notification</p>
              <div class="az-notification-list">
                <div class="media new">
                  <div class="az-img-user"><img src="../../img/faces/face2.jpg" alt=""></div>
                  <div class="media-body">
                    <p>Congratulate <strong>Socrates Itumay</strong> for work anniversaries</p>
                    <span>Mar 15 12:32pm</span>
                  </div><!-- media-body -->
                </div><!-- media -->
               
              </div><!-- az-notification-list -->
              <div class="dropdown-footer"><a href="">View All Notifications</a></div>
            </div><!-- dropdown-menu -->
          </div><!-- az-header-notification -->
          <div class="dropdown az-profile-menu">
            <a href="" class="az-img-user">
            <img src="../images/<?php echo empty($fetch['image']) ? '1.png' : $fetch['image']; ?>">
            </a>
            <div class="dropdown-menu">
              <div class="az-dropdown-header d-sm-none">
                <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
              </div>
              <div class="az-header-profile">
                <div class="az-img-user">
                <?php
                                        // If notification has admin_id, it's admin, else customer
                                        if ($notification['admin_id'] > 0) {
                                            echo '<img src="../images/' . ($notification['admin_image'] ?? '1.png') . '" alt="Admin">';
                                        } else {
                                            echo '<img src="../images/' . ($notification['customer_image'] ?? '1.png') . '" alt="Customer">';
                                        }
                                        ?>
                </div><!-- az-img-user -->
                <h6><?php echo htmlspecialchars($fetch['first_name']); ?></h6>
                <span><?php echo htmlspecialchars($fetch['first_name']); ?></span>
              </div><!-- az-header-profile -->

              <a href="#" class="dropdown-item"><i class="typcn typcn-user-outline"></i> My Profile</a>
              <a href="profile.php" class="dropdown-item"><i class="typcn typcn-edit"></i> Edit Profile</a>
              <a href="./../logout.php" class="dropdown-item"><i class="typcn typcn-power-outline"></i>Sign Out </a>
            </div><!-- dropdown-menu -->
          </div>
        </div><!-- az-header-right -->
      </div><!-- container -->
    </div><!-- az-header -->

