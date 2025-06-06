<?php
include '../db/database.php';

function generateReferenceNumber() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function isReferenceNumberUnique($ref_no, $conn) {
    $query = "SELECT COUNT(*) as count FROM loan WHERE ref_no = '$ref_no'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] == 0;
}

// Generate and check for uniqueness
do {
    $ref_no = generateReferenceNumber();
} while (!isReferenceNumberUnique($ref_no, $conn));

echo $ref_no; // Output the unique reference number
?>
