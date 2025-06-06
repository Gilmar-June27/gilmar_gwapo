<?php
include './db/database.php';
session_start();

$collector_id = $_SESSION['collector_id'] ?? null;
if (!$collector_id) {
    header('Location: login.php');
    exit;
}

// --- UPDATE DOCUMENTATION ---
if (isset($_POST['update_doc'])) {
    $id = intval($_POST['doc_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);
    $image_name = $_POST['current_image'];

    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = "images/" . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $_SESSION['message'] = 'Image upload failed.';
        }
    }

    $query = "UPDATE documentation 
              SET description='$description', review='$review', image='$image_name' 
              WHERE id=$id AND collector_id=$collector_id";

    $_SESSION['message'] = mysqli_query($conn, $query) 
        ? 'Documentation updated successfully.' 
        : 'Documentation update failed.';
}

// --- DELETE DOCUMENTATION ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM documentation WHERE id=$id AND collector_id=$collector_id");
    $_SESSION['message'] = 'Documentation deleted successfully.';
}

// Pagination & Search logic
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM documentation WHERE collector_id='$collector_id'";
if ($search != '') {
    $count_sql .= " AND (name LIKE '%$search%' OR contact_number LIKE '%$search%' OR junk_type LIKE '%$search%' OR address LIKE '%$search%')";
}
$total_result = mysqli_query($conn, $count_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$query = "SELECT documentation.*, users.first_name, users.last_name, users.address, 
                 pickup_requests.paid, pickup_requests.kl, pickup_requests.junk_type
          FROM documentation
          LEFT JOIN users ON documentation.customer_id = users.id
          LEFT JOIN pickup_requests ON documentation.customer_id = pickup_requests.customer_id 
                                    AND documentation.collector_id = pickup_requests.collector_id
          WHERE documentation.collector_id = '$collector_id' AND paid='Unpaid'";


if ($search != '') {
    $query .= " AND (documentation.description LIKE '%$search%' OR documentation.review LIKE '%$search%' OR users.first_name LIKE '%$search%' OR users.last_name LIKE '%$search%' OR users.address LIKE '%$search%')";
}

$query .= " ORDER BY documentation.created_at DESC LIMIT $limit OFFSET $offset ";
$requests = mysqli_query($conn, $query);
?>

<?php @include("header.php"); ?>
<?php @include("navbar.php"); ?>

<div class="container mt-4">
    <div class="az-content-breadcrumb">
        <span>Junk</span>
        <span>Documentation</span>           
    </div>
    <h2 class="az-content-title">Documentation</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info text-center">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <div style="text-align: right; margin-bottom: 10px;">
   <a href="postcateg.php"  class="btn btn-secondary" onclick="filterLoans('pending')">Paid</a>
   <a href="unpaid.php"  class="btn btn-primary" onclick="filterLoans('Unpaid')">Unpaid</a>

   
</div>


    <form method="GET" class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <select name="limit" class="form-select" onchange="this.form.submit()">
                <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5 entries</option>
                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 entries</option>
                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20 entries</option>
            </select>
        </div>
        <div style="display:flex;justify-content:space-evenly; position: relative;">
            <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">&nbsp;
            <button type="submit" class="btn btn-primary">Apply</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Customer Name</th>
                <th>Customer Address</th>
                <th>Description</th>
                <th>Review</th>
                <th>Image</th>
                <th>Junk Type</th>
                <th>Paid</th>
                <th>KL</th>

                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($requests) > 0): while ($row = mysqli_fetch_assoc($requests)): ?>
            <tr>
                <td><?= htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= !empty($row['description']) ? htmlspecialchars($row['description']) : 'N/A' ?></td>
                <td><?= !empty($row['review']) ? htmlspecialchars($row['review']) : 'N/A' ?></td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="images/<?= $row['image'] ?>" width="100">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['junk_type'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['paid'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['kl'] ?? 'N/A') ?></td>

                <td><?= date('F j, Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal<?= $row['id'] ?>">Edit</button>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    <a href="receipt.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm" target="_blank">Receipt</a>

                    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <form method="POST" enctype="multipart/form-data" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Documentation</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="doc_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="current_image" value="<?= $row['image'] ?>">

                                    <div class="form-group">
                                        <label>Description</label>
                                        <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($row['description']) ?>" >
                                    </div>

                                    <div class="form-group">
                                        <label>Review</label>
                                        <input type="text" name="review" class="form-control" value="<?= htmlspecialchars($row['review']) ?>" >
                                    </div>

                                    <div class="form-group">
                                        <label>Upload New Image</label><br>
                                        <input type="file" id="imageUpload<?= $row['id'] ?>" name="image" accept="image/*" style="display: none;">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('imageUpload<?= $row['id'] ?>').click();">Browse Files</button>
                                        <div id="previewContainer<?= $row['id'] ?>" class="mt-2"></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="update_doc" class="btn btn-primary">Update</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="7" class="text-center">No documentation found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&search=<?= $search ?>">Previous</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>&search=<?= $search ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&search=<?= $search ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const previews = document.querySelectorAll('[id^="imageUpload"]');
    previews.forEach(input => {
        input.addEventListener("change", function (e) {
            const file = e.target.files[0];
            const id = input.id.replace("imageUpload", "");
            const container = document.getElementById("previewContainer" + id);
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    container.innerHTML = `
                        <div style="position: relative; display: inline-block;">
                            <img src="${event.target.result}" class="img-thumbnail" width="100">
                            <button type="button" class="btn btn-dark btn-sm" style="position: absolute; top: 0; right: 0;" onclick="removeImage('${id}')">
                                <i class="fa fa-times-circle"></i>
                            </button>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    });
});

function removeImage(id) {
    document.getElementById("previewContainer" + id).innerHTML = '';
    document.getElementById("imageUpload" + id).value = '';
}
</script>

<?php @include("footer.php"); ?>