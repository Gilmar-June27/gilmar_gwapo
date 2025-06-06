<?php
@include("../db/database.php");

if (isset($_POST['query'])) {
    $search = mysqli_real_escape_string($conn, $_POST['query']);
    $query = "SELECT id, first_name, last_name FROM users 
              WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' 
              LIMIT 10";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($user = mysqli_fetch_assoc($result)) {
            $fullname = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
            echo "<a href='#' class='list-group-item list-group-item-action user-item' data-id='{$user['id']}'>$fullname</a>";
        }
    } else {
        echo "<div class='list-group-item'>No users found</div>";
    }
}
?>
