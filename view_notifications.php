<?php
include './db/database.php';
session_start();

// Check if collector is logged in
$collector_id = $_SESSION['collector_id'] ?? null;
if (!$collector_id) {
    header("Location: login.php");
    exit;
}

// Fetch collector details
$collector_query = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$collector_id'");
$collector = mysqli_fetch_assoc($collector_query);

// Fetch all notifications (admin + customer)
$notifications = mysqli_query($conn, "
    SELECT 
        an.id, an.loan_id, an.admin_id, an.collector_id, an.message, an.status, an.created_at,
        admin_user.first_name AS sender_name, admin_user.image AS sender_image,
        'admin' AS sender_type
    FROM admin_notification an
    LEFT JOIN users admin_user ON an.admin_id = admin_user.id
    WHERE an.collector_id = '$collector_id'

    UNION

    SELECT 
        cn.id, cn.loan_id, cn.admin_id, cn.collector_id, cn.message, cn.status, cn.created_at,
        customer_user.first_name AS sender_name, customer_user.image AS sender_image,
        'customer' AS sender_type
    FROM customer_notification cn
    LEFT JOIN users customer_user ON cn.customer_id = customer_user.id
    WHERE cn.collector_id = '$collector_id'

    ORDER BY created_at DESC
") or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Notifications</title>
    <link rel="stylesheet" href="your-styles.css"> <!-- Optional CSS -->
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; }
        .notification-box {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .notification-box.unread {
            background-color: #eef7ff;
        }
        .notification-box img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 15px;
        }
        .notification-text {
            flex: 1;
        }
        .notification-text strong {
            color: #444;
        }
        .notification-time {
            font-size: 0.85em;
            color: #888;
            margin-top: 4px;
        }
        h2 {
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <h2>All Notifications</h2>
  <a href="index.php" class="btn btn-primary" style="display: inline-block; margin-bottom: 20px; text-decoration: none; background-color: #007bff; color: white; padding: 10px 16px; border-radius: 5px;">Back</a>
    <?php if (mysqli_num_rows($notifications) > 0): ?>
        <?php while ($notification = mysqli_fetch_assoc($notifications)): ?>
            <div class="notification-box <?php echo $notification['status'] == 0 ? 'unread' : ''; ?>">
                <img src="./images/<?php echo $notification['sender_image'] ?: '1.png'; ?>" alt="User">
                <div class="notification-text">
                    <p>
                        <strong><?php echo htmlspecialchars($notification['sender_name']); ?></strong><br>
                        <?php echo htmlspecialchars($notification['message']); ?>
                    </p>
                    <div class="notification-time">
                        <?php echo date("F j, Y, g:i a", strtotime($notification['created_at'])); ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications found.</p>
    <?php endif; ?>
</body>
</html>
