<?php
include '../db/database.php';
session_start();

$customer_id = $_SESSION['customer_id'];

// Fetch user data
$select = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$customer_id'") or die('Query failed');
$fetch = mysqli_fetch_assoc($select);

// Handle form submission
if (isset($_POST['update_profile'])) {
    $update_first_name = mysqli_real_escape_string($conn, $_POST['update_first_name']);
    $update_last_name = mysqli_real_escape_string($conn, $_POST['update_last_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    

    mysqli_query($conn, "UPDATE `users` SET first_name = '$update_first_name', last_name = '$update_last_name', email = '$update_email', number = '$number', address = '$address' WHERE id = '$customer_id'") or die('Query failed');

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
            mysqli_query($conn, "UPDATE `users` SET password = '$confirm_pass' WHERE id = '$customer_id'") or die('Query failed');
            $message[] = 'Password updated successfully!';
        }
    }

    $update_image = $_FILES['update_image']['name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = '../images/' . $update_image;

    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'Image is too large';
        } else {
            $image_update_query = mysqli_query($conn, "UPDATE `users` SET image = '$update_image' WHERE id = '$customer_id'") or die('Query failed');
            if ($image_update_query) {
                move_uploaded_file($update_image_tmp_name, $update_image_folder);
            }
            $message[] = 'Image updated successfully!';
        }
    }
}
?>

<?php @include("header.php"); ?>
    <div class="container-scroller">
      
      <!-- partial:partials/_navbar.html -->
      <?php @include("navbar.php");?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <?php @include("sidebar.php");?>

    
        <div class="main-panel">
        <div class="content-wrapper">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background-color: #f5f6fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .profile-container {
        max-width: 900px;
        margin: auto;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-top: 50px;
    }

    .profile-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #007bff;
    }

    .form-control {
        border-radius: 10px;
    }

    .profile-button {
        background-color: #007bff;
        border: none;
        border-radius: 10px;
        padding: 10px 25px;
        color: white;
    }

    .profile-button:hover {
        background-color: #0056b3;
    }

    label {
        font-weight: 600;
        margin-bottom: 5px;
    }
</style>

<body>

        <div class="row">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <?php
                    if (isset($fetch['image']) && !empty($fetch['image'])) {
                        echo '<img class="rounded-circle mt-5" width="150px" src="../images/' . htmlspecialchars($fetch['image']) . '">';
                    } else {
                        echo '<img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg">';
                    }
                    ?>
                    <span class="font-weight-bold"><?php echo htmlspecialchars($fetch['first_name'] . ' ' . $fetch['last_name']); ?></span>
                    <span class="text-black-50"><?php echo htmlspecialchars($fetch['email']); ?></span>
                    <span> </span>
                </div>
            </div>
            <div class="col-md-5 border-right">
                <form method="post" enctype="multipart/form-data">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Profile Settings</h4>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="labels">First Name</label>
                                <input type="text" class="form-control" name="update_first_name" placeholder="first name" value="<?php echo htmlspecialchars($fetch['first_name']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="labels">Last Name</label>
                                <input type="text" class="form-control" name="update_last_name" value="<?php echo htmlspecialchars($fetch['last_name']); ?>" placeholder="surname">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="labels">Mobile Number</label>
                                <input type="text" class="form-control" name="number" placeholder="enter phone number" value="<?php echo htmlspecialchars($fetch['number']); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Address</label>
                                <input type="text" class="form-control" name="address" placeholder="enter address line 1" value="<?php echo htmlspecialchars($fetch['address']); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Email ID</label>
                                <input type="text" class="form-control" name="update_email" placeholder="enter email id" value="<?php echo htmlspecialchars($fetch['email']); ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <input type="hidden" name="old_pass" value="<?php echo htmlspecialchars($fetch['password']); ?>">
                            <div class="col-md-6">
                                <label class="labels">Old Password</label>
                                <input type="password" class="form-control" name="update_pass" placeholder="old password">
                            </div>
                            <div class="col-md-6">
                                <label class="labels">New Password</label>
                                <input type="password" class="form-control" name="new_pass" placeholder="new password">
                            </div>
                            <div class="col-md-6">
                                <label class="labels">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_pass" placeholder="confirm password">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="labels">Profile Image</label>
                                <input type="file" id="update_image" name="update_image" accept="image/*" style="display: none;">
                                <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('update_image').click()">Change Image</button>
                                <img id="preview" src="" alt="Preview" class="img-thumbnail mt-2" style="display: none; max-width: 150px;">

                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="submit" name="update_profile">Save Profile</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script>
    const fileInput = document.getElementById('update_image');
    const preview = document.getElementById('preview');

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.setAttribute('src', e.target.result);
                preview.style.display = 'block';
            };

            reader.readAsDataURL(file);
        } else {
            preview.style.display = '';
        }
    });
</script>
</body>
