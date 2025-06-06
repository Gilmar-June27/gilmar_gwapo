<?php
include '../db/database.php';
session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

// Update notifications to mark them as read
mysqli_query($conn, "UPDATE collector_notification SET status = 1 WHERE customer_id = '$customer_id' AND status = 0");

echo json_encode(['success' => true]);
