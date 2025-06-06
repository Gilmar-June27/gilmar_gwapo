<?php @include("header.php"); ?>
    <div class="container-scroller">
      
      <!-- partial:partials/_navbar.html -->
      <?php @include("navbar.php");?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <?php @include("sidebar.php");?>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper" style="    margin-top: 51px;">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-home"></i>
                </span> Dashboard
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                  </li>
                </ul>
              </nav>
            </div>
            <?php @include("map.php");?>
          </div>

          <?php @include("footer.php");?>