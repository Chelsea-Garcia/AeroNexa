<?php
// --- DIRECT FETCH FUNCTION (Same logic as Aureliya) ---
function getSkyRouteVehicles() {
    // 1. Try 127.0.0.1 (IPv4) - Assuming Port 8003 for SkyRoute
    // CHANGE '8003' if your SkyRoute server is running on a different port!
    $url = 'http://127.0.0.1:8003/api/skyroute/vehicles';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $output = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    // 2. Backup: Try localhost
    if ($err || empty($output)) {
        $url = 'http://localhost:8003/api/skyroute/vehicles';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }

    return json_decode($output, true);
}

// Fetch data
$vehicles = getSkyRouteVehicles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="skyroute.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>AERONEXA – SKYROUTE</title>
    
    <style>
        /* Modal Styles */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; justify-content: center; align-items: center; }
        .modal.hidden { display: none; }
        .modal-content { background: white; padding: 25px; border-radius: 10px; width: 90%; max-width: 400px; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .close-modal { position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9em; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .modal-actions { display: flex; gap: 10px; margin-top: 20px; }
        
        /* Button Colors to match SkyRoute Theme (Assuming Blue/Dark) */
        .confirm-btn { background: #04498D; color: white; border: none; padding: 10px; flex: 1; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .cancel-btn { background: #ddd; color: #333; border: none; padding: 10px; flex: 1; border-radius: 5px; cursor: pointer; }
        .confirm-btn:hover { background: #03366d; }
    </style>
</head>

<body>
<div id="home-section" class="home-section">
        <nav class="navbar">
            <div class="nav-logo">
                <img src="../ASSETS/aero.logo.png" alt="Logo" class="nav-logo-img" />
                <span class="nav-title">AERONEXA</span>
            </div>
            <ul class="nav-menu">
                <li><a href="../homepage.html" class="nav-link" onclick="setActive(this)">HOME</a></li>
                <li><a href="../PSA/psa.php" class="nav-link" onclick="setActive(this)">PHILLIPINE SKY AIRWAY</a></li>
                <li><a href="../AURELI-YAH!/aureliya.php" class="nav-link" onclick="setActive(this)">AURELI-YA!</a></li>
                <li><a href="../TRUTRAVEL/trutravel.php" class="nav-link" onclick="setActive(this)">TRUTRAVEL</a></li>
                <li><a href="skyroute.php" class="nav-link active" onclick="setActive(this)">SKYROUTE</a></li>
            </ul>
            <div class="nav-profile">
                <a href="../account.html">
                    <img src="../ASSETS/profile.png" alt="User Profile" class="profile-icon">
                </a>
            </div>
        </nav>

        <header class="hero-banner">
            <div class="hero-overlay"></div>

            <div class="search-card">
                <div class="search-row">
                    <div class="search-item">
                        <label>FROM</label>
                        <input type="text" placeholder="" />
                    </div>
                    <div class="search-item">
                        <label>TO</label>
                        <input type="text" placeholder="" />
                    </div>
                    <div class="search-item">
                        <label>DEPARTING DATE</label>
                        <input type="date" />
                    </div>
                    <div class="search-item">
                        <label>PASSENGERS</label>
                        <input type="number" min="1" value="1" />
                    </div>
                    <div class="search-action">
                        <button class="search-btn">Search</button>
                    </div>
                </div>
            </div>
        </header>

        <section class="vehicles-section">
            <div class="container">
                <h2 class="section-title">VEHICLES</h2>

                <div class="vehicles-grid" id="vehicleGrid">
                    <?php if (!empty($vehicles)): ?>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <?php 
                                // 1. Format Data
                                // Gamitin ang 'name' o 'model' kung ano man ang meron
                                $name = htmlspecialchars($vehicle['name'] ?? $vehicle['model'] ?? 'Unknown Vehicle');
                                $type = htmlspecialchars($vehicle['type']);  // Bus, Car, SUV
                                $price = isset($vehicle['price_per_day']) ? number_format($vehicle['price_per_day'], 0) : 'TBD';
                                $capacity = isset($vehicle['capacity']) ? $vehicle['capacity'] : '4';

                                // 2. Handle Image (Fallback logic)
                                // Adjust fallback images based on your actual assets
                                $fallbackImg = 'CAR.png'; 
                                if (stripos($type, 'bus') !== false) $fallbackImg = 'BUS.png';
                                elseif (stripos($type, 'suv') !== false) $fallbackImg = 'SUV.png';

                                $imageSrc = !empty($vehicle['image']) ? $vehicle['image'] : $fallbackImg;
                                
                                // 3. Safe Strings for JS
                                $safeName = addslashes($name);
                            ?>

                            <div class="vehicle-card">
                                <img src="<?= $imageSrc ?>" alt="<?= $name ?>" class="vehicle-img" onerror="this.src='<?= $fallbackImg ?>'" />
                                <div class="vehicle-title"><?= $name ?></div>
                                <div style="font-size: 0.9em; color: #666; margin-bottom: 10px;">
                                    ₱<?= $price ?> / day • <?= $capacity ?> Seats
                                </div>
                                <button class="book-now" onclick="openBookingModal('<?= $safeName ?>')">Book Now</button>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 20px;">
                            <h3>No vehicles found.</h3>
                            <p>Please check if SkyRoute Server (Port 8003?) is running and Seeded.</p>
                            <code>php artisan db:seed --class=Database\Seeders\skyroute\VehicleSeeder</code>
                        </div>
                    <?php endif; ?>
                    </div>
            </div>
        </section>

    <footer class="main-footer">
        <div class="footer-bottom">
            <p>&copy; 2025 AERONEXA. All rights reserved.</p>
        </div>
    </footer>

    <div id="booking-modal" class="modal hidden">
        <div class="modal-content">
            <span class="close-modal" onclick="closeBookingModal()">&times;</span>
            <h2>Rent Vehicle</h2>
            <p id="modal-vehicle-name" style="color: #666; margin-bottom: 20px;">Vehicle Name</p>

            <form onsubmit="event.preventDefault(); openSuccessModal();">
                <div class="form-group"><label>Full Name</label><input type="text" required /></div>
                <div class="form-group"><label>Pick-up Date</label><input type="date" required /></div>
                <div class="form-group"><label>Return Date</label><input type="date" required /></div>
                <div class="modal-actions">
                    <button type="button" class="cancel-btn" onclick="closeBookingModal()">Cancel</button>
                    <button type="submit" class="confirm-btn">Confirm Rental</button>
                </div>
            </form>
        </div>
    </div>

    <div id="success-modal" class="modal hidden">
        <div class="modal-content" style="text-align: center;">
            <span class="close-modal" onclick="closeSuccessModal()">&times;</span>
            <div style="font-size: 50px; color: green; margin-bottom: 10px;">&#10004;</div>
            <h2>Booking Confirmed!</h2>
            <p>Your vehicle is reserved.</p>
            <p>Ref: <strong id="booking-ref-number">SKY-000</strong></p>
            <button class="confirm-btn" onclick="closeSuccessModal()" style="margin-top: 20px;">OK</button>
        </div>
    </div>

    <script>
        // Navigation Active State
        function setActive(element) {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            element.classList.add('active');
        }

        // Highlight Active Link on Load
        document.addEventListener('DOMContentLoaded', () => {
            const path = location.pathname.split('/').pop() || 'skyroute.php';
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href').indexOf(path) !== -1) {
                    link.classList.add('active');
                }
            });
        });

        // Modals Logic
        function openBookingModal(vehicleName) {
            document.getElementById('booking-modal').classList.remove('hidden');
            document.getElementById('modal-vehicle-name').innerText = vehicleName;
        }

        function closeBookingModal() {
            document.getElementById('booking-modal').classList.add('hidden');
        }

        function openSuccessModal() {
            closeBookingModal();
            document.getElementById('booking-ref-number').innerText = "SKY-" + Math.floor(Math.random() * 100000);
            document.getElementById('success-modal').classList.remove('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('success-modal').classList.add('hidden');
        }

        // Close on outside click
        window.onclick = function(event) {
            const bModal = document.getElementById('booking-modal');
            const sModal = document.getElementById('success-modal');
            if (event.target == bModal) closeBookingModal();
            if (event.target == sModal) closeSuccessModal();
        }
    </script>
</body>
</html>