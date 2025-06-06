<?php
include './db/database.php';
session_start();

// Get current collector ID
$collector_id = $_SESSION['collector_id'] ?? null;

if (!$collector_id) {
    header('Location: login.php');
    exit;
}

// Fetch logged-in collector info
$select = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$collector_id'") or die('Query failed');
$fetch = mysqli_fetch_assoc($select);

// Notifications Query: show admin info and customer info (not collector info)
$notifications = mysqli_query($conn, "
    SELECT 
        an.id, an.loan_id, an.admin_id, an.collector_id, an.message, an.status, an.created_at,
        admin_user.first_name AS sender_name, admin_user.image AS sender_image
    FROM admin_notification an
    LEFT JOIN users admin_user ON an.admin_id = admin_user.id
    WHERE an.collector_id = '$collector_id'

    UNION

    SELECT 
        cn.id, cn.loan_id, cn.admin_id, cn.collector_id, cn.message, cn.status, cn.created_at,
        customer_user.first_name AS sender_name, customer_user.image AS sender_image
    FROM customer_notification cn
    LEFT JOIN users customer_user ON cn.customer_id = customer_user.id
    WHERE cn.collector_id = '$collector_id'

    ORDER BY created_at DESC
    LIMIT 7
") or die('Query failed');

$notification_count = mysqli_num_rows($notifications);

// Unread count query
$unread_query = mysqli_query($conn, "
    SELECT SUM(unread) AS unread_count FROM (
        SELECT COUNT(*) AS unread FROM admin_notification WHERE collector_id = '$collector_id' AND status = 0
        UNION ALL
        SELECT COUNT(*) AS unread FROM customer_notification WHERE collector_id = '$collector_id' AND status = 0
    ) AS combined_unread
") or die('Query failed');

$unread_data = mysqli_fetch_assoc($unread_query);
$unread_count = $unread_data['unread_count'] ?? 0;
?>

<!-- Header HTML Starts -->
<div class="az-header">
    <div class="container">
        <div class="az-header-left">
            <a href="index.php" class="az-logo">
                <img src="1.png" style="width:81px; margin-top:13px; filter: invert(62%) sepia(16%) saturate(1632%) hue-rotate(109deg) brightness(100%) contrast(82%);">
            </a>
            <a href="#" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
        </div>

        <div class="az-header-menu">
            <div class="az-header-menu-header">
                <a href="index.php" class="az-logo">
                    <img src="1.png" style="width:81px; margin-top:13px; filter: invert(62%) sepia(16%) saturate(1632%) hue-rotate(109deg) brightness(100%) contrast(82%);">
                </a>
                <a href="#" class="close">&times;</a>
            </div>
            <ul class="nav">
                <li class="nav-item"><a href="index.php" class="nav-link"><i class="typcn typcn-map"></i> Home</a></li>
                <li class="nav-item"><a href="pricing.php" class="nav-link"><i class="typcn typcn-document"></i> Junk Pricing</a></li>
                <li class="nav-item">
                    <a href="#" class="nav-link with-sub"><i class="typcn typcn-attachment-outline"></i> Loan</a>
                    <div class="az-menu-sub">
                        <div class="container">
                            <nav class="nav">
                                <a href="add-loan.php" class="nav-link"><i class="typcn typcn-upload-outline"></i> Your Loan</a>
                            </nav>
                        </div>
                    </div>
                </li>
                <li class="nav-item"><a href="postcateg.php" class="nav-link"><i class="typcn typcn-book"></i> Documentation</a></li>
                <li class="nav-item"><a href="customerlist.php" class="nav-link"><i class="typcn typcn-group"></i> Customer List</a></li>
                <li class="nav-item"><a href="revenue.php" class="nav-link"><i class="typcn typcn-chart-bar-outline"></i> Dashboard</a></li>
            </ul>
        </div>

        <div class="az-header-right">
            <!-- Notifications Dropdown -->
            <div class="dropdown az-header-notification">
                <a href="#" class="new"><i class="typcn typcn-bell"></i></a>
                <div class="dropdown-menu">
                    <div class="az-dropdown-header mg-b-20 d-sm-none">
                        <a href="#" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                    </div>
                    <h6 class="az-notification-title">Notifications</h6>
                    <p class="az-notification-text">You have <?php echo $unread_count; ?> unread notification<?php echo $unread_count != 1 ? 's' : ''; ?></p>

                    <div class="az-notification-list">
                        <?php if ($notification_count > 0): ?>
                            <?php while ($notification = mysqli_fetch_assoc($notifications)): ?>
                                <div class="media <?php echo $notification['status'] == 0 ? 'new' : ''; ?>">
                                    <div class="az-img-user">
                                        <img src="./images/<?php echo $notification['sender_image'] ?? '1.png'; ?>" alt="User">
                                    </div>
                                    <div class="media-body">
                                        <p>
                                            <strong><?php echo htmlspecialchars($notification['sender_name'] ?? 'Unknown User'); ?>:</strong>
                                            <?php echo htmlspecialchars($notification['message']); ?>
                                        </p>
                                        <span><?php echo date('M d h:i A', strtotime($notification['created_at'])); ?></span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No new notifications.</p>
                        <?php endif; ?>
                    </div>

                    <div class="dropdown-footer">
                        <a href="view_notifications.php">View All Notifications</a>
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown az-profile-menu">
                <a href="#" class="az-img-user">
                    <img src="./images/<?php echo empty($fetch['image']) ? '1.png' : $fetch['image']; ?>">
                </a>
                <div class="dropdown-menu">
                    <div class="az-dropdown-header d-sm-none">
                        <a href="#" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                    </div>
                    <div class="az-header-profile">
                        <div class="az-img-user">
                            <img src="./images/<?php echo empty($fetch['image']) ? '1.png' : $fetch['image']; ?>">
                        </div>
                        <h6><?php echo htmlspecialchars($fetch['first_name']); ?></h6>
                        <span><?php echo htmlspecialchars($fetch['user_type']); ?></span>
                    </div>

                    <a href="my_profile.php" class="dropdown-item"><i class="typcn typcn-user-outline"></i> My Profile</a>
                    <a href="profile.php" class="dropdown-item"><i class="typcn typcn-edit"></i> Edit Profile</a>
                    <a href="./logout.php" class="dropdown-item"><i class="typcn typcn-power-outline"></i> Sign Out</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Header HTML Ends -->
