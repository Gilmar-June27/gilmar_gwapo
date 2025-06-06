<?php
@include '../db/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:./login.php');
    exit;
}

// INSERT LOAN TYPE
if (isset($_POST['add_loan_type'])) {
    $ltype_name = mysqli_real_escape_string($conn, $_POST['ltype_name']);
    $ltype_desc = mysqli_real_escape_string($conn, $_POST['ltype_desc']);

    $query = "INSERT INTO loan_type (ltype_name, ltype_desc) VALUES ('$ltype_name', '$ltype_desc')";
    if (mysqli_query($conn, $query)) {
        header("Location: ".$_SERVER['PHP_SELF']."?success=1");
        exit;
    } else {
        die("Insert Failed: " . mysqli_error($conn));
    }
}

// DELETE LOAN TYPE
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $query = "DELETE FROM loan_type WHERE ltype_id = $delete_id";
    if (mysqli_query($conn, $query)) {
        header("Location: ".$_SERVER['PHP_SELF']."?deleted=1");
        exit;
    } else {
        die("Delete Failed: " . mysqli_error($conn));
    }
}

// UPDATE LOAN TYPE
if (isset($_POST['update_loan_type'])) {
    $ltype_id = (int)$_POST['ltype_id'];
    $ltype_name = mysqli_real_escape_string($conn, $_POST['ltype_name']);
    $ltype_desc = mysqli_real_escape_string($conn, $_POST['ltype_desc']);

    $query = "UPDATE loan_type SET ltype_name = '$ltype_name', ltype_desc = '$ltype_desc' WHERE ltype_id = $ltype_id";
    if (mysqli_query($conn, $query)) {
        header("Location: ".$_SERVER['PHP_SELF']."?updated=1");
        exit;
    } else {
        die("Update Failed: " . mysqli_error($conn));
    }
}

// Pagination + Search Logic
$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

$offset = ($page - 1) * $entries_per_page;
$where = $search ? "WHERE ltype_name LIKE '%$search%' OR ltype_desc LIKE '%$search%'" : "";

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM loan_type $where");
$total_rows = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_rows / $entries_per_page);

$result = mysqli_query($conn, "SELECT * FROM loan_type $where ORDER BY ltype_id DESC LIMIT $entries_per_page OFFSET $offset");
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
.s{
    display: flex;
    justify-content: space-between;
}
</style>

<div class="container mt-4">
    <div class="az-content-breadcrumb">
        <span>Loan</span>
        <span>Loan Type Management</span>           
    </div>
  

    <h2 class="az-content-title">Loan Type Management</h2>

    <button class="btn add-btn" id="openLoanModal">Add Loan Type</button>
    <?php if (isset($_GET['success'])): ?>
    <div style="color: green; margin-bottom: 10px;">Loan type added successfully!</div>
<?php endif; ?>
    <!-- Search and Entries -->
    <div class="search-bar">
    <form method="get" style="display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center; width: 100%;">
    <!-- "Show" dropdown at the start -->
    <div class="s"  style="flex: 1; display: flex; justify-content: flex-start;">
        <label for="entries">Show</label>
        <select name="entries" id="entries" onchange="this.form.submit()"  >
            <?php foreach ([5, 10, 25, 50] as $opt): ?>
                <option value="<?= $opt ?>" <?= $entries_per_page == $opt ? 'selected' : '' ?>><?= $opt ?> entries</option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Search and button at the end -->
    <div class="s" style="flex: 1; display: flex; justify-content: flex-end;">
        <div>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search loan types..." style="padding: 6px; width: 200px;">
        </div>

        <div>
            <button type="submit" class="btn add-btn" style="padding: 8px 14px;">Search</button>
        </div>
    </div>
</form>


    </div>

    <!-- Modal -->
    <div class="modal" id="loanModal">
        <div class="modal-content">
            <span class="modal-close" id="closeLoanModal">&times;</span>
            <h3>Add Loan Type</h3>
            <form action="" method="POST">
                <label>Loan Type Name:</label><br>
                <input type="text" name="ltype_name" required><br><br>
                <label>Description:</label><br>
                <textarea name="ltype_desc" rows="3" required></textarea><br><br>
                <button class="btn add-btn" type="submit" name="add_loan_type">Save</button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <table class="table table-striped table-bordered">
    <thead>
        <tr>
            <!-- <th>ID</th> -->
            <th>Loan Type Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <!-- <td><?= $row['ltype_id'] ?></td> -->
                    <td><?= htmlspecialchars($row['ltype_name']) ?></td>
                    <td><?= htmlspecialchars($row['ltype_desc']) ?></td>
                    <td>
                        <!-- Edit Button -->
                        <button class="btn edit-btn" onclick="openEditModal(<?= $row['ltype_id'] ?>, '<?= htmlspecialchars($row['ltype_name']) ?>', '<?= htmlspecialchars($row['ltype_desc']) ?>')">Edit</button>
                        <!-- Delete Button -->
                        <a href="?delete_id=<?= $row['ltype_id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this loan type?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No loan types found.</td></tr>
        <?php endif; ?>
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


</div>

<!-- Modal for Editing Loan Type -->
<div class="modal" id="editLoanModal">
    <div class="modal-content">
        <span class="modal-close" id="closeEditLoanModal">&times;</span>
        <h3>Edit Loan Type</h3>
        <form action="" method="POST">
            <input type="hidden" name="ltype_id" id="editLtypeId">
            <label>Loan Type Name:</label><br>
            <input type="text" name="ltype_name" id="editLtypeName" required><br><br>
            <label>Description:</label><br>
            <textarea name="ltype_desc" id="editLtypeDesc" rows="3" required></textarea><br><br>
            <button class="btn add-btn" type="submit" name="update_loan_type">Update</button>
        </form>
    </div>
</div>

<script>
document.getElementById("openLoanModal").onclick = function () {
    document.getElementById("loanModal").style.display = "block";
};
document.getElementById("closeLoanModal").onclick = function () {
    document.getElementById("loanModal").style.display = "none";
};
document.getElementById("closeEditLoanModal").onclick = function () {
    document.getElementById("editLoanModal").style.display = "none";
};

window.onclick = function (event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
};

// Open Edit Modal and Populate Fields
function openEditModal(id, name, desc) {
    document.getElementById("editLtypeId").value = id;
    document.getElementById("editLtypeName").value = name;
    document.getElementById("editLtypeDesc").value = desc;
    document.getElementById("editLoanModal").style.display = "block";
}
</script>

<?php @include("footer.php"); ?>
