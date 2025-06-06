<?php
include './db/database.php';
session_start();

$collector_id = $_SESSION['collector_id'];

if (!isset($collector_id)) {
    header('Location: login.php');
    exit;
}

$select = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$collector_id'") or die('Query failed');
$fetch = mysqli_fetch_assoc($select);
?>

<?php @include("header.php"); ?>
<?php @include("navbar.php"); ?>

<main class="main">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow border-0 rounded-3 p-4">
                    <div class="text-center">
                        <?php if (!empty($fetch['image'])): ?>
                            <img src="images/<?= htmlspecialchars($fetch['image']) ?>" class="rounded-circle mb-3" width="120" alt="Profile">
                        <?php else: ?>
                            <img src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg" class="rounded-circle mb-3" width="120" alt="Default Image">
                        <?php endif; ?>
                        <h4 class="fw-semibold"><?= htmlspecialchars($fetch['first_name'] . ' ' . $fetch['last_name']) ?></h4>
                        <p class="text-muted mb-4">My Profile</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($fetch['email']) ?></li>
                        <li class="list-group-item"><strong>Phone:</strong> <?= htmlspecialchars($fetch['number']) ?></li>
                        <li class="list-group-item"><strong>Address:</strong> <?= htmlspecialchars($fetch['address']) ?></li>
                        <li class="list-group-item text-danger"><strong>Password:</strong> <?= htmlspecialchars($fetch['password']) ?></li>
                    </ul>
                    <div class="text-center mt-4">
                        <a href="profile.php" class="btn btn-primary px-4">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php @include("footer.php"); ?>
