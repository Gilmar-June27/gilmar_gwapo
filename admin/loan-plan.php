<?php
@include '../db/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:./login.php');
    exit;
}

// Handle insert
if (isset($_POST['add_loan_plan'])) {
    $month = $_POST['lplan_month'];
    $interest = $_POST['lplan_interest'];
    $penalty = $_POST['lplan_penalty'];

    mysqli_query($conn, "INSERT INTO loan_plan (lplan_month, lplan_interest, lplan_penalty) VALUES ('$month', '$interest', '$penalty')");
}

// Handle update
if (isset($_POST['update_loan_plan'])) {
    $lplan_id = $_POST['lplan_id'];
    $month = $_POST['lplan_month'];
    $interest = $_POST['lplan_interest'];
    $penalty = $_POST['lplan_penalty'];

    mysqli_query($conn, "UPDATE loan_plan SET lplan_month = '$month', lplan_interest = '$interest', lplan_penalty = '$penalty' WHERE lplan_id = $lplan_id");
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM loan_plan WHERE lplan_id = $delete_id");
}

// Get pagination and search
$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 5;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $entries_per_page;

// Filtered query
$query = "
    SELECT * FROM loan_plan 
    WHERE lplan_month LIKE '%$search%' OR lplan_interest LIKE '%$search%' OR lplan_penalty LIKE '%$search%' 
    ORDER BY lplan_id DESC 
    LIMIT $entries_per_page OFFSET $offset
";
$plans = mysqli_query($conn, $query);


// Count total records for pagination
$total_query = "
    SELECT COUNT(*) AS total FROM loan_plan 
    WHERE lplan_month LIKE '%$search%' OR lplan_interest LIKE '%$search%' OR lplan_penalty LIKE '%$search%'
";
$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_fetch_assoc($total_result)['total'];
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
        <span>Loan Plan</span>           
    </div>
    <h2 class="az-content-title">Loan Plan</h2>
    <button class="btn add-btn" id="openLoanModal">Add Loan Plan</button>

    <div id="loanModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" id="closeLoanModal">&times;</span>
            <h4>Add New Loan Plan</h4>
            <form method="POST">
                <div class="mb-3">
                    <label for="lplan_month" class="form-label">Months Duration</label>
                    <input type="number" class="form-control" name="lplan_month" required>
                </div>

                <div class="mb-3">
                    <label for="lplan_interest" class="form-label">Interest (%)</label>
                    <input type="number" step="0.01" class="form-control" name="lplan_interest" required>
                </div>

                <div class="mb-3">
                    <label for="lplan_penalty" class="form-label">Penalty (%)</label>
                    <input type="number" class="form-control" name="lplan_penalty" required>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" name="add_loan_plan" class="btn add-btn me-2">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search + Entries -->
    <form method="get" style="display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center; width: 100%;">
        <div class="s" style="flex: 1; display: flex; justify-content: flex-start;">
            <label for="entries">Show</label>
            <select name="entries" id="entries" onchange="this.form.submit()" class="form-control">
                <?php foreach ([5, 10, 25, 50] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $entries_per_page == $opt ? 'selected' : '' ?>><?= $opt ?> entries</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="s" style="flex: 1; justify-content: flex-end;">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search loan plans..." style="padding: 6px; width: 200px;">
            <button type="submit" class="btn add-btn" style="margin-left: 8px;">Search</button>
        </div>
    </form>

    <!-- Table -->
    <table class="table table-striped table-bordered">
    <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Months</th>
                <th>Interest (%)</th>
                <th>Penalty (%)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($plans) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($plans)): ?>
                    <tr>
                        <!-- <td><?= $row['lplan_id'] ?></td> -->
                        <td><?= $row['lplan_month'] ?></td>
                        <td><?= $row['lplan_interest'] ?></td>
                        <td><?= $row['lplan_penalty'] ?></td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn edit-btn" onclick="openEditModal(<?= $row['lplan_id'] ?>, '<?= htmlspecialchars($row['lplan_month']) ?>', '<?= htmlspecialchars($row['lplan_interest']) ?>', '<?= htmlspecialchars($row['lplan_penalty']) ?>')">Edit</button>
                            <!-- Delete Button -->
                            <a href="?delete_id=<?= $row['lplan_id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this loan plan?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No loan plans found.</td></tr>
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


    <!-- Modal for Editing Loan Plan -->
    <div class="modal" id="editLoanModal">
        <div class="modal-content">
            <span class="modal-close" id="closeEditLoanModal">&times;</span>
            <h3>Edit Loan Plan</h3>
            <form action="" method="POST">
                <input type="hidden" name="lplan_id" id="editLplanId">
                <label>Months Duration:</label><br>
                <input type="number" name="lplan_month" id="editLplanMonth" required><br><br>
                <label>Interest (%):</label><br>
                <input type="number" step="0.01" name="lplan_interest" id="editLplanInterest" required><br><br>
                <label>Penalty (%):</label><br>
                <input type="number" name="lplan_penalty" id="editLplanPenalty" required><br><br>
                <button class="btn add-btn" type="submit" name="update_loan_plan">Update</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Open Edit Modal
    function openEditModal(id, month, interest, penalty) {
        document.getElementById("editLplanId").value = id;
        document.getElementById("editLplanMonth").value = month;
        document.getElementById("editLplanInterest").value = interest;
        document.getElementById("editLplanPenalty").value = penalty;
        document.getElementById("editLoanModal").style.display = "block";
    }

    // Close Edit Modal
    document.getElementById("closeEditLoanModal").onclick = function () {
        document.getElementById("editLoanModal").style.display = "none";
    };

    // Close Modal if clicked outside
    window.onclick = function (event) {
        if (event.target.id === "editLoanModal") {
            document.getElementById("editLoanModal").style.display = "none";
        }
    };

document.getElementById("openLoanModal").onclick = function () {
    document.getElementById("loanModal").style.display = "block";
};

document.getElementById("closeLoanModal").onclick = function () {
    document.getElementById("loanModal").style.display = "none";
};

window.onclick = function (event) {
    if (event.target.id === "loanModal") {
        document.getElementById("loanModal").style.display = "none";
    }
};
</script>

<?php @include("footer.php") ?>
