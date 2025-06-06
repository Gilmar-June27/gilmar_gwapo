<?php @include("header.php")?>
<?php @include("navbar.php")?>
 
<div class="container mt-4">
<div class="az-content-breadcrumb">
        <span>Junk</span>
        <span>Customer</span>           
    </div>
    <h2 class="az-content-title">Customer</h2>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list"></i> Users</h5>
            <div>
                <select id="filter" class="form-select form-select-sm d-inline w-auto">
                    <option value="latest">Latest Requests</option>
                    <option value="oldest">Oldest Requests</option>
                </select>
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#historyModal">History</button>
            </div>
        </div>
        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Users</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>User Type</th>
                        
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="customerTable">
                    <tr>
                        <td><img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="Alice"> <strong>Alice Mayer</strong></td>
                        <td>Inquiry about item availability</td>
                        <td>2025-03-21</td>
                        
                        <td>Customer</td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                        <td>
                            <button class="btn btn-success btn-sm">Approve</button>
                            <button class="btn btn-danger btn-sm">Decline</button>
                        </td>
                    </tr>
                    <tr>
                        <td><img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="Kate"> <strong>Kate Moss</strong></td>
                        <td>Request for price negotiation</td>
                        <td>2025-03-20</td>
                     
                        <td>Collector</td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td>
                            <button class="btn btn-success btn-sm" disabled>Approve</button>
                            <button class="btn btn-danger btn-sm">Decline</button>
                        </td>
                    </tr>
                   
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // 🔹 Store only the place name and redirect to index.php
    function redirectToMap(placeName) {
        localStorage.setItem("mapPlace", placeName);
        window.location.href = "index.php"; // Redirect to homepage
    }

    // 🔹 Sorting functionality for the table
    document.getElementById('filter').addEventListener('change', function () {
        let table = document.getElementById('customerTable');
        let rows = Array.from(table.rows);
        let sortOrder = this.value === 'latest' ? -1 : 1;

        rows.sort((a, b) => {
            let dateA = new Date(a.cells[2].innerText);
            let dateB = new Date(b.cells[2].innerText);
            return (dateA - dateB) * sortOrder;
        });

        table.innerHTML = '';
        rows.forEach(row => table.appendChild(row));
    });
</script>

<?php @include("footer.php") ?>
