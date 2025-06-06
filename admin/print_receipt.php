<?php
include '../db/database.php';

$payment_id = $_GET['payment_id'] ?? null;

if (!$payment_id) {
    die('Invalid payment ID');
}

$query = "SELECT * FROM payment WHERE payment_id = '$payment_id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die('Receipt not found');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            padding: 40px;
            color: #333;
        }

        .receipt-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #28a745;
        }

        .receipt-container h2 {
            text-align: center;
            color: #28a745;
            margin-bottom: 25px;
            font-size: 24px;
        }

        .line {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        .row {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }

        .label {
            font-weight: 600;
            color: #555;
        }

        .value {
            text-align: right;
            font-weight: 500;
        }

        .btn-print {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-print button {
            padding: 10px 20px;
            font-size: 16px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-print button:hover {
            background: #218838;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .btn-print {
                display: none;
            }

            .receipt-container {
                box-shadow: none;
                border-left: none;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <h2>Payment Receipt</h2>
    <div class="line"></div>

    <div class="row"><span class="label">Payment ID:</span> <span class="value"><?= $data['payment_id'] ?></span></div>
    <div class="row"><span class="label">Loan ID:</span> <span class="value"><?= $data['loan_id'] ?></span></div>
    <div class="row"><span class="label">Amount:</span> <span class="value">₱<?= number_format($data['pay_amount'], 2) ?></span></div>
    <div class="row"><span class="label">Penalty:</span> <span class="value">₱<?= number_format($data['penalty'], 2) ?></span></div>
    <div class="row"><span class="label">Overdue:</span> <span class="value"><?= $data['overdue'] ? 'Yes' : 'No' ?></span></div>
    <div class="row"><span class="label">Is Paid:</span> <span class="value"><?= $data['is_paid'] ? 'Paid' : 'Unpaid' ?></span></div>
    <div class="row"><span class="label">Date:</span> <span class="value"><?= date('F j, Y h:i A', strtotime($data['date_created'])) ?></span></div>

    <div class="btn-print">
        <button onclick="window.print()">🖨️ Print Receipt</button>
    </div>
</div>

</body>
</html>
