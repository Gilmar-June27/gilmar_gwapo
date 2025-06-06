<?php @include("../header.php"); ?>
<?php @include("../../navbar.php"); ?>

<div class="container mt-4">
    <button id="addJunkBtn" class="btn btn-primary mb-3">Add Junk</button>

    <div class="card">
        <div class="card-header">
            <h4>Junk List</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product Type</th>
                        <th>Image</th>
                        <th>Pricing (per kilo)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="junkTableBody">
                    <!-- Dynamic rows will be added here -->
                </tbody>
            </table>
        </div>
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
                <form id="junkForm">
                    <div class="mb-3">
                        <label for="junkType" class="form-label">Product Type</label>
                        <select class="form-control" id="junkType" required>
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
                            <input type="file" class="form-control d-none" id="junkImage" accept="image/*">
                            <button type="button" class="btn btn-primary" id="browseFiles">Browse Files</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="junkPrice" class="form-label">Price</label>
                        <div class="d-flex">
                            <input type="number" class="form-control me-2" id="junkPrice" required placeholder="Enter price">
                            <select class="form-control" id="priceUnit">
                                <option value="1kg">1 kg</option>
                                <option value="2kg">2 kg</option>
                                <option value="5kg">5 kg</option>
                                <option value="10kg">10 kg</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

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
        handleFileUpload(event.target.files[0]);
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

    function handleFileUpload(file) {
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewContainer').innerHTML = `
                    <div style="position: relative; display: inline-block;">
                        <img src="${e.target.result}" class="img-thumbnail" width="100">
                        <button type="button" class="btn btn-dark btn-sm" style="position: absolute; top: 0; right: 0;" onclick="removeImage()">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                    </div>
                `;
                currentImageUrl = e.target.result; // Update currentImageUrl with new image
            };
            reader.readAsDataURL(file);
        }
    }

    function removeImage() {
        document.getElementById('previewContainer').innerHTML = '';
        document.getElementById('junkImage').value = '';
        currentImageUrl = ''; // Clear the current image URL
    }

    document.getElementById('junkForm').addEventListener('submit', function(event) {
        event.preventDefault();
        let type = document.getElementById('junkType').value;
        let imageFile = document.getElementById('junkImage').files[0];
        let price = document.getElementById('junkPrice').value;
        let unit = document.getElementById('priceUnit').value;
        let imageUrl = currentImageUrl || (imageFile ? URL.createObjectURL(imageFile) : 'https://via.placeholder.com/100');

        if (currentEditingRow) {
            // Update the existing row
            currentEditingRow.children[0].innerText = type;
            currentEditingRow.children[1].innerHTML = `<img src="${imageUrl}" class="img-thumbnail" width="100">`;
            currentEditingRow.children[2].innerText = `₱${price} per ${unit}`;
        } else {
            // Add a new row
            let row = `<tr>
                          <td>${type}</td>
                          <td><img src="${imageUrl}" class="img-thumbnail" width="100"></td>
                          <td>₱${price} per ${unit}</td>
                          <td>
                              <button class="btn btn-warning btn-sm" onclick="editJunk(this)">✏ Edit</button>
                              <button class="btn btn-danger btn-sm" onclick="deleteJunk(this)">🗑 Delete</button>
                          </td>
                      </tr>`;
            document.getElementById('junkTableBody').insertAdjacentHTML('beforeend', row);
        }

        new bootstrap.Modal(document.getElementById('junkModal')).hide();
        document.getElementById('junkForm').reset();
        document.getElementById('previewContainer').innerHTML = ''; // Clear preview
        currentImageUrl = ''; // Reset image URL for future entries
    });

    function deleteJunk(btn) {
        btn.parentElement.parentElement.remove();
    }

    function editJunk(btn) {
        let row = btn.parentElement.parentElement;
        let type = row.children[0].innerText;
        let priceData = row.children[2].innerText.split(' ');
        let price = priceData[0].replace('₱', '');
        let unit = priceData.slice(2).join(' ');
        let image = row.children[1].getElementsByTagName('img')[0].src;

        // Set values in the form for editing
        document.getElementById('junkType').value = type;
        document.getElementById('junkPrice').value = price;
        document.getElementById('priceUnit').value = unit;
        currentImageUrl = image; // Retain the current image

        // Show the image preview in the modal
        document.getElementById('previewContainer').innerHTML = `<img src="${image}" class="img-thumbnail" width="100">`;

        currentEditingRow = row; // Set the current row for editing

        new bootstrap.Modal(document.getElementById('junkModal')).show();
    }
</script>

<?php @include("footer.php"); ?>
