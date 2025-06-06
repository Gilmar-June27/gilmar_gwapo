<?php
include '../db/database.php';
session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
    header('location:./login.php');
    exit;
}

$select = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$customer_id'") or die('Query failed');
$fetch = mysqli_fetch_assoc($select);

$notifications_query = mysqli_query($conn, "
  SELECT n.*, a.first_name AS collector_name, a.image AS collector_image
  FROM `collector_notification` n
  LEFT JOIN `users` a ON n.admin_id = a.id
  WHERE n.customer_id = '$customer_id'
  ORDER BY n.created_at DESC
") or die('Query failed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Notifications</title>
  <link rel="stylesheet" href="../assets/css/style.css"> <!-- Adjust your path -->
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f9fc;
      padding: 30px;
    }
    .notification-box {
      max-width: 700px;
      margin: 0 auto;
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .notification {
      display: flex;
      gap: 15px;
      border-bottom: 1px solid #eee;
      padding: 15px 0;
    }
    .notification:last-child {
      border-bottom: none;
    }
    .notification img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
    }
    .notification-content {
      flex-grow: 1;
    }
    .notification-content h4 {
      margin: 0;
      font-size: 16px;
    }
    .notification-content small {
      color: #888;
    }
    .notification-content p {
      margin: 5px 0 0;
    }
    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      text-decoration: none;
      color: #3498db;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <a class="back-link" href="index.php">&larr; Back to Dashboard</a>

  <div class="notification-box">
    <h2>All Notifications</h2>
    <?php if (mysqli_num_rows($notifications_query) > 0): ?>
      <?php while ($notification = mysqli_fetch_assoc($notifications_query)): ?>
        <div class="notification">
          <img src="./../images/<?php echo $notification['collector_image'] ?? '1.png'; ?>" alt="Admin">
          <div class="notification-content">
            <h4><?php echo htmlspecialchars($notification['collector_name']); ?></h4>
            <small><?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?></small>
            <p><?php echo htmlspecialchars($notification['message']); ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No notifications to display.</p>
    <?php endif; ?>
  </div>

</body>
</html>
