<?php
include '../db/database.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('Location: ./login.php');
    exit;
}

// --- ADD DOCUMENTATION ---
if (isset($_POST['add_doc'])) {
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);
    $collector_id = intval($_POST['collector_id']);
    $image_name = '';

    // Check if documentation already exists for this admin and collector
    $check_sql = "SELECT id FROM documentation WHERE admin_id = '$admin_id' AND collector_id = '$collector_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['message'] = 'This collector already has documentation.';
    } else {
        if (!empty($_FILES['image']['name'])) {
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_path = "../images/" . $image_name;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $_SESSION['message'] = 'Image upload failed.';
                $image_name = '';
            }
        }

        $insert = "INSERT INTO documentation (admin_id, collector_id, description, review, image, created_at)
                   VALUES ('$admin_id', '$collector_id', '$description', '$review', '$image_name', NOW())";
        $_SESSION['message'] = mysqli_query($conn, $insert)
            ? 'Documentation added successfully.'
            : 'Failed to add documentation.';
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- UPDATE DOCUMENTATION ---
if (isset($_POST['update_doc'])) {
    $id = intval($_POST['doc_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);
    $image_name = $_POST['current_image'];

    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = "../images/" . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $_SESSION['message'] = 'Image upload failed.';
        }
    }

    $query = "UPDATE documentation 
              SET description='$description', review='$review', image='$image_name' 
              WHERE id=$id AND admin_id=$admin_id";

    $_SESSION['message'] = mysqli_query($conn, $query)
        ? 'Documentation updated successfully.'
        : 'Documentation update failed.';
}

// --- DELETE DOCUMENTATION ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM documentation WHERE id=$id AND admin_id=$admin_id");
    $_SESSION['message'] = 'Documentation deleted successfully.';
}

// --- ADD PAID/KL/TYPE INFO TO PICKUP_REQUEST ---
if (isset($_POST['add_paid_kl'])) {
    $request_id = intval($_POST['request_id']);
    $collector_id = intval($_POST['collector_id']);
    $admin_id = $_SESSION['admin_id'];

    $paid = mysqli_real_escape_string($conn, $_POST['paid']);
    $kl = mysqli_real_escape_string($conn, $_POST['kl']);
    $junk_type = mysqli_real_escape_string($conn, $_POST['junk_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

    $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $user_address = mysqli_real_escape_string($conn, $_POST['user_address']);

    $check = mysqli_query($conn, "SELECT id FROM pickup_requests WHERE id = $request_id");

    if (mysqli_num_rows($check) > 0) {
        $update_sql = "UPDATE pickup_requests 
                       SET name = '$user_name',
                           address = '$user_address',
                           contact_number = '$contact_number',
                           status = 'Completed',
                           paid = '$paid',
                           kl = '$kl',
                           description = '$description',
                           junk_type = '$junk_type',
                           paid_at = NOW(),
                           collector_id = '$collector_id',
                           admin_id = '$admin_id'
                       WHERE id = $request_id";
    } else {
        $update_sql = "INSERT INTO pickup_requests 
                       (id, name, address, contact_number, paid, kl, junk_type, description, paid_at, status, collector_id, admin_id)
                       VALUES ($request_id, '$user_name', '$user_address', '$contact_number', '$paid', '$kl', '$junk_type', '$description', NOW(), 'Completed', '$collector_id', '$admin_id')";
    }

    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['message'] = 'Paid, KL, and Junk Type updated successfully.';
    } else {
        $_SESSION['message'] = 'Failed to update pickup request: ' . mysqli_error($conn);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- PAGINATION & SEARCH ---
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Count total
$count_sql = "SELECT COUNT(*) as total FROM documentation WHERE admin_id='$admin_id'";
if ($search != '') {
    $count_sql .= " AND (description LIKE '%$search%' OR review LIKE '%$search%')";
}
$total_result = mysqli_query($conn, $count_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Main query
$query = "SELECT DISTINCT documentation.*, users.first_name, users.last_name, users.address, 
                 pickup_requests.paid, pickup_requests.kl, pickup_requests.junk_type
          FROM documentation
          LEFT JOIN users ON documentation.collector_id = users.id
          LEFT JOIN pickup_requests ON documentation.collector_id = pickup_requests.collector_id 
                                     AND documentation.customer_id = pickup_requests.customer_id
          WHERE documentation.admin_id = '$admin_id'";

if ($search != '') {
    $query .= " AND (documentation.description LIKE '%$search%' OR documentation.review LIKE '%$search%' OR users.first_name LIKE '%$search%' OR users.last_name LIKE '%$search%' OR users.address LIKE '%$search%')";
}

$query .= " ORDER BY documentation.created_at DESC LIMIT $limit OFFSET $offset";
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

    <!-- Add Documentation Button -->
<div class="mb-3 text-left">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addDocModal"> Add Documentation</button>
</div>

<!-- Add Documentation Modal -->
<div class="modal fade" id="addDocModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Documentation</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Optional collector_id, adjust as needed -->
          


             <div class="form-group">
    <label>Select Collector (User)</label>
    <select name="collector_id" class="form-control" required>
        <option value="">-- Select Collector --</option>
        <?php
        // Fetch all collector users
        $collectors = mysqli_query($conn, "SELECT id, first_name, last_name FROM users WHERE user_type = 'collector'");

        while ($collector = mysqli_fetch_assoc($collectors)) {
            $collectorId = $collector['id'];
            $collectorName = htmlspecialchars($collector['first_name'] . ' ' . $collector['last_name']);

            // Check if this collector already has documentation
            $checkDoc = mysqli_query($conn, "SELECT id FROM documentation WHERE collector_id = '$collectorId'");
            $hasDoc = mysqli_num_rows($checkDoc) > 0;

            // Mark as disabled if already added
            $disabled = $hasDoc ? 'disabled' : '';

            echo "<option value='{$collectorId}' $disabled>$collectorName" . ($hasDoc ? " (Already Added)" : "") . "</option>";
        }
        ?>
    </select>
</div>



                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Review</label>
                    <input type="text" name="review" class="form-control" required>
                </div>

                <div class="form-group">
                 <label>Upload Image</label><br>
                    <div class="upload-btn-wrapper">
                        <button type="button" class="btn-upload btn btn-success" onclick="document.getElementById('imageInput').click();">Choose Image</button>
                        <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;" onchange="previewImage(this);">
                    </div>
                    <br>
                    <img id="imagePreview" src="#" alt="Image Preview" style="display: none; width: 100px; margin-top: 10px;">
                </div>



                
            </div>
            <div class="modal-footer">
                <button type="submit" name="add_doc" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
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
                <th>Paid</th>
                <th>KL</th>
                <th>Image</th>
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
                <td><?= htmlspecialchars($row['paid']) ?></td>
                        <td><?= htmlspecialchars($row['kl']) ?></td></td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="../images/<?= $row['image'] ?>" width="100">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= date('F j, Y', strtotime($row['created_at'])) ?></td>
                <td>
                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#paidKlModal<?= $row['id'] ?>">Add Paid</button>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal<?= $row['id'] ?>">Edit</button>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    <a href="receipt.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Receipt</a>

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



<!-- Add Paid & KL Modal -->
<div class="modal fade" id="paidKlModal<?= $row['id'] ?? 0 ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Paid  </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Hidden fields for request and collector -->
                <input type="hidden" name="request_id" value="<?= $row['id'] ?? 0 ?>">
                <input type="hidden" name="collector_id" value="<?= $row['collector_id'] ?? 0 ?>">

                <!-- Hidden fields for name, number, address -->
                <input type="hidden" name="user_name" value="<?= htmlspecialchars($row['name'] ?? '') ?>">
                <input type="hidden" name="contact_number" value="<?= htmlspecialchars($row['contact_number'] ?? '') ?>">
                <input type="hidden" name="user_address" value="<?= htmlspecialchars($row['address'] ?? '') ?>">


                <!-- Collector Selection -->
                <div class="form-group">
                    <label for="collector_id">Select Collector to Ensure:</label>
                    <select name="collector_id" class="form-control" required>
                        <option value="">-- Select --</option>
                        <?php
                        $collectorRes = mysqli_query($conn, "SELECT id, first_name, last_name FROM users WHERE user_type = 'collector'");
                        while ($collector = mysqli_fetch_assoc($collectorRes)) {
                            echo "<option value='{$collector['id']}'>{$collector['first_name']} {$collector['last_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                

                <!-- Paid -->
                <div class="form-group">
                    <label for="paid">Paid:</label>
                    <input type="text" name="paid" class="form-control" required>
                </div>

                <!-- KL -->
                <div class="form-group">
                    <label for="kl">KL:</label>
                    <input type="text" name="kl" class="form-control" required>
                </div>

                <!-- Junk Type -->
                <div class="form-group">
                    <label for="junk_type" class="form-label">Product Type</label>
                    <select class="form-control" id="junk_type" name="junk_type" required>
                        <option value="">-- Select Type --</option>
                        <option value="Metal">Metal</option>
                        <option value="Plastic">Plastic</option>
                        <option value="Paper">Paper</option>
                        <option value="Glass">Glass</option>
                        <option value="Wood">Wood</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Fabric">Fabric</option>
                        <option value="Ceramic">Ceramic</option>
                        <option value="Rubber">Rubber</option>
                        <option value="Copper">Copper</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add_paid_kl" class="btn btn-success">Save</button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
}
</script>

<script>
$(document).ready(function() {
    $('select[name="collector_id"]').select2({
        width: '100%',
        placeholder: "Search for a user",
        allowClear: true
    });
});
</script>
<script>
document.querySelector("input[name='image']").addEventListener("change", function (event) {
    const reader = new FileReader();
    reader.onload = function () {
        const output = document.getElementById('imagePreview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
});
</script>

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