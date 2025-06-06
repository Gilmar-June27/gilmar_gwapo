<?php  

include './db/database.php';
session_start();

// print_r("WErfwe");
// function updating profile by users

$user_id = $_SESSION['user_id'];

// Fetch user data for pre-filling the form
$select = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$user_id'") or die('Query failed');
if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
}

if (isset($_POST['update_profile'])) {

    print_r("sdfsdf");
    $update_first_name = mysqli_real_escape_string($conn, $_POST['update_first_name']);
    $update_last_name = mysqli_real_escape_string($conn, $_POST['update_last_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    

    mysqli_query($conn, "UPDATE `users` SET first_name = '$update_first_name', last_name = '$update_last_name', email = '$update_email', number = '$number', address = '$address' WHERE id = '$user_id'") or die('Query failed');

    $old_pass = $_POST['old_pass'];
    $update_pass = mysqli_real_escape_string($conn, md5($_POST['update_pass']));
    $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
    $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));

    if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {
        if ($update_pass != $old_pass) {
            $message[] = 'Old password does not match!';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = 'New password and confirmation do not match!';
        } else {
            mysqli_query($conn, "UPDATE `users` SET password = '$confirm_pass' WHERE id = '$user_id'") or die('Query failed');
            $message[] = 'Password updated successfully!';
        }
    }

    $update_image = $_FILES['update_image']['name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = './images/' . $update_image;

    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'Image is too large';
        } else {
            $image_update_query = mysqli_query($conn, "UPDATE `users` SET image = '$update_image' WHERE id = '$user_id'") or die('Query failed');
            if ($image_update_query) {
                move_uploaded_file($update_image_tmp_name, $update_image_folder);
            }
            $message[] = 'Image updated successfully!';
        }
    }
}


//function for add pricing
if (isset($_POST['add'])) {
    $product_type = mysqli_real_escape_string($conn, $_POST['product_type']);
    $garbage_price = $_POST['garbage_price'];
    $garbage_kg = $_POST['garbage_kg'];
    
    // Handle image upload
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = './images/' . $image;

    if ($image_size > 2000000) {
        $_SESSION['message'] = 'Image exceeds the size limit of 2MB.';
       
        exit();
    }

    if (move_uploaded_file($image_tmp_name, $image_folder)) {
        // Insert product data into the database
        $add_product_query = mysqli_query($conn, "INSERT INTO `junk_price` (product_type, image, garbage_price, garbage_kg,user_id) VALUES('$product_type', '$image', '$garbage_price', '$garbage_kg', '$user_id')");
        header("location:pricing.php");
        if ($add_product_query) {
            $_SESSION['message'] = 'Product added successfully!.';
        } else {
            $_SESSION['message'] = 'Product could not be added!';
        }
    } else {
        $_SESSION['message'] = 'Failed to upload image.';
    }
   
   
    exit();
}







//functon for login
if (isset($_POST['submit'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

    if (mysqli_num_rows($select_users) > 0) {

        $row = mysqli_fetch_assoc($select_users);

        if ($row['block'] == 'yes') {
            $message[] = 'Your account has been deactivated by the admin.';
        } else {
            $user_id = $row['id'];
            $update_query = "UPDATE `users` SET status = 'activate' WHERE id = '$user_id'";
            mysqli_query($conn, $update_query) or die('query failed');

            if ($row['user_type'] == 'customer') {
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_id'] = $row['id'];
                header('location:index.php');
            } elseif ($row['user_type'] == 'collector') {
                $_SESSION['admin_name'] = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_id'] = $row['id'];
                header('location:index.php');
            } 
			// elseif ($row['user_type'] == 'super_admin') {
            //     $_SESSION['super_admin_name'] = $row['name'];
            //     $_SESSION['super_admin_email'] = $row['email'];
            //     $_SESSION['super_admin_id'] = $row['id'];
            //     header('location:admin/super-admin-dashboard.php');
            // }
        }

    } else {
        $message[] = 'Incorrect email or password!';
    }
}





//function for registration

if (isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
    $user_type = $_POST['user_type'];
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'images/' . $image;

    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select_users) > 0) {
        $message[] = 'User already exists!';
    } else {
        if ($password != $cpass) {
            $message[] = 'Confirm password does not match!';
        } elseif ($image_size > 500000000) {
            $message[] = 'Image size is too large!';
        } else {
            // Corrected the INSERT query to include all columns
            $insert_query = "INSERT INTO `users` (first_name, last_name, email, address, number, password, image,user_type) VALUES('$first_name', '$last_name', '$email', '$address', '$number', '$password', '$image', '$user_type')";
            mysqli_query($conn, $insert_query) or die('Query failed: ' . mysqli_error($conn));          
            $message[] = 'Registered successfully!';
            header('location:login.php');
            // Important to stop further script execution after redirect
            
        }
    }
}
?>