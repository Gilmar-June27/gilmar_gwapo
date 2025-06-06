<nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
          <li class="nav-item" style="opacity: 0;">
              <a class="nav-link" href="index.php">
                <span class="menu-title">Home</span>
                <i class="mdi mdi-home menu-icon"></i>
              </a>
            </li>
            <li class="nav-item nav-profile">
              <a href="#" class="nav-link">
                <div class="nav-profile-image">
                <img src="./../images/<?php echo empty($fetch['image']) ? '1.png' : $fetch['image']; ?> ">
                  <span class="login-status online"></span>
                  <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                  <span class="font-weight-bold mb-2"><?php echo htmlspecialchars($fetch['first_name']); ?></span>
                  <!-- <span class="text-secondary text-small">Project Manager</span> -->
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
              </a>
            </li>
           
            <li class="nav-item">
              <a class="nav-link" href="index.php">
                <span class="menu-title">Home</span>
                <i class="mdi mdi-home menu-icon"></i>
              </a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link"  href="request_pickup.php" >
                <span class="menu-title">Pick Up Request</span>
                <i class="mdi mdi-contacts menu-icon"></i>
              </a>
              
            </li>
          </ul>
        </nav>