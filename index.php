<?php @include("header.php")?>
<?php @include("navbar.php")?>
<?php @include("./db/database.php");

// Fetch customers from DB
$customers = [];
$query = "SELECT first_name, address FROM users WHERE user_type = 'customer'";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
}
?>

<div class="az-content az-content-dashboard">
    <div class="container">
        <div class="az-content-body">
            <div class="az-dashboard-one-title">
                <div class="search-container">
                    <input type="text" id="search-box" placeholder="Search for a place...">
                    <div class="suggestions" id="suggestions"></div>
                </div>
                <div id="map"></div>

                <div class="map-theme-selector" style="position: absolute;
                        top: 173px;
                        left: 203px;
                        z-index: 5000;
                        padding: 5px;
                        border-radius: 5px;
                        border: 1px solid #ddd;">
                    <select id="theme-select" style="padding: 4px; border-radius: 3px;" onchange="changeMapTheme(this.value)">
                        <option value="retro">Default</option>
                        <option value="osm">OSM</option>
                        <option value="realistic">Hybrid</option>
                    </select>
                </div>

                <button class="location-button" onclick="getCurrentLocation()">
                    <span class="material-symbols-outlined">my_location</span>
                </button>

                <script>
                    // Get customer data from PHP
                    const customers = <?php echo json_encode($customers); ?>;

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

                    // Tile Layers for Themes
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
                                    marker = L.marker(latlng).addTo(map)
                                        .bindPopup(`<b>${placeName}</b>`)
                                        .openPopup();
                                    map.setView(latlng, 15);
                                } else {
                                    alert("Location not found!");
                                }
                            })
                            .catch(err => console.error(err));
                    }

                    // Display all customer markers (green)
                    document.addEventListener("DOMContentLoaded", function () {
                        let storedPlace = localStorage.getItem("mapPlace");
                        if (storedPlace) {
                            findAndMarkLocation(storedPlace);
                            localStorage.removeItem("mapPlace");
                        }

                        customers.forEach(customer => {
                            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(customer.address)}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data && data.length > 0) {
                                        let lat = parseFloat(data[0].lat);
                                        let lon = parseFloat(data[0].lon);
                                        let latlng = [lat, lon];

                                        const greenIcon = L.icon({
                                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                            iconSize: [25, 41],
                                            iconAnchor: [12, 41],
                                            popupAnchor: [1, -34],
                                            shadowSize: [41, 41]
                                        });

                                        L.marker(latlng, { icon: greenIcon })
                                            .addTo(map)
                                            .bindPopup(`<b>${customer.first_name}</b><br>${customer.address}`);
                                    }
                                })
                                .catch(err => console.error("Geocoding error:", err));
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<?php @include("footer.php")?>
