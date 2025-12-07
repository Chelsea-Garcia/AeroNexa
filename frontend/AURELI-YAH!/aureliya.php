<?php
// --- ROBUST FETCHER (Pumipilit kumuha ng data) ---
function getAureliyaProperties() {
    // 1. Subukan muna ang 127.0.0.1 (IPv4)
    $url = 'http://127.0.0.1:8002/api/aureliya/properties';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // Force IPv4
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $output = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    // 2. Kung ayaw, subukan ang localhost
    if ($err || empty($output)) {
        $url = 'http://localhost:8002/api/aureliya/properties';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }

    return json_decode($output, true);
}

// Tawagin ang function
$properties = getAureliyaProperties();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="aureliya.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>AERONEXA – AURELI-YA!</title>
</head>

<body>

    <nav class="navbar">
        <div class="nav-logo">
            <img src="aero.logo.png" alt="Logo" class="nav-logo-img" />
            <span class="nav-title">AERONEXA</span>
        </div>
        <ul class="nav-menu">
            <li><a href="/AeroNexa/homepage.html" class="nav-link" onclick="setActive(this)">HOME</a></li>
            <li><a href="/AeroNexa/frontend/PSA/psa.php" class="nav-link" onclick="setActive(this)">PHILIPPINE SKY AIRWAY</a></li>
            <li><a href="/AeroNexa/frontend/AURELI-YAH!/aureliya.php" class="nav-link" onclick="setActive(this)">AURELI-YA!</a></li>
            <li><a href="/AeroNexa/frontend/TRUTRAVEL/trutravel.php" class="nav-link" onclick="setActive(this)">TRUTRAVEL</a></li>
            <li><a href="/AeroNexa/frontend/SKYROUTE/skyroute.php" class="nav-link" onclick="setActive(this)">SKYROUTE</a></li>
        </ul>
        <div class="nav-profile">
            <a href="account.html">
                <img src="profile.png" alt="User Profile" class="profile-icon">
            </a>
        </div>
    </nav>
    
<div class="page-wrap">

    <div class="hotel-navbar">
        <div class="filter-bar" role="region" aria-label="flight search filters">
        <div class="filter-fields-container">
            <div class="filter-item">
                <label for="where">Where</label>
                <div class="select-wrapper">
                    <select id="where">
                        <option value="">Any</option>
                        <option>Manila</option>
                        <option>Clark</option>
                        <option>Cebu</option>
                        <option>Davao</option>
                        <option>Iloilo</option>
                    </select>
                </div>
            </div>

            <div class="filter-item">
                <label for="when">When</label>
                <div class="select-wrapper date-wrapper">
                    <input id="when" type="date"/>
                </div>
            </div>

            <div class="filter-item">
                <label for="guests">Guests</label>
                <div class="select-wrapper">
                    <select id="guests">
                        <option>Adult</option>
                        <option>Child</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="filter-action">
            <button id="search-btn" class="search-btn">Search</button>
        </div>
    </div>
</div>

    <div class="spacer-30"></div>

    <div class="content-wrapper" id="contentWrapper">
        <div class="airbnb-grid" id="airbnbGrid">
    <?php if (!empty($properties)): ?>
        <?php foreach ($properties as $property): ?>
            <?php 
                // 1. Setup Data (Gaya ng ginawa sa PSA, pero Aureliya fields)
                $title = htmlspecialchars($property['title']);
                $desc = addslashes(htmlspecialchars($property['description'])); 
                $price = number_format($property['price_per_night'], 0);
                $location = htmlspecialchars($property['city']) . ', ' . htmlspecialchars($property['country']);
                
                // Handle Images
                $photos = isset($property['photos']) && is_string($property['photos']) ? json_decode($property['photos'], true) : ($property['photos'] ?? []);
                $imageSrc = !empty($photos) ? $photos[0] : 'assets/airbnb1.jpg';
                
                // Escape para sa JavaScript
                $safeTitle = addslashes($title);
                $safeLoc = addslashes($location);
            ?>

            <div class="airbnb-card" 
                 onclick="showDetails('<?= $safeTitle ?>', '<?= $desc ?>', '<?= $price ?>', '<?= $safeLoc ?>', '<?= $imageSrc ?>', '<?= $property['type'] ?>', '<?= $property['max_guests'] ?>')">
                
                <img src="<?= $imageSrc ?>" onerror="this.src='assets/airbnb1.jpg'">
                <div class="card-info">
                    <p class="card-title"><?= $title ?></p>
                    <p class="card-loc"><?= $location ?></p>
                    <small>₱<?= $price ?> / night</small>
                </div>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <p style="padding: 20px;">No properties found. (Check Database & Server 8002)</p>
    <?php endif; ?>
</div>

        <div class="details-panel" id="detailsPanel">
            <h2>Select an Airbnb</h2>
            <p>Click one of the listings to view more details.</p>
        </div>
    </div>
</div>

<footer class="main-footer">
    <div class="footer-bottom">
        <p>&copy; 2025 AERONEXA. All rights reserved.</p>
    </div>
</footer>

<script src="C:\xampp\htdocs\AeroNexa\backendjs\aureliya.js"></script>
<script>

    const details = [
        { title: "Cozy Beachfront Home", desc: "A relaxing beachfront stay.", imgSrc: "airbnb1.jpg" },
        { title: "Mountain View Cabin", desc: "A peaceful cabin with cold breeze.", imgSrc: "airbnb2.jpg" },
        { title: "Luxury City Condo", desc: "In the heart of the city.", imgSrc: "airbnb3.jpg" },
        { title: "Private Island Retreat", desc: "Exclusive island experience.", imgSrc: "airbnb4.jpg" },
        { title: "Modern Glass Villa", desc: "Panoramic glass walls + pool.", imgSrc: "airbnb5.jpg" },
        { title: "Forest Treehouse", desc: "Stay above the forest canopy.", imgSrc: "airbnb6.jpg" }
    ];

    // Tanggalin ang 'index' at tanggapin ang actual values
function showDetails(title, desc, price, location, imgSrc, type, guests) {
    const panel = document.getElementById("detailsPanel");
    
    panel.innerHTML = `
        <img src="${imgSrc}" class="detail-image" onerror="this.src='assets/airbnb1.jpg'" />
        <div class="detail-content">
            <span style="background:#FF5A5F; color:white; padding:4px 8px; border-radius:4px; font-size:0.8em; font-weight:bold;">${type.toUpperCase()}</span>
            <h2>${title}</h2>
            <p>📍 ${location}</p>
            <hr style="margin:15px 0; border:0; border-top:1px solid #eee;">
            <p>${desc}</p>
            <div style="margin-top:10px; font-size:0.9em; color:#555;">
                <p>👥 Capacity: Up to <strong>${guests}</strong> Guests</p>
                <p>💰 Price: <strong>₱${price}</strong> / night</p>
            </div>
            <button class="book-btn" onclick="alert('Booking feature coming soon!')">Book Now</button>
        </div>
    `;
}
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.airbnb-card');
        cards.forEach(card => {
            card.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                showDetails(index);
            });
        });
    });
</script>
</body>
</html>
