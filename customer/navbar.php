<?php
include '../db/database.php';
session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
    header('location:./login.php');
    exit;
}

$select = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$customer_id'") or die('Query failed');
if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
}

$notifications_query = mysqli_query($conn, "
  SELECT n.*, a.first_name AS collector_name, a.image AS collector_image
  FROM `collector_notification` n
  LEFT JOIN `users` a ON n.admin_id = a.id
  WHERE n.customer_id = '$customer_id'
  ORDER BY n.created_at DESC
") or die('Query failed');




// Get the notification count (now excluding the "pickup request from customer")
$notification_count = mysqli_num_rows($notifications_query);

// Fetch unread count
$unread_query = mysqli_query($conn, "SELECT COUNT(*) AS unread_count FROM collector_notification WHERE customer_id = '$customer_id' AND status = 0");

$unread_data = mysqli_fetch_assoc($unread_query);
$unread_count = $unread_data['unread_count'];


?>


<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <a class="navbar-brand brand-logo" href="index.php"><img src="../1.png" style="height: 88px;width: 182px; filter: invert(62%) sepia(16%) saturate(1632%) hue-rotate(109deg) brightness(100%) contrast(82%);"></a>
          <a class="navbar-brand brand-logo-mini" href="index.php"><img src="../1.png" style="height: 35px;width: 182px; filter: invert(62%) sepia(16%) saturate(1632%) hue-rotate(109deg) brightness(100%) contrast(82%);"></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
          </button>
          <!-- <div class="search-field d-none d-md-block">
            <form class="d-flex align-items-center h-100" action="#">
              <div class="input-group">
                <div class="input-group-prepend bg-transparent">
                  <i class="input-group-text border-0 mdi mdi-magnify"></i>
                </div>
                <input type="text" class="form-control bg-transparent border-0" placeholder="Search projects">
              </div>
            </form>
          </div> -->
          <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown">
              <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="nav-profile-img">
                  <img src="./../images/<?php echo empty($fetch['image']) ? '1.png' : $fetch['image']; ?> ">
                  <span class="availability-status online"></span>
                </div>
                <div class="nav-profile-text">
                  <p class="mb-1 text-black"><?php echo htmlspecialchars($fetch['first_name']); ?></p>
                </div>
              </a>
              <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                <a class="dropdown-item" href="profile.php">
                <i class="mdi mdi-pencil me-2 text-primary"></i>
                Edit Profile </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="./../logout.php">
                  <i class="mdi mdi-logout me-2 text-primary"></i> Signout </a>
              </div>
            </li>
            <li class="nav-item d-none d-lg-block full-screen-link">
              <a class="nav-link">
                <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
              </a>
            </li>
            
            <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" data-bs-toggle="dropdown" onclick="toggleNotifications(); markNotificationsAsRead();">
              <i class="mdi mdi-bell-outline"></i>
              <?php if ($unread_count > 0): ?>
                <span class="count-symbol bg-danger notif-count" id="notifCount"><?php echo $unread_count; ?></span>
              <?php endif; ?>
            </a>

            <div class="dropdown-menu dropdown-menu-end navbar-dropdown preview-list p-3" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
              <h6 class="dropdown-header">Notifications</h6>
              <div class="dropdown-divider"></div>

              <?php if ($notification_count > 0): ?>
                <?php while ($notification = mysqli_fetch_assoc($notifications_query)): ?>
                  <div class="d-flex align-items-start mb-3">
                    <img src="./../images/<?php echo $notification['collector_image'] ?? '1.png'; ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;" alt="Admin">
                    <div class="flex-grow-1">
                      <strong><?php echo htmlspecialchars($notification['collector_name']); ?></strong>
                      <div class="small text-muted"><?php echo date('M d, h:i A', strtotime($notification['created_at'])); ?></div>
                      <div class="mt-1"><?php echo htmlspecialchars($notification['message']); ?></div>
                    
                    </div>
                  </div>
                  <div class="dropdown-divider"></div>
                  
                <?php endwhile; ?>
              <?php else: ?>
                <p class="text-center text-muted">No notifications yet.</p>
              <?php endif; ?>
              <a href="view_notifications.php" class="d-block mt-1 text-primary">View Details</a>
            </div>
          </li>

            
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav><script>
function markNotificationsAsRead() {
    fetch('mark_notifications_read.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const countElem = document.getElementById('notifCount');
                if (countElem) {
                    countElem.style.display = 'none';
                }
            }
        })
        .catch(err => console.error('Error marking notifications:', err));
}
</script>