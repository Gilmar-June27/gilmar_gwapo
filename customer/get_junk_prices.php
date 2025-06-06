<?php
include '../db/database.php';

if (isset($_GET['collector_id'])) {
    $collector_id = intval($_GET['collector_id']);
    $junkPrices = [];

    $query = "SELECT * FROM junk_price WHERE collector_id = $collector_id";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $junkPrices[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($junkPrices);
    exit;
}

echo json_encode([]);
?>
