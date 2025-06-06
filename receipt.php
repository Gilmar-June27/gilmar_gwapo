<?php
include './db/database.php';
session_start();

$collector_id = $_SESSION['collector_id'] ?? null;
if (!$collector_id) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "Invalid receipt ID.";
    exit;
}

$query = "SELECT documentation.*, users.first_name, users.last_name, users.address, 
                 pickup_requests.paid, pickup_requests.kl, pickup_requests.junk_type
          FROM documentation
          LEFT JOIN users ON documentation.customer_id = users.id
          LEFT JOIN pickup_requests ON documentation.customer_id = pickup_requests.customer_id 
                                    AND documentation.collector_id = pickup_requests.collector_id
          WHERE documentation.id = '$id' AND documentation.collector_id = '$collector_id'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Receipt not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f7f7;
            padding: 30px;
        }

        .receipt-container {
            max-width: 600px;
            background: white;
            padding: 30px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .receipt-header h2 {
            margin: 0;
            font-size: 28px;
            color: #2c3e50;
        }

        .receipt-body {
            border-top: 2px dashed #ccc;
            padding-top: 20px;
        }

        .receipt-row {
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .value {
            color: #333;
        }

        .print-btn {
            display: block;
            margin: 30px auto 0;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2> Receipt</h2>
            <p><?= date('F j, Y, g:i a', strtotime($data['created_at'])) ?></p>
        </div>

        <div class="receipt-body">
            <div class="receipt-row">
                <div class="label">Customer:</div>
                <div class="value"><?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Address:</div>
                <div class="value"><?= htmlspecialchars($data['address']) ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Junk Type:</div>
                <div class="value"><?= htmlspecialchars($data['junk_type']) ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Weight :</div>
                <div class="value"><?= htmlspecialchars($data['kl']) ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Paid Amount:</div>
                <div class="value">₱<?= htmlspecialchars($data['paid']) ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Description:</div>
                <div class="value"><?= !empty($row['description']) ? htmlspecialchars($row['description']) : 'N/A' ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Review:</div>
                <div class="value"><?= !empty($row['review']) ? htmlspecialchars($row['review']) : 'N/A' ?></div>
                
            </div>
        </div>

        <button class="print-btn" onclick="window.print()">🖨 Print Receipt</button>
       

    </div>
</body>
</html>
