<?php

include '../db/database.php';
session_start();

// Ensure customer_id is set
// Get customer info (assume $fetch already populated)
$query = "SELECT first_name, address, number FROM users WHERE id = '$customer_id'";
$result = mysqli_query($conn, $query);
$fetch = mysqli_fetch_assoc($result);

// Get all collectors
$collectors = [];
$collectorQuery = "SELECT id, first_name, address FROM users WHERE user_type = 'collector'";
$collectorResult = mysqli_query($conn, $collectorQuery);
while ($row = mysqli_fetch_assoc($collectorResult)) {
    $collectors[] = $row;
}

// Get junk price list
$junkPrices = [];
$priceQuery = "SELECT * FROM junk_price WHERE collector_id='$collector_id'";
$priceResult = mysqli_query($conn, $priceQuery);
while ($row = mysqli_fetch_assoc($priceResult)) {
    $junkPrices[] = $row;
}

if (isset($_POST['add_request']))  {
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $preferred_date = mysqli_real_escape_string($conn, $_POST['preferred_date']);
    $collector_id = mysqli_real_escape_string($conn, $_POST['collector_id']);


    // Extract junk_type, collector_id, and kl from the selected option
    $junk_data = explode(',', $_POST['junk_data']);
    $junk_type = mysqli_real_escape_string($conn, $junk_data[0]);
 
    $kl = mysqli_real_escape_string($conn, $junk_data[2]);

    // Insert into DB
    $query = "INSERT INTO pickup_requests 
        (customer_id, collector_id, name, address, contact_number, junk_type, description, preferred_date, kl, paid) 
        VALUES 
        ('$customer_id', '$collector_id', '$name', '$address', '$contact_number', '$junk_type', '$description', '$preferred_date', '$kl', 'Unpaid')";

        // Send collector_notification
    $msg = "Pickup request from customer.";
    mysqli_query($conn, "INSERT INTO customer_notification ( collector_id, message, customer_id) 
                         VALUES ( $collector_id,  '$msg',  '$customer_id')");

    if (mysqli_query($conn, $query)) {
        // echo "<script>alert('Request submitted successfully!');</script>";
        $_SESSION['message'] = 'Request submitted successfully!';
    } else {
        // echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        $_SESSION['message'] = 'Failed to add Request.';
    }
    header("Location:request_pickup.php ");
    
}


?>

<?php @include("header.php");?>

<style>
    

    #map {
        height: 77vh;
        width: 97%;
        border-radius: 10px;
        margin: -76px auto;
        z-index: 1;
    }

    .search-container {
        position: relative;
        transform: translateY(-155px) translateX(-147px);
        z-index: 2;
        background: white;
        padding: 8px;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        width: 320px;
    }

    input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    .suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        width: 100%;
        border-radius: 3px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        display: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 100;
    }

    .suggestions div {
        padding: 8px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }

    .suggestions div:hover {
        background: #f1f1f1;
    }

    .location-button {
        position: absolute;
        bottom: 63px;
        right: 70px;
        z-index: 2;
        background: white;
        padding: 10px;
        border-radius: 50%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .location-button:hover {
        background: #f1f1f1;
    }

    .map-theme-selector {
        position: absolute;
        top: 239px;
        left: 317px;
        z-index: 2;
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ddd;
        background: white;
    }

    .map-theme-selector select {
        padding: 4px;
        border-radius: 3px;
    }

    #pickupModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    /* Modal box */
.modal-content {
    background-color: #fff;
    padding: 30px;
    width: 100%;
    max-width: 500px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s ease-in-out;
    font-family: 'Segoe UI', sans-serif;
}
.modal-content h3 {
    margin-bottom: 20px;
    font-size: 1.6rem;
    color: #333;
    text-align: center;
}

/* Form styling */
.modal-content form label {
    display: block;
    margin-bottom: 5px;
    color: #555;
    font-weight: 500;
}

.modal-content form input[type="text"],
.modal-content form input[type="date"],
.modal-content form select,
.modal-content form textarea {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #f9f9f9;
    font-size: 14px;
    transition: border-color 0.3s;
}

.modal-content form input:focus,
.modal-content form select:focus,
.modal-content form textarea:focus {
    border-color: #4CAF50;
    outline: none;
    background-color: #fff;
}

.modal-content form textarea {
    height: 100px;
    resize: vertical;
}

.modal-content button {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    margin-right: 10px;
    background-color: #4CAF50;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.modal-content button:hover {
    background-color: #388e3c;
}

.modal-content button[type="button"] {
    background-color: #f44336;
}

.modal-content button[type="button"]:hover {
    background-color: #d32f2f;
}
.custom-alert {
    background-color: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
    padding: 15px 20px;
    margin: 3px 540px;
    width: 90%;
    z-index: 10000;
    max-width: 600px;
    border-radius: 8px;
    font-family: 'Segoe UI', sans-serif;
    font-size: 15px;
    position: fixed;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: flex
;
    align-items: center;
    gap: 10px;
    animation: slideIn 0.3s ease-out;
}

.alert-icon {
    font-size: 20px;
}

.close-alert {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 18px;
    cursor: pointer;
    color: #2e7d32;
    font-weight: bold;
}

.close-alert:hover {
    color: #c62828;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>



        
            <?php if (isset($_SESSION['message'])): ?>
    <div class="custom-alert">
        <span class="alert-icon">&#x1F4AC;</span> 
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        <span class="close-alert" onclick="this.parentElement.style.display='none';">&times;</span>
    </div>
<?php endif; ?>

                <div class="search-container">
                    <input type="text" id="search-box" placeholder="Search for a place...">
                    <div class="suggestions" id="suggestions"></div>
                </div>

                <div id="map"></div>

                <div class="map-theme-selector">
                    <select id="theme-select" onchange="changeMapTheme(this.value)">
                        <option value="retro">Default</option>
                        <option value="osm">OSM</option>
                        <option value="realistic">Hybrid</option>
                    </select>
                </div>

                <button class="location-button" onclick="getCurrentLocation()">
                    <span class="material-symbols-outlined">my_location</span>
                </button>

                <div id="pickupModal">
    <div class="modal-content">
        <h3>Pickup Request</h3>
        
     

        <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="customer_id" value="<?= $customer_id ?>">
        <input type="hidden" name="collector_id" id="collectorIdField">

            <label style="display:none">Name</label>
            <input type="text" name="name" style="opacity:0" value="<?= htmlspecialchars($fetch['first_name']) ?>">

            <label style="display:none">Address</label>
            <input type="text" name="address" style="opacity:0" value="<?= htmlspecialchars($fetch['address']) ?>">

            <label style="display:none">Contact Number</label>
            <input type="text" name="contact_number" style="opacity:0" value="<?= htmlspecialchars($fetch['number']) ?>">

          

            <label>Junk Type & Price per Kilo</label>
<select name="junk_data" required>
    <option value="">-- Select Junk Type --</option>
    <?php foreach ($junkPrices as $junk): ?>
        <option 
            value="<?= $junk['junk_type'] . ',' . $junk['collector_id'] . ',' . $junk['kl'] ?>">
            <?= $junk['junk_type'] ?> - ₱<?= $junk['garbage_price'] ?>/kg
        </option>
    <?php endforeach; ?>
</select>


            <label>Description</label>
            <textarea name="description" placeholder="Describe your junk..."></textarea>

            <label>Preferred Date</label>
            <input type="date" name="preferred_date" required>

            <br><br>
            <button type="submit" name="add_request">Submit Request</button>
            <button type="button" onclick="document.getElementById('pickupModal').style.display='none'">Close</button>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="infoModal" style="
    display: none; 
    position: fixed; 
    top: 50%; 
    left: 50%; 
    transform: translate(-50%, -50%);
    background: white; 
    padding: 24px; 
    border-radius: 10px; 
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); 
    text-align:start;
    width: 300px;
    font-family: Arial, sans-serif;
    z-index: 1000;
">
    <h3 style="margin-top: 0; color: #333;">Collector Info</h3>
    <p><strong>Name:</strong> <span id="infoName" style="color: #555;"></span></p>
    <p><strong>Address:</strong> <span id="infoAddress" style="color: #555;"></span></p>
    <div style="text-align: right; margin-top: 20px;">
        <button onclick="closeInfoModal()" style="
            background-color: #dc3545; 
            color: white; 
            border: none; 
            padding: 8px 14px; 
            border-radius: 4px; 
            cursor: pointer;
        ">Close</button>
    </div>
</div>


<div id="modalOverlay" style="
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
"></div>
<script>
function showInfoModal(name, address) {
    document.getElementById('infoName').textContent = name;
    document.getElementById('infoAddress').textContent = address;
    document.getElementById('infoModal').style.display = 'block';
}

function closeInfoModal() {
    document.getElementById('infoModal').style.display = 'none';
}
</script>




                <script>
                    const collectors = <?php echo json_encode($collectors); ?>;
                    

                    // let map = L.map('map', {
                    //     worldCopyJump: false,
                    //     maxBounds: [[-90, -180], [90, 180]],
                    //     maxBoundsViscosity: 1.0
                    // }).setView([10.3157, 123.8854], 13);

                    let map = L.map('map', {
                        worldCopyJump: false,
                        maxBounds: [[-90, -180], [90, 180]],
                        maxBoundsViscosity: 1.0
                    }).setView([10.0323, 124.1221], 13); 



                    let marker;

                    let tileLayers = {
                        retro: L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                            attribution: '&copy; Google Maps'
                        }),
                        osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }),
                        realistic: L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                            attribution: '&copy; Google Maps'
                        })
                    };

                    let currentLayer = tileLayers.retro;
                    currentLayer.addTo(map);

                    function changeMapTheme(theme) {
                        if (tileLayers[theme]) {
                            map.removeLayer(currentLayer);
                            currentLayer = tileLayers[theme];
                            currentLayer.addTo(map);
                        }
                    }

                    let searchBox = document.getElementById("search-box");
                    let suggestionsBox = document.getElementById("suggestions");

                    searchBox.addEventListener("input", function () {
                        let searchText = searchBox.value;
                        if (searchText.length < 2) {
                            suggestionsBox.style.display = "none";
                            return;
                        }

                        fetch(`https://photon.komoot.io/api/?q=${searchText}`)
                            .then(response => response.json())
                            .then(data => {
                                suggestionsBox.innerHTML = "";
                                data.features.forEach(feature => {
                                    let div = document.createElement("div");
                                    div.textContent = feature.properties.name;
                                    div.addEventListener("click", function () {
                                        searchBox.value = feature.properties.name;
                                        findAndMarkLocation(feature.properties.name);
                                        suggestionsBox.style.display = "none";
                                    });
                                    suggestionsBox.appendChild(div);
                                });
                                suggestionsBox.style.display = "block";
                            })
                            .catch(err => console.error(err));
                    });

                    document.addEventListener("click", function (e) {
                        if (!searchBox.contains(e.target) && !suggestionsBox.contains(e.target)) {
                            suggestionsBox.style.display = "none";
                        }
                    });

                    function getCurrentLocation() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(position => {
                                let lat = position.coords.latitude;
                                let lng = position.coords.longitude;
                                let latlng = [lat, lng];
                                if (marker) map.removeLayer(marker);
                                marker = L.marker(latlng).addTo(map).bindPopup("You are here").openPopup();
                                map.setView(latlng, 13);
                            }, () => {
                                alert("Geolocation permission denied or unavailable.");
                            });
                        } else {
                            alert("Geolocation is not supported by this browser.");
                        }
                    }

                    function findAndMarkLocation(placeName) {
                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(placeName)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.length > 0) {
                                    let lat = parseFloat(data[0].lat);
                                    let lon = parseFloat(data[0].lon);
                                    let latlng = [lat, lon];

                                    if (marker) map.removeLayer(marker);
                                    marker = L.marker(latlng).addTo(map).bindPopup(placeName).openPopup();
                                    map.setView(latlng, 13);
                                }
                            })
                            .catch(err => console.error(err));
                    }

                        // Function to open the modal and update collector info
    function openModal(id, name, address) {
        document.getElementById('pickupModal').style.display = 'flex';

        // Update collector info in the modal
        document.getElementById('collectorIdField').value = id;
        document.getElementById('collectorName').innerText = name;
        document.getElementById('collectorAddress').innerText = address;
    }

   // Iterate through the collectors and create markers for each
collectors.forEach(collector => {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(collector.address)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let lat = parseFloat(data[0].lat);
                let lon = parseFloat(data[0].lon);
                let marker = L.marker([lat, lon], { icon: redIcon }).addTo(map);

                // When clicking on the marker, open the modal and display the collector's name and address
                marker.bindPopup(`
    <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-start;">
        <strong>${collector.first_name}</strong>
        <span>${collector.address}</span>
        <div style="display: flex; gap: 10px;">
            <button 
                onclick="openModal(${collector.id}, '${collector.first_name}', '${collector.address}')"
                style="background-color: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                Request Pickup
            </button>
            <button 
                onclick="showInfoModal('${collector.first_name}', '${collector.address}')"
                style="background-color: #218838; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                Show Info
            </button>
        </div>
    </div>
`);

            }
        })
        .catch(err => console.error(err));
});

function showInfoModal(name, address) {
    const modal = document.getElementById("infoModal");
    document.getElementById("infoName").innerText = name;
    document.getElementById("infoAddress").innerText = address;
    modal.style.display = "block";
}

function closeInfoModal() {
    document.getElementById("infoModal").style.display = "none";
}


function showInfoModal(name, address) {
    document.getElementById("infoName").innerText = name;
    document.getElementById("infoAddress").innerText = address;
    document.getElementById("infoModal").style.display = "block";
    document.getElementById("modalOverlay").style.display = "block";
}

function closeInfoModal() {
    document.getElementById("infoModal").style.display = "none";
    document.getElementById("modalOverlay").style.display = "none";
}


                    
                    const redIcon = L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });

                   
                </script>
            </div>
        </div>
    </div>
</div>

