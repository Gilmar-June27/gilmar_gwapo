<?php
include './db/database.php';
session_start();

if (isset($_POST['submit'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

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
                $_SESSION['customer_name'] = $row['name'];
                $_SESSION['customer_email'] = $row['email'];
                $_SESSION['customer_id'] = $row['id'];
                
                header('location:./customer/index.php');
            } elseif ($row['user_type'] == 'collector') {
                $_SESSION['collector_name'] = $row['name'];
                $_SESSION['collector_email'] = $row['email'];
                $_SESSION['collector_id'] = $row['id'];
                header('location:index.php');
            }elseif ($row['user_type'] == 'admin') {
                $_SESSION['admin_name'] = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_id'] = $row['id'];
                header('location:./admin/index.php');
            }  
			
        }

    } else {
        $message[] = 'Incorrect email or password!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="newcss/login.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 2;
        }

        .eye-icon .eye,
        .eye-icon .pupil {
            transition: all 0.3s ease;
        }

        .eye-icon.hide .eye {
            opacity: 0.3;
        }

        .eye-icon.hide .pupil {
            display: none;
        }

        .i svg {
            fill: none;
            stroke: #555;
            stroke-width: 2;
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
            <form style="padding:40px" method="post" enctype="multipart/form-data">
                <img src="https://raw.githubusercontent.com/sefyudem/Responsive-Login-Form/master/img/avatar.svg">
                <h2 class="title">Welcome</h2>

                <?php if (isset($message)) {
                    foreach ($message as $msg) {
                        echo '<div class="message" style="text-align:center">
                            <span>' . $msg . '</span>
                            <span style="cursor:pointer" onclick="this.parentElement.remove();">×</span>
                        </div>';
                    }
                } ?>

                <!-- Email Field -->
                <div class="input-div one">
                    <div class="i">
                        <!-- User Icon -->
                        <svg width="20" height="20" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-3-3.87M4 21v-2a4 4 0 0 1 3-3.87M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                        </svg>
                    </div>
                    <div class="div">
                        <h5>Email</h5>
                        <input type="text" class="input" name="email" required>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="input-div pass">
                    <div class="i">
                        <!-- Lock Icon -->
                        <svg width="20" height="20" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>
                    <div class="div" style="position: relative;">
                        <h5>Password</h5>
                        <input type="password" class="input password-field" name="password" required>
                        <!-- Eye Icon -->
                        <span class="toggle-password">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path class="eye" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                                <circle class="pupil" cx="12" cy="12" r="3"/>
                            </svg>
                        </span>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn">Login</button>

                <p style="text-align: center; margin-top: 10px; display: flex; align-items: center; justify-content: center;">
                    Don’t have an account?&nbsp;
                    <a href="registration.php" style="color:rgb(74, 231, 87); text-decoration: none; font-weight: bold;"> Signup</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        // Input focus effect
        const inputs = document.querySelectorAll(".input");
        inputs.forEach(input => {
            input.addEventListener("focus", function () {
                this.parentNode.parentNode.classList.add("focus");
            });
            input.addEventListener("blur", function () {
                if (this.value === "") {
                    this.parentNode.parentNode.classList.remove("focus");
                }
            });
        });

        // Password toggle
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const input = this.previousElementSibling;
                const eyeIcon = this.querySelector('.eye-icon');

                if (input.type === "password") {
                    input.type = "text";
                    eyeIcon.classList.add('hide');
                } else {
                    input.type = "password";
                    eyeIcon.classList.remove('hide');
                }
            });
        });
    </script>
</body>
</html>