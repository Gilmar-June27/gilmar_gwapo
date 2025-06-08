<?php
session_start();
include './db/database.php';

$collector_id = $_SESSION['collector_id'] ?? null;

if (!$collector_id) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// Update status for admin notifications
$update_admin = mysqli_query($conn, "UPDATE admin_notification SET status = 1 WHERE collector_id = '$collector_id' AND status = 0");

// Update status for customer notifications
$update_customer = mysqli_query($conn, "UPDATE customer_notification SET status = 1 WHERE collector_id = '$collector_id' AND status = 0");

if ($update_admin && $update_customer) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
