<?php
@include("../db/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $collector_id = $_POST['collector_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $junk_type = $_POST['junk_type'];
    $description = $_POST['description'];
    $preferred_date = $_POST['preferred_date'];

    $query = "INSERT INTO pickup_requests (customer_id, collector_id, name, address, contact_number, junk_type, description, preferred_date) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iissssss", $customer_id, $collector_id, $name, $address, $contact_number, $junk_type, $description, $preferred_date);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Pickup request sent successfully!'); window.location.href='map_page.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
