<?php
include './db/database.php';
session_start();

$collector_id = $_SESSION['collector_id'];

if (!isset($collector_id)) {
    header('Location: login.php');
    exit;
}

$message = [];

// Fetch user data
$select = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$collector_id'") or die('Query failed');
if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
}

if (isset($_POST['update_profile'])) {
    $update_first_name = mysqli_real_escape_string($conn, $_POST['update_first_name']);
    $update_last_name = mysqli_real_escape_string($conn, $_POST['update_last_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    mysqli_query($conn, "UPDATE `users` SET first_name = '$update_first_name', last_name = '$update_last_name', email = '$update_email', number = '$number', address = '$address' WHERE id = '$collector_id'") or die('Query failed');

    // Password update section
    $old_pass_input = $_POST['update_pass'];
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_pass']);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_pass']);

    if (!empty($old_pass_input) || !empty($new_pass) || !empty($confirm_pass)) {
        $stored_hashed_pass = $fetch['password'];

        if (!password_verify($old_pass_input, $stored_hashed_pass)) {
            $message[] = 'Old password is incorrect!';
        } elseif ($new_pass !== $confirm_pass) {
            $message[] = 'New password and confirmation do not match!';
        } else {
            $new_hashed_pass = password_hash($confirm_pass, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE `users` SET password = '$new_hashed_pass' WHERE id = '$collector_id'") or die('Query failed');
            $message[] = 'Password updated successfully!';
        }
    }

    // Profile image update
    $update_image = $_FILES['update_image']['name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = 'images/' . $update_image;

    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'Image is too large';
        } else {
            $image_update_query = mysqli_query($conn, "UPDATE `users` SET image = '$update_image' WHERE id = '$collector_id'") or die('Query failed');
            if ($image_update_query) {
                move_uploaded_file($update_image_tmp_name, $update_image_folder);
                $message[] = 'Image updated successfully!';
            }
        }
    }
}
?>

<?php @include("header.php") ?>
<?php @include("navbar.php") ?>
<main class="main">
    <div class="container rounded bg-white mt-5 mb-5">
        <div class="row">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <?php
                    if (isset($fetch['image']) && !empty($fetch['image'])) {
                        echo '<img class="rounded-circle mt-5" width="150px" src="images/' . htmlspecialchars($fetch['image']) . '">';
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

<?php @include("footer.php") ?>