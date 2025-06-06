<?php
include './db/database.php';

if (isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);
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
        
            $insert_query = "INSERT INTO `users` (first_name, last_name, email, address, number, password, image,user_type) VALUES('$first_name', '$last_name', '$email', '$address', '$number', '$password', '$image', '$user_type')";
            mysqli_query($conn, $insert_query) or die('Query failed: ' . mysqli_error($conn));          
            $message[] = 'Registered successfully!';
            header('location:login.php');
           
            
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="newcss/styles.css">
    <style>
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            z-index: 1;
        }

        .div {
            position: relative;
        }

        #address_results {
            background: white;
            border: 1px solid #ccc;
            text-align: start;
            max-height: 200px;
            top: 51px;
            overflow-y: auto;
            position: absolute;
            width: 109%;
            left: -24px;
            z-index: 999;
            font-size: 14px;
            color: #333;
            padding: 5px;
            border-radius: 5px;
            display: none;
        }

        #address_results div {
            padding: 5px 10px;
            cursor: pointer;
        }

        #address_results div:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <img class="wave" src="ban.png">
    <div class="container">
        <div class="img">
            <img src="https://raw.githubusercontent.com/sefyudem/Responsive-Login-Form/master/img/bg.svg">
        </div>
        <div class="login-content">
            <form method="post" enctype="multipart/form-data">
                <img src="https://raw.githubusercontent.com/sefyudem/Responsive-Login-Form/master/img/avatar.svg">
                <h2 class="title">Welcome</h2>

                <?php
                if (isset($message)) {
                    foreach ($message as $msg) {
                        echo '
                            <div class="message">
                                <span>' . $msg . '</span>
                                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
                            </div>
                        ';
                    }
                }
                ?>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-user"></i></div>
                    <div class="div">
                        <h5>Firstname</h5>
                        <input type="text" class="input" name="first_name" required>
                    </div>
                </div>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-envelope"></i></div>
                    <div class="div">
                        <h5>Lastname</h5>
                        <input type="text" class="input" name="last_name" required>
                    </div>
                </div>

                <div class="input-div one" style="display:none;">
                    <div class="i"><i class="fas fa-user-tag"></i></div>
                    <div class="div">
                        <select name="user_type" style="width: 311px; outline: none; border: none; font-weight: 700; color: rgba(0,0,0,.4);">
                            <option value="customer" selected>Customer</option>
                        </select>
                    </div>
                </div>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-envelope"></i></div>
                    <div class="div">
                        <h5>Email</h5>
                        <input type="email" class="input" name="email" required>
                    </div>
                </div>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-phone"></i></div>
                    <div class="div">
                        <h5>Phone Number</h5>
                        <input type="text" class="input" name="number" required>
                    </div>
                </div>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-search-location"></i></div>
                    <div class="div">
                        <h5>Search Address</h5>
                        <input type="text" class="input" id="address_search" name="address">
                        <div id="address_results"></div>
                    </div>
                </div>

                <div class="input-div pass">
                    <div class="i"><i class="fas fa-lock"></i></div>
                    <div class="div">
                        <h5>Password</h5>
                        <input type="password" class="input password-field" name="password" required>
                        <span class="toggle-password"><i class="far fa-eye"></i></span>
                    </div>
                </div>

                <div class="input-div pass">
                    <div class="i"><i class="fas fa-lock"></i></div>
                    <div class="div">
                        <h5>Confirm Password</h5>
                        <input type="password" class="input password-field" name="cpassword" required>
                        <span class="toggle-password"><i class="far fa-eye"></i></span>
                    </div>
                </div>

                <div class="input-div one" style="opacity:0">
                    <div class="i"><i class="fas fa-image"></i></div>
                    <div class="div">
                        <h5>Image</h5>
                        <input type="file" class="input" name="image">
                    </div>
                </div>

                <button type="submit" name="submit" class="btn">Register</button>

                <p style="text-align: center; margin-top: 10px; display: flex; align-items: center; justify-content: center;">
                    Already have an account?
                    <a href="login.php" style="margin-left:5px; color:rgb(74, 231, 87); text-decoration: none; font-weight: bold;">Login</a>
                </p>
            </form>
        </div>
    </div>

    <script>
      const addressInput = document.getElementById("address_search");
    const resultsDiv = document.getElementById("address_results");

    const buenavistaBarangays = [
        "Anonang", "Asinan", "Bago", "Baluarte", "Bantuan", "Bato", "Bonotbonot", "Bugaong",
        "Cambuhat", "Cambus-oc", "Cangawa", "Cantomugcad", "Cantores", "Cantuba", "Catigbian", "Cawag",
        "Cruz", "Dait", "Eastern Cabul-an", "Hunan", "Lapacan Norte", "Lapacan Sur", "Lubang", "Lusong (Plateau)",
        "Magkaya", "Merryland", "Nueva Granada", "Nueva Montana", "Overland", "Panghagban", "Poblacion",
        "Puting Bato", "Rufo Hill", "Sweetland", "Western Cabul-an"
    ];

    addressInput.addEventListener("input", function () {
        let query = this.value.trim().toLowerCase();
        resultsDiv.innerHTML = "";
        resultsDiv.style.display = "none";

        if (query.length >= 1) {
            // Case 1: User searches "buenavista" or "b"
            if (query === "buenavista" || query === "b") {
                resultsDiv.style.display = "block";
                buenavistaBarangays.forEach(barangay => {
                    const div = document.createElement("div");
                    div.textContent = barangay + ", Buenavista, Bohol";
                    div.style.padding = "5px";
                    div.style.cursor = "pointer";
                    div.addEventListener("click", function () {
                        addressInput.value = this.textContent;
                        resultsDiv.innerHTML = "";
                        resultsDiv.style.display = "none";
                    });
                    resultsDiv.appendChild(div);
                });
            } else {
                // Case 2: Use OpenStreetMap Nominatim search
                fetch("https://nominatim.openstreetmap.org/search?format=json&q=" + encodeURIComponent(query) + "&addressdetails=1&limit=10&countrycodes=ph")
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            resultsDiv.style.display = "block";
                            data.forEach(result => {
                                const div = document.createElement("div");
                                div.textContent = result.display_name;
                                div.style.padding = "5px";
                                div.style.cursor = "pointer";
                                div.addEventListener("click", function () {
                                    addressInput.value = this.textContent;
                                    resultsDiv.innerHTML = "";
                                    resultsDiv.style.display = "none";
                                });
                                resultsDiv.appendChild(div);
                            });
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching data:", error);
                    });
            }
        }
    });

    // Hide results if clicking outside
    document.addEventListener("click", function (event) {
        if (!addressInput.contains(event.target) && !resultsDiv.contains(event.target)) {
            resultsDiv.style.display = "none";
        }
    });

    // Input animation (optional)
    const inputs = document.querySelectorAll(".input");
    inputs.forEach(input => {
        input.addEventListener("focus", () => input.parentNode.parentNode.classList.add("focus"));
        input.addEventListener("blur", () => {
            if (input.value === "") input.parentNode.parentNode.classList.remove("focus");
        });
    });

    // Password toggle
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const eye = this.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        });
    });
</script>

</body>
</html>