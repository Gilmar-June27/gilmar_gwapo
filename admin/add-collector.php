<?php
include '../db/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:./login.php');
    exit;
}

// ADD COLLECTOR
if (isset($_POST['add'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $password = $_POST['password']; // hashed password

    $insert = mysqli_query($conn, "INSERT INTO users (first_name, last_name, email, address, number, password, user_type) 
        VALUES ('$first_name', '$last_name', '$email', '$address', '$number', '$password', 'collector')");
}

// UPDATE COLLECTOR
if (isset($_POST['edit_user'])) {
    $id = $_POST['update_id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['edit_first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['edit_last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['edit_email']);
    $address = mysqli_real_escape_string($conn, $_POST['edit_address']);
    $number = mysqli_real_escape_string($conn, $_POST['edit_number']);

    mysqli_query($conn, "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', address='$address', number='$number' WHERE id=$id");
}

// DELETE COLLECTOR
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$delete_id");
    header("Location: admin_collector.php");
    exit;
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$start = ($page - 1) * $limit;

$totalQuery = "SELECT COUNT(*) AS total FROM users WHERE user_type='collector' AND CONCAT(first_name, ' ', last_name, email, number) LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$total = $totalRow['total'];
$pages = ceil($total / $limit);

$select_collector = mysqli_query($conn, "SELECT * FROM users WHERE user_type='collector' AND CONCAT(first_name, ' ', last_name, email, number) LIKE '%$search%' LIMIT $start, $limit");
?>

<?php @include("header.php") ?>
<?php @include("navbar.php") ?>

<style>
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99; }
.modal-content { background: white; margin: 10% auto; padding: 20px; width: 400px; border-radius: 8px; position: relative; }
.modal-close { position: absolute; top: 10px; right: 15px; cursor: pointer; font-weight: bold; }
.btn { padding: 8px 12px; border: none; cursor: pointer; }
.add-btn { background:#3366ff; color: white; margin-bottom: 10px; }
.edit-btn { background: #3366ff; color: white; }
.delete-btn { background: red; color: white; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 8px; }
</style>

<div class="container mt-4">
<div class="az-content-breadcrumb">
            <span>Junk</span>
            <span>Collector Management</span>           
          </div>
          <h2 class="az-content-title">Collector Management</h2>


<button class="btn add-btn" id="openModal">Add Collector</button>
<div class="modal" id="collectorModal">
    <div class="modal-content">
        <span class="modal-close" id="closeModal">&times;</span>
        <h3>Add Collector</h3>
        <form action="" method="post">
            <input type="text" name="first_name" placeholder="First Name" required><br><br>
            <input type="text" name="last_name" placeholder="Last Name" required><br><br>
            <input type="email" name="email" placeholder="Email" required style="    padding: 10px;
    border-radius: 10px;
    width: 355px;
    outline: none;
    border: 1px solid lightgrey;"><br><br>
            <input type="text" name="address" placeholder="Address" required><br><br>
            <input type="text" name="number" placeholder="Phone Number" required><br><br>
            <input type="password" name="password" placeholder="Password" required style="    padding: 10px;
    border-radius: 10px;
    width: 355px;
    outline: none;
    border: 1px solid lightgrey;"><br><br>
            <button class="btn add-btn" type="submit" name="add">Create</button>
        </form>
    </div>
</div>

<form method="GET" class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        Show 
        <select name="limit" onchange="this.form.submit()">
            <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
            <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
        </select>
        entries
    </div>
    <div style="display:flex;justify-content:space-evenly; position: relative;">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-sm btn-primary">Search</button>
    </div>
</form>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Password</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($select_collector) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($select_collector)): ?>
                <tr>
                    <td><?= $row['first_name'].' '.$row['last_name']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td><?= $row['number']; ?></td>
                    <td><?= $row['password']; ?></td>
                    <td><?= $row['status']; ?></td>
                    <td>
                        <button class="btn edit-btn" onclick="openEditModal(<?= $row['id']; ?>)">Edit</button>
                        <a href="admin_collector.php?delete=<?= $row['id']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this collector?');">Delete</a>
                    </td>
                </tr>
                <!-- Edit Modal (same as before) -->
                <div class="modal" id="editModal<?= $row['id']; ?>">
                    <div class="modal-content">
                        <span class="modal-close" onclick="closeEditModal(<?= $row['id']; ?>)">&times;</span>
                        <h3>Edit Collector</h3>
                        <form action="" method="post">
                            <input type="hidden" name="update_id" value="<?= $row['id']; ?>">
                            <input type="text" name="edit_first_name" value="<?= $row['first_name']; ?>" required><br><br>
                            <input type="text" name="edit_last_name" value="<?= $row['last_name']; ?>" required><br><br>
                            <input type="email" name="edit_email" style="border-radius: 7px;padding: 10px;border: 1px solid rgba(0, 0, 0, 20);" value="<?= $row['email']; ?>" required><br><br>
                            <input type="text" name="edit_address" value="<?= $row['address']; ?>" required><br><br>
                            <input type="text" name="edit_number" value="<?= $row['number']; ?>" required><br><br>
                            <button class="btn edit-btn" type="submit" name="edit_user">Update</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No collectors found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Pagination -->
<nav>
    <ul class="pagination">
        <?php if($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?search=<?= $search ?>&limit=<?= $limit ?>&page=<?= $page - 1 ?>">Previous</a></li>
        <?php endif; ?>
        
        <?php for($i = 1; $i <= $pages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?search=<?= $search ?>&limit=<?= $limit ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        
        <?php if($page < $pages): ?>
            <li class="page-item"><a class="page-link" href="?search=<?= $search ?>&limit=<?= $limit ?>&page=<?= $page + 1 ?>">Next</a></li>
        <?php endif; ?>
    </ul>
</nav>

</div>

<script>
document.getElementById("openModal").onclick = function() {
    document.getElementById("collectorModal").style.display = "block";
};
document.getElementById("closeModal").onclick = function() {
    document.getElementById("collectorModal").style.display = "none";
};
function openEditModal(id) {
    document.getElementById("editModal" + id).style.display = "block";
}
function closeEditModal(id) {
    document.getElementById("editModal" + id).style.display = "none";
}
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
}
</script>

<?php @include("footer.php") ?>
