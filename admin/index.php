

<?php @include("header.php")?>
<?php @include("navbar.php")?>

    <div class="az-content az-content-dashboard">
      <div class="container">
        <div class="az-content-body">
          <div class="az-dashboard-one-title">
            <div>
              <h2 class="az-dashboard-title">Hi, welcome Admin!</h2>
              
            </div>
            <!-- <div class="az-content-header-right">
              <div class="media">
                <div class="media-body">
                  <label>Start Date</label>
                  <h6>Oct 10, 2018</h6>
                </div>
              </div>
              <div class="media">
                <div class="media-body">
                  <label>End Date</label>
                  <h6>Oct 23, 2018</h6>
                </div>
              </div>
              <div class="media">
                <div class="media-body">
                  <label>Event Category</label>
                  <h6>All Categories</h6>
                </div>
              </div>
              <a href="" class="btn btn-purple">Export</a>
            </div> -->
          </div><!-- az-dashboard-one-title -->

          <div class="az-dashboard-nav"> 
            <!-- <nav class="nav">
              <a class="nav-link active" data-toggle="tab" href="#">Overview</a>
              <a class="nav-link" data-toggle="tab" href="#">Audiences</a>
              <a class="nav-link" data-toggle="tab" href="#">Demographics</a>
              <a class="nav-link" data-toggle="tab" href="#">More</a>
            </nav> -->

            <!-- <nav class="nav">
              <a class="nav-link" href="#"><i class="far fa-save"></i> Save Report</a>
              <a class="nav-link" href="#"><i class="far fa-file-pdf"></i> Export to PDF</a>
              <a class="nav-link" href="#"><i class="far fa-envelope"></i>Send to Email</a>
              <a class="nav-link" href="#"><i class="fas fa-ellipsis-h"></i></a>
            </nav> -->
          </div>
          <a href="add_money.php" class="m-3 btn btn-primary">My Money</a>
          <div class="row row-sm mg-b-20">
            <div class="col-lg-7 ht-lg-100p">
              <div class="card card-dashboard-one">
                <div class="card-header">
                  
                  <div class="btn-group">
                    <button class="btn active">Day</button>
                    <button class="btn">Week</button>
                    <button class="btn">Month</button>
                  </div>
                </div><!-- card-header -->
                <div class="card-body">
                  <div class="card-body-top">
                    <div>
                      <label class="mg-b-0">Collector</label>
                      <?php 
                          $select_collector = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'collector'") or die('query failed');
                          $number_of_collector = mysqli_num_rows($select_collector);
                      ?>
                      <h2><?php echo $number_of_collector; ?></h2>
                    </div>
                    <div>
                    <label class="mg-b-0">Completed Borrower's</label>
                      <?php 
                          $select_completed_borrower = mysqli_query($conn, "SELECT * FROM `loan` WHERE status = '3'") or die('query failed');
                          $number_of_completed_borrower = mysqli_num_rows($select_completed_borrower);
                      ?>
                      <h2><?php echo $number_of_completed_borrower; ?></h2>
                    </div>
                    <div>
                      <label class="mg-b-0">Confirm Borrower's</label>
                      <?php 
                          $select_confirm_borrower = mysqli_query($conn, "SELECT * FROM `loan` WHERE status = '1'") or die('query failed');
                          $number_of_confirm_borrower = mysqli_num_rows($select_confirm_borrower);
                      ?>
                      <h2><?php echo $number_of_confirm_borrower; ?></h2>
                    </div>
                    <div>
                      <label class="mg-b-0">Released Borrower's</label>
                      <?php 
                          $select_released_borrower = mysqli_query($conn, "SELECT * FROM `loan` WHERE status = '2'") or die('query failed');
                          $number_of_released_borrower = mysqli_num_rows($select_released_borrower);
                      ?>
                      <h2><?php echo $number_of_released_borrower; ?></h2>
                    </div>
                  </div><!-- card-body-top -->
                  <div class="flot-chart-wrapper">
                    <div id="flotChart" class="flot-chart"></div>
                  </div><!-- flot-chart-wrapper -->
                </div><!-- card-body -->
              </div><!-- card -->
            </div><!-- col -->
            <div class="col-lg-5 mg-t-20 mg-lg-t-0">
              <div class="row row-sm">
                <div class="col-sm-6">
                  <div class="card card-dashboard-two">
                    <div class="card-header">
                                  <?php 
                          $select_admins = mysqli_query($conn, "SELECT * FROM `users` WHERE status = 'deactivate'") or die('query failed');
                          $number_of_admins = mysqli_num_rows($select_admins);
                      ?>
                      <h6><?php echo $number_of_admins; ?><i class="icon ion-md-trending-up tx-success"></i> <small>18.02%</small></h6>
                      <p>Payment's</p>
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
                        $select_admins = mysqli_query($conn, "SELECT * FROM `users` ") or die('query failed');
                        $number_of_admins = mysqli_num_rows($select_admins);
                    ?>
                      <h6><?php echo $number_of_admins; ?> <i class="icon ion-md-trending-down tx-danger"></i> <small>0.86%</small></h6>
                      <p>Total Users</p>
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
                          $select_admins = mysqli_query($conn, "SELECT * FROM `users` WHERE status = 'activate' AND user_type='collector'") or die('query failed');
                          $number_of_admins = mysqli_num_rows($select_admins);
                      ?>
                      <h6><?php echo $number_of_admins; ?> <small class="tx-success"><i class="icon ion-md-arrow-up"></i> 2.87%</small></h6>
                      <small>The total number of sessions within the date range. It is the period time a collector is actively engaged with your website.</small>
                    </div><!-- card-header -->
                    <div class="card-body">
                      <div class="chart"><canvas id="chartBar5"></canvas></div>
                    </div>
                  </div>
                </div>
              </div><!-- row -->
            </div><!--col -->
          </div><!-- row -->

         
        </div><!-- az-content-body -->
      </div>
    </div><!-- az-content -->
<?php @include("footer.php") ?>