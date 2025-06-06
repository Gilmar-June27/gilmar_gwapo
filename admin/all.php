<?php
include '../db/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:./login.php');
    exit;
}

// Prevent auto-insert on page reload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Handle Add Product
    if (isset($_POST['add'])) {
        $garbage_price = $_POST['garbage_price'];
        $kl = $_POST['kl'];
        $junk_type = $_POST['junk_type'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_size = $_FILES['image']['size'];
            $image_folder = '../images/' . $image_name;

            if ($image_size > 50000000) {
                $_SESSION['message'] = 'Image exceeds the size limit of 50MB.';
            } else {
                if (move_uploaded_file($image_tmp_name, $image_folder)) {
                    $query = "INSERT INTO `junk_price` (garbage_price, kl, image, junk_type, admin_id) 
                              VALUES('$garbage_price', '$kl', '$image_name', '$junk_type', '$admin_id')";
                    $add_product_query = mysqli_query($conn, $query);
                    $_SESSION['message'] = $add_product_query ? 'Product added successfully!' : 'Product could not be added!';
                } else {
                    $_SESSION['message'] = 'Failed to upload image.';
                }
            }
        } else {
            $_SESSION['message'] = 'No image uploaded.';
        }
    }

    if (isset($_POST['delete'])) {
        $delete_id = $_POST['delete_id'];
        mysqli_query($conn, "DELETE FROM junk_price WHERE id = '$delete_id' ");
        $_SESSION['message'] = 'Junk deleted.';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
   

    // Handle Update Product
   // Handle Update Product
// Handle Update Product
// Handle Update Product
if (isset($_POST['update_product'])) {
    $update_id = $_POST['update_id'];
    $junk_type = $_POST['junk_type'];
    $update_price = $_POST['update_price'];
    $update_kg = $_POST['update_kg'];
    $image_query = '';

    if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == 0) {
        $image_name = $_FILES['update_image']['name'];
        $image_tmp_name = $_FILES['update_image']['tmp_name'];
        $image_size = $_FILES['update_image']['size'];
        $image_folder = '../images/' . $image_name;

        if ($image_size > 50000000) {
            $_SESSION['message'] = 'Image exceeds 50MB size limit.';
        } else {
            if (move_uploaded_file($image_tmp_name, $image_folder)) {
                $image_query = ", image = '$image_name'";
            } else {
                $_SESSION['message'] = 'Failed to upload new image.';
            }
        }
    }

    $update_sql = "UPDATE `junk_price` SET 
                    junk_type = '$junk_type',
                    garbage_price = '$update_price',
                    kl = '$update_kg'
                    $image_query
                  WHERE id = '$update_id' AND admin_id = '$admin_id'";

    $update_result = mysqli_query($conn, $update_sql);

    $_SESSION['message'] = $update_result ? 'Product updated successfully!' : 'Product could not be updated!';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}






    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM `junk_price` WHERE `admin_id` = '$admin_id'");




// Pagination & Search logic
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM  junk_price WHERE admin_id='$admin_id'";
if ($search != '') {
    $count_sql .= " AND (junk_type LIKE '%$search%' OR garbage_price LIKE '%$search%' OR kl LIKE '%$search% ')";
}
$total_result = mysqli_query($conn, $count_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$query = "SELECT junk_price.*, users.first_name, users.last_name, users.address
          FROM junk_price
          LEFT JOIN users ON junk_price.admin_id = users.id
          WHERE junk_price.admin_id='$admin_id'";

if ($search != '') {
    $query .= " AND (junk_price.junk_type LIKE '%$search%' OR junk_price.garbage_price LIKE '%$search%' OR users.first_name LIKE '%$search%' OR users.last_name LIKE '%$search%' OR users.address LIKE '%$search%')";
}

$query .= " ORDER BY junk_price.created_at DESC LIMIT $limit OFFSET $offset";
$requests = mysqli_query($conn, $query);
?>



<?php @include("header.php"); ?>
<?php @include("navbar.php"); ?>

<div class="container mt-4">
<div class="az-content-breadcrumb">
            <span>Junk</span>
            <span>Junk</span>           
          </div>
          <h2 class="az-content-title">Junk</h2>
<?php if (isset($_SESSION['message'])) { ?>
        <div class="alert alert-info text-center">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php } ?>
    <button id="addJunkBtn" class="btn btn-primary mb-3">Add Junk</button>

   

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
                        <th>Product Type</th>
                        <th>Image</th>
                        <th>Pricing </th>
                        <th>Kilo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($requests) > 0): while ($row = mysqli_fetch_assoc($requests)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['junk_type']); ?></td>
                            <td><img src="../images/<?php echo htmlspecialchars($row['image']); ?>" width="50" alt="Image"></td>
                            <td><?php echo htmlspecialchars($row['garbage_price']); ?></td>
                            <td><?php echo htmlspecialchars($row['kl']);?></td>
                            <td>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal<?= $row['id'] ?>">Edit</button>
                    <form method="post" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Junk Price</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="update_id" value="<?= $row['id'] ?>">

        <div class="form-group">
          <label>Product Type</label>
          <input type="text" name="junk_type" class="form-control" value="<?= htmlspecialchars($row['junk_type']) ?>" required>
        </div>

        <div class="form-group">
          <label>Garbage Price</label>
          <input type="text" name="update_price" class="form-control" value="<?= htmlspecialchars($row['garbage_price']) ?>" required>
        </div>

        <div class="form-group">
          <label>Garbage Per (KG)</label>
          <input type="text" name="update_kg" class="form-control" value="<?= htmlspecialchars($row['kl']) ?>" required>
        </div>

        <div class="form-group">
    <label>Upload New Image</label><br>
    <!-- Hidden file input -->
    <input type="file" id="imageUpload<?= $row['id'] ?>" name="update_image" accept="image/*" style="display: none;" onchange="previewImage(this, <?= $row['id'] ?>)">
    
    <!-- Button to trigger file input -->
    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('imageUpload<?= $row['id'] ?>').click();">Browse Files</button>
    
    <!-- Container to preview selected image -->
    <div id="previewContainer<?= $row['id'] ?>" class="mt-2">
        <?php if (!empty($row['image'])): ?>
            <img src="../images/<?= htmlspecialchars($row['image']) ?>" alt="Current Image" style="max-width: 100px;">
        <?php endif; ?>
    </div>
</div>

      </div>
      <div class="modal-footer">
        <button type="submit" name="update_product" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

                </td>
                        </tr>
                        <?php endwhile; else: ?>
            <tr>
                <td colspan="7" class="text-center">No data found.</td>
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
</div>





<!-- Add Junk Modal -->
<div class="modal fade" id="junkModal" tabindex="-1" aria-labelledby="junkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="junkModalLabel">Add Junk</h5>
                <button type="button" class="btn-close" style="border: none;" data-bs-dismiss="modal" aria-label="Close" id="closeJunkModal"></button>
            </div>
            <div class="modal-body">
                <form id="junkForm" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="junkType" class="form-label">Product Type</label>
                        <select class="form-control" id="junkType" name="junk_type" required>
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
                    <div class="mb-3">
                        <label for="junkImage" class="form-label">Upload Image</label>
                        <div class="upload-area border rounded p-3 text-center" id="dropZone" style="border: 2px dashed #ccc; cursor: pointer; position: relative;">
                            <div id="previewContainer" class="mb-2"></div>
                            <p>Drag & Drop files here<br>or</p>
                            <input type="file" name="image" class="form-control d-none" id="junkImage"  accept="image/jpg, image/jpeg, image/png">
                            <button type="button" name="image" class="btn btn-primary" id="browseFiles">Browse Files</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="junkPrice" class="form-label">Price</label>
                        <div class="d-flex">
                            <input type="number" class="form-control me-2" id="junkPrice" name="garbage_price" required placeholder="Enter price">
                            <select class="form-control" name="kl" id="priceUnit">
                                <option value="1kg">1 kg</option>
                                <option value="2kg">2 kg</option>
                                <option value="5kg">5 kg</option>
                                <option value="10kg">10 kg</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>






<script>
function previewImage(input, id) {
    const file = input.files[0];
    const container = document.getElementById('previewContainer' + id);
    container.innerHTML = ''; // Clear previous

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100px';
            container.appendChild(img);
        }
        reader.readAsDataURL(file);
    }
}
</script>

<script>
    let currentEditingRow = null;
    let currentImageUrl = ''; // Store the current image URL for editing

    document.getElementById('addJunkBtn').addEventListener('click', function() {
        currentEditingRow = null; // Clear any previous edit
        currentImageUrl = ''; // Reset image URL
        new bootstrap.Modal(document.getElementById('junkModal')).show();
    });

    document.getElementById('closeJunkModal').addEventListener('click', function() {
        new bootstrap.Modal(document.getElementById('junkModal')).hide();
    });

    document.getElementById('browseFiles').addEventListener('click', function() {
        document.getElementById('junkImage').click();
    });

    document.getElementById('junkImage').addEventListener('change', function(event) {
        handleFileUpload(event.target.files);
    });

    document.getElementById('dropZone').addEventListener('dragover', function(event) {
        event.preventDefault();
        this.style.borderColor = "blue";
    });

    document.getElementById('dropZone').addEventListener('dragleave', function(event) {
        this.style.borderColor = "#ccc";
    });

    document.getElementById('dropZone').addEventListener('drop', function(event) {
        event.preventDefault();
        this.style.borderColor = "#ccc";
        const file = event.dataTransfer.files[0];
        if (file) {
            handleFileUpload(file);
        }
    });

    function handleFileUpload(files) {
        document.getElementById('previewContainer').innerHTML = '';
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewContainer').innerHTML += `
                    <div style="position: relative; display: inline-block;">
                        <img src="${e.target.result}" class="img-thumbnail" width="100">
                        <button type="button" class="btn btn-dark btn-sm" style="position: absolute; top: 0; right: 0;" onclick="removeImage()">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        });
    }

    function removeImage() {
        document.getElementById('previewContainer').innerHTML = '';
        document.getElementById('imageUpload').value = '';
    }

    // Populate Update Modal
function populateUpdateModal(id, productType, price, kg) {
    document.getElementById('updateId').value = id;
    document.getElementById('updateProductType').value = productType;
    document.getElementById('updatePrice').value = price;
    document.getElementById('updateKg').value = kg;
}

document.querySelectorAll('.btn-success').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const productType = this.getAttribute('data-product-type');
        const price = this.getAttribute('data-price');
        const kg = this.getAttribute('data-kg');
        
        // Populate the modal with the existing values
        populateUpdateModal(id, productType, price, kg);
        
        // Show the update modal
        new bootstrap.Modal(document.getElementById('updateModal')).show();
    });
});

// Function to populate the modal with values
function populateUpdateModal(id, productType, price, kg) {
    document.getElementById('updateId').value = id;
    document.getElementById('updateProductType').value = productType;
    document.getElementById('updatePrice').value = price;
    document.getElementById('updateKg').value = kg;
}

// Populate Update Modal
function populateUpdateModal(id, productType, price, kg) {
    document.getElementById('updateId').value = id;
    document.getElementById('updateProductType').value = productType;
    document.getElementById('updatePrice').value = price;
    document.getElementById('updateKg').value = kg;
}

// Handle Update button click for each row
document.querySelectorAll('.btn-success').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const productType = this.getAttribute('data-product-type');
        const price = this.getAttribute('data-price');
        const kg = this.getAttribute('data-kg');
        
        // Populate the modal with the existing values
        populateUpdateModal(id, productType, price, kg);
        
        // Show the update modal
        new bootstrap.Modal(document.getElementById('updateModal')).show();
    });
});

// Close modal after update
document.querySelector('.btn-success').addEventListener('click', function() {
    new bootstrap.Modal(document.getElementById('updateModal')).hide();
});

</script>

<?php @include("footer.php"); ?>
