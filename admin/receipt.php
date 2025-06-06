<?php
include '../db/database.php';
session_start();

// Check if admin is logged in
$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('Location: ./login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Invalid receipt ID.";
    exit;
}

// Fetch data from pickup_requests JOIN users (collector info)
$query = "
    SELECT 
        pr.*, 
        u.first_name, 
        u.last_name 
    FROM pickup_requests pr
    LEFT JOIN users u ON pr.collector_id = u.id
    WHERE pr.id = '$id' AND pr.admin_id = '$admin_id'
";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo '
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh; background: #f8f9fa;">
      <div style="
        background: linear-gradient(to right, #f8d7da, #f5c6cb);
        border: 2px solid #dc3545;
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        max-width: 500px;
        width: 100%;
        padding: 30px;
        text-align: center;
        font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
        position: relative;
        animation: popIn 0.5s ease;
      ">
        <a href="javascript:history.back()" style="
          position: absolute;
          top: 15px;
          right: 15px;
          background-color: #dc3545;
          color: #fff;
          padding: 8px 16px;
          border-radius: 8px;
          text-decoration: none;
          font-weight: bold;
          font-size: 14px;
          transition: background-color 0.3s ease;
        " onmouseover="this.style.backgroundColor=\'#c82333\'" onmouseout="this.style.backgroundColor=\'#dc3545\'">
          ← Go Back
        </a>
    
        <img src="https://cdn-icons-png.flaticon.com/512/463/463612.png" alt="Warning" style="width: 50px; margin-bottom: 15px; margin-top: 30px;">
        <h4 style="color: #721c24; margin-bottom: 10px;">No Receipt Found</h4>
        <p style="color: #721c24; font-size: 16px;">
          You need to complete the payment to this user first before generating a receipt.
        </p>
      </div>
    </div>
    
    <style>
    @keyframes popIn {
      0% { transform: scale(0.9); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }
    </style>
    ';
    

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
            <h2>Receipt</h2>
            <p><?= isset($data['created_at']) ? date('F j, Y, g:i a', strtotime($data['created_at'])) : '' ?></p>
        </div>

        <div class="receipt-body">
            <div class="receipt-row">
                <div class="label">Collector Name:</div>
                <div class="value"><?= htmlspecialchars(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')) ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Address:</div>
                <div class="value"><?= htmlspecialchars($data['address'] ?? '') ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Junk Type:</div>
                <div class="value"><?= htmlspecialchars($data['junk_type'] ?? '') ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Weight :</div>
                <div class="value"><?= htmlspecialchars($data['kl'] ?? '') ?></div>
            </div>

            <div class="receipt-row">
                <div class="label">Paid Amount:</div>
                <div class="value">₱<?= htmlspecialchars($data['paid'] ?? '') ?></div>
            </div>
        </div>

        <button class="print-btn" onclick="window.print()">🖨 Print Receipt</button>
    </div>
</body>
</html>
