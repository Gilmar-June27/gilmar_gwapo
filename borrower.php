<?php
include './db/database.php';
session_start();

$collector_id = $_SESSION['collector_id'];

if(!isset($collector_id)){
    header('location:./login.php');
    exit;
}

if (isset($_POST['add_borrower'])) {
    $admin_id = mysqli_real_escape_string($conn, $_POST['admin_id']);
    $tax_id = mysqli_real_escape_string($conn, $_POST['tax_id']);

    $insert = "INSERT INTO borrower (admin_id, tax_id) VALUES ('$admin_id', '$tax_id')";
    if (mysqli_query($conn, $insert)) {
        header("Location: ".$_SERVER['PHP_SELF']."?success=1");
        exit;
    } else {
        die("Insert Failed: " . mysqli_error($conn));
    }
}

// Delete Borrower
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $delete_query = "DELETE FROM borrower WHERE borrower_id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: ".$_SERVER['PHP_SELF']."?delete_success=1");
        exit;
    } else {
        die("Delete Failed: " . mysqli_error($conn));
    }
}

// Update Borrower
if (isset($_POST['update_borrower'])) {
    $borrower_id = (int)$_POST['borrower_id'];
    $admin_id = mysqli_real_escape_string($conn, $_POST['admin_id']);
    $tax_id = mysqli_real_escape_string($conn, $_POST['tax_id']);

    $update_query = "UPDATE borrower SET admin_id = '$admin_id', tax_id = '$tax_id' WHERE borrower_id = $borrower_id";
    if (mysqli_query($conn, $update_query)) {
        header("Location: ".$_SERVER['PHP_SELF']."?update_success=1");
        exit;
    } else {
        die("Update Failed: " . mysqli_error($conn));
    }
}

// Get the selected entries per page (default: 10)
$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 5;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Get current page number (default: 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $entries_per_page;

// Construct the query with search filter
$query = "
    SELECT b.borrower_id, b.tax_id, u.first_name, u.last_name, u.email, u.address, u.number 
    FROM borrower b
    JOIN users u ON b.admin_id = u.id
    WHERE u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR b.tax_id LIKE '%$search%'
    ORDER BY b.borrower_id DESC
    LIMIT $entries_per_page OFFSET $offset
";

$borrowers = mysqli_query($conn, $query);

// Get the total number of records for pagination
$total_records_query = "
    SELECT COUNT(*) AS total
    FROM borrower b
    JOIN users u ON b.admin_id = u.id
    WHERE u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR b.tax_id LIKE '%$search%'
";
$total_records_result = mysqli_query($conn, $total_records_query);
$total_records = mysqli_fetch_assoc($total_records_result)['total'];
$total_pages = ceil($total_records / $entries_per_page);
?>

<?php @include("header.php"); ?>
<?php @include("navbar.php"); ?>

<style>
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99; }
.modal-content { background: white; margin: 10% auto; padding: 20px; width: 400px; border-radius: 8px; position: relative; }
.modal-close { position: absolute; top: 10px; right: 15px; cursor: pointer; font-weight: bold; }
.btn { padding: 8px 12px; border: none; cursor: pointer; border-radius: 4px; }
.add-btn { background:#3366ff; color: white; margin-bottom: 10px; }
.table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.table th, .table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
.table th { background-color: #f2f2f2; }
.edit-btn { background: #3366ff; color: white; }
.delete-btn { background: red; color: white; }
.search-bar { display: flex; justify-content: space-between; margin: 10px 0; align-items: center; gap: 10px; flex-wrap: wrap; }
.s { display: flex; justify-content: space-between; }
select.form-control {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

</style>


<div class="container mt-4">
<div class="az-content-breadcrumb">
            <span>Junk</span>
            <span>Borrow</span>           
          </div>
          <h2 class="az-content-title"> Borrow</h2>
    <button class="btn add-btn" id="openBorrowerModal">Add Borrow</button>
    <?php if (isset($_GET['success'])): ?>
        <div style="color: green; margin-bottom: 10px;">
            <?php
            if ($_GET['success'] == 1) {
                echo "Borrower added successfully!";
            } elseif ($_GET['success'] == 2) {
                echo "Borrower updated successfully!";
            } elseif ($_GET['success'] == 3) {
                echo "Borrower deleted successfully!";
            }
            ?>
        </div>
    <?php endif; ?>
    <!-- Modal for Adding Borrower -->
    <!-- Same modal structure as before -->

    <!-- Filter/Search and Pagination Form -->
    <form method="get" style="display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center; width: 100%;">
        <!-- Show entries dropdown -->
        <div class="s" style="flex: 1; display: flex; justify-content: flex-start;">
            <label for="entries">Show</label>
            <select name="entries" id="entries" onchange="this.form.submit()">
                <?php foreach ([5, 10, 25, 50] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $entries_per_page == $opt ? 'selected' : '' ?>><?= $opt ?> entries</option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Search and button -->
        <div class="s" style="flex: 1; display: flex; justify-content: flex-end;">
            <div>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search borrowers..." style="padding: 6px; width: 200px;">
            </div>
            <div>
                <button type="submit" class="btn add-btn" style="padding: 8px 14px;">Search</button>
            </div>
        </div>
    </form>

    <div id="borrowerModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" id="closeBorrowerModal">&times;</span>
            <h4>Add admin to Borrower</h4>
            <form method="POST"> <!-- Changed method to POST -->
                <div class="mb-3">
                    <label for="admin_id" class="form-label">Select admin</label>
                    <select class="form-control" name="admin_id" id="admin_id" required>
                        <option value="" disabled selected>-- Choose to borrow --</option>
                        <?php
                        $admins = mysqli_query($conn, "SELECT id, first_name, last_name FROM users WHERE user_type = 'admin'");
                        while ($row = mysqli_fetch_assoc($admins)) {
                            $fullName = htmlspecialchars($row['first_name'] . ', ' . $row['last_name']);
                            echo "<option value='{$row['id']}'>{$fullName}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tax_id" class="form-label">Tax ID</label>
                    <input type="text" class="form-control" name="tax_id" id="tax_id" required>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" name="add_borrower" class="btn add-btn me-2">Add</button>
                    
                </div>
            </form>
        </div>
    </div>



    <!-- Borrower Table -->
    <table class="table table-striped table-bordered">
    <thead>
        <tr>
            <!-- <th>Borrower ID</th> -->
            <th>admin Name</th>
            <th>Email</th>
            <th>Address</th>
            <th>Number</th>
            <th>Tax ID</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($b = mysqli_fetch_assoc($borrowers)): ?>
            <tr>
                <!-- <td><?= htmlspecialchars($b['borrower_id']) ?></td> -->
                <td><?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?></td>
                <td><?= htmlspecialchars($b['email']) ?></td>
                <td><?= htmlspecialchars($b['address']) ?></td>
                <td><?= htmlspecialchars($b['number']) ?></td>
                <td><?= htmlspecialchars($b['tax_id']) ?></td>
                <td>
                    <!-- Edit Button -->
                    <button class="btn edit-btn" onclick="openEditModal(<?= $b['borrower_id'] ?>, '<?= htmlspecialchars($b['tax_id']) ?>', '<?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?>')">Edit</button>
                    <!-- Delete Button -->
                    <a href="?delete_id=<?= $b['borrower_id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this borrower?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?search=<?= $search ?>&entries=<?= $entries_per_page ?>&page=<?= $page - 1 ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= $search ?>&entries=<?= $entries_per_page ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?search=<?= $search ?>&entries=<?= $entries_per_page ?>&page=<?= $page + 1 ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>



    <div class="modal" id="editBorrowerModal">
    <div class="modal-content">
        <span class="modal-close" id="closeEditBorrowerModal">&times;</span>
        <h3>Edit Borrower</h3>
        <form action="" method="POST">
            <input type="hidden" name="borrower_id" id="editBorrowerId">
            <div class="mb-3">
                <label for="admin_id" class="form-label">Select admin</label>
                <select class="form-control" name="admin_id" id="editadminId" disabled>
                   
                    <?php
                    $admins = mysqli_query($conn, "SELECT id, first_name, last_name FROM users WHERE user_type = 'admin'");
                    while ($row = mysqli_fetch_assoc($admins)) {
                        $fullName = htmlspecialchars($row['first_name'] . ', ' . $row['last_name']);
                        echo "<option value='{$row['id']}'>{$fullName}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="tax_id" class="form-label">Tax ID</label>
                <input type="text" class="form-control" name="tax_id" id="editTaxId" required>
            </div>
            <button type="submit" name="update_borrower" class="btn add-btn">Update Borrower</button>
        </form>
    </div>
</div>
</div>


<!-- Edit Modal -->


<!-- Script for Modal and Edit Functionality -->
<script>
function openEditModal(borrower_id, tax_id, admin_name) {
    document.getElementById('editBorrowerId').value = borrower_id;
    document.getElementById('editTaxId').value = tax_id;
    document.getElementById('editadminId').value = admin_name;
    document.getElementById('editBorrowerModal').style.display = 'block';
}

document.getElementById("closeEditBorrowerModal").onclick = function () {
    document.getElementById("editBorrowerModal").style.display = "none";
};
document.getElementById("openBorrowerModal").onclick = function () {
    document.getElementById("borrowerModal").style.display = "block";
};

document.getElementById("closeBorrowerModal").onclick = function () {
    document.getElementById("borrowerModal").style.display = "none";
};

document.getElementById("cancelBorrowerBtn").onclick = function () {
    document.getElementById("borrowerModal").style.display = "none";
};

window.onclick = function (event) {
    if (event.target.id === "borrowerModal") {
        document.getElementById("borrowerModal").style.display = "none";
    }
};
</script>

<?php @include("footer.php") ?>
