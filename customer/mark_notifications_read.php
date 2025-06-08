<?php
session_start();
include '../db/database.php';

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$customer_id = $_SESSION['customer_id'];

$update = mysqli_query($conn, "UPDATE collector_notification SET status = 1 WHERE customer_id = '$customer_id' AND status = 0");

if ($update) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update']);
}
?>
