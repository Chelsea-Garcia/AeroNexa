<?php
require_once '../api_helper.php'; // I-include ang helper
$flights = fetchData('http://localhost:8000/api/psa/flights');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="psa.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>AERONEXA - Book Flight</title>
</head>

<body>

<nav class="navbar">
    <div class="nav-logo">
        <img src="aero.logo.png" alt="Logo" class="nav-logo-img" />
        <span class="nav-title">AERONEXA</span>
    </div>
    <ul class="nav-menu">
        <li><a href="homepage.html" class="nav-link" onclick="setActive(this)">HOME</a></li>
        <li><a href="psa.php" class="nav-link active" onclick="setActive(this)">PHILLIPINE SKY AIRWAY</a></li>
        <li><a href="aureliya.php" class="nav-link" onclick="setActive(this)">AURELI-YA!</a></li>
        <li><a href="trutravel.php" class="nav-link" onclick="setActive(this)">TRUTRAVEL</a></li>
        <li><a href="skyroute.php" class="nav-link" onclick="setActive(this)">SKYROUTE</a></li>
    </ul>
    <div class="nav-profile">
        <a href="account.html">
            <img src="profile.png" alt="User Profile" class="profile-icon">
        </a>
    </div>
</nav>

<div class="page-wrap">

    <div class="filter-bar" role="region" aria-label="flight search filters">
        <div class="filter-item">
            <label for="trip-type">Type</label>
            <div class="select-wrapper">
                <select id="trip-type">
                    <option value="oneway">One way</option>
                    <option value="round">Round trip</option>
                </select>
            </div>
        </div>

        <div class="filter-item">
            <label for="from">From</label>
            <div class="select-wrapper">
                <select id="from">
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
            <label for="to">To</label>
            <div class="select-wrapper">
                <select id="to">
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
            <label for="cabin">Class</label>
            <div class="select-wrapper">
                <select id="cabin">
                    <option>Economy</option>
                    <option>Premium Economy</option>
                    <option>Business</option>
                </select>
            </div>
        </div>

        <div class="filter-item">
            <label for="departure">Departure</label>
            <input id="departure" type="date" />
        </div>

        <div class="filter-action">
            <button id="search-btn" class="search-btn">Search</button>
        </div>
    </div>

    <div class="booking-container" id="booking-container">

        <div class="packages-panel">
            <h2 class="panel-title">TRAVEL PACKAGES</h2>
            <div class="package-card">
                <h4>Palawan Getaway</h4>
                <p class="package-meta">5 days, 4 nights. Incl. flight & hotel.</p>
                <div class="package-price">₱18,000</div>
            </div>
            <div class="package-card">
                <h4>Bohol Adventure</h4>
                <p class="package-meta">3 days, 2 nights. Incl. ferry & tour.</p>
                <div class="package-price">₱12,500</div>
            </div>
        </div>

        <div class="left-panel">
            <h2 class="panel-title">FLIGHTS</h2>
            <div id="flight-list">
                <?php if (!empty($flights)): ?>
                    <?php foreach ($flights as $flight): ?>
                        <?php 
                            // Format Data
                            $route = htmlspecialchars($flight['origin']) . ' to ' . htmlspecialchars($flight['destination']);
                            $depTime = date('h:i A', strtotime($flight['departure_time']));
                            $arrTime = date('h:i A', strtotime($flight['arrival_time']));
                            $timeString = "$depTime - $arrTime";
                            $flightNum = htmlspecialchars($flight['flight_number']);
                            $price = number_format($flight['price'], 0);
                        ?>
                        
                        <div class="flight-card" 
                             onclick="selectFlight(this, '<?= $flightNum ?>')" 
                             data-price="<?= $price ?>" 
                             data-route="<?= $route ?>">
                             
                            <div class="flight-info">
                                <h3 class="flight-number">Flight <?= $flightNum ?></h3>
                                <p class="flight-route"><?= $route ?></p>
                                <p class="flight-time"><?= $timeString ?></p>
                            </div>
                            <div class="flight-price">
                                ₱<?= $price ?>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 20px; text-align: center; color: #666;">
                        <h3>No flights available</h3>
                        <p>Check if Server is running (Port 8000) and Database has seeds.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="right-panel hidden" id="right-panel">
            <button class="close-btn" id="close-details-panel" aria-label="Close Details Panel">&times;</button>
            
            <div class="tab-header">
                <div id="tab-details" class="tab-active" onclick="switchTab('details')">DETAILS</div>
                <div id="tab-fares" onclick="switchTab('fares')">FARES</div>
            </div>

            <div id="details-content" class="tab-content">
                <h3 class="muted">Select a flight to view details</h3>
            </div>

            <div id="fares-content" class="tab-content hidden">
                <h4 class="muted">No fare selected</h4>
            </div>
        </div>

    </div>
</div>

<footer class="main-footer">
    <div class="footer-bottom">
        <p>&copy; 2025 AERONEXA. All rights reserved.</p>
    </div>
</footer>

<script>
    // --- Utility Functions ---

    function setActive(element) {
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        element.classList.add('active');
    }

    // --- Main Logic: Select Flight ---
    function selectFlight(card, flightNum) {
        const rightPanel = document.getElementById('right-panel');
        const bookingContainer = document.getElementById('booking-container'); 
        
        // 1. Show the Right Panel
        if (rightPanel && bookingContainer) {
            rightPanel.classList.remove('hidden');
            bookingContainer.classList.add('three-column'); 
        }

        // 2. Get Data from the Clicked Card
        const route = card.getAttribute('data-route'); 
        const price = card.getAttribute('data-price');
        
        // Get time from inside the card
        let time = 'N/A';
        const timeElement = card.querySelector('.flight-time');
        if (timeElement) {
            time = timeElement.innerText;
        }
        
        // 3. Update the Details Panel Content
        document.getElementById('details-content').innerHTML = `
            <h2>${route}</h2>
            <p><strong>Flight No:</strong> Flight ${flightNum}</p>
            <p><strong>Time:</strong> ${time}</p>
            <p><strong>Price:</strong> ₱${price}</p>
            
            <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px;">
                <p><strong>Aircraft:</strong> Airbus A320</p>
                <p style="font-size: 0.9em; color: #555; margin-top:5px;">Includes 7kg Hand Carry luggage + 20kg Check-in.</p>
            </div>
            
            <button class="book-btn" style="margin-top: 20px;" onclick="alert('Booking for Flight ${flightNum}...')">Proceed to Book</button>
        `;

        switchTab('details');
    }

    function closeDetailsPanel() {
        const rightPanel = document.getElementById('right-panel');
        const bookingContainer = document.getElementById('booking-container'); 

        if (rightPanel && bookingContainer) {
            rightPanel.classList.add('hidden');
            bookingContainer.classList.remove('three-column');
        }
    }

    function switchTab(tab) {
        document.getElementById("tab-details").classList.remove("tab-active");
        document.getElementById("tab-fares").classList.remove("tab-active");
        document.getElementById("details-content").classList.add("hidden");
        document.getElementById("fares-content").classList.add("hidden");

        if (tab === "details") {
            document.getElementById("tab-details").classList.add("tab-active");
            document.getElementById("details-content").classList.remove("hidden");
        } else {
            document.getElementById("tab-fares").classList.add("tab-active");
            document.getElementById("fares-content").classList.remove("hidden");
        }
    }

    // --- Search Logic ---
    document.addEventListener('DOMContentLoaded', () => {
        // Handle Active Link
        const path = location.pathname.split('/').pop() || 'psa.php';
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === path) link.classList.add('active');
        });

        // Close Button
        const closeBtn = document.getElementById('close-details-panel');
        if(closeBtn) {
            closeBtn.addEventListener('click', closeDetailsPanel);
        }

        // Search Button Logic
        const searchBtn = document.getElementById('search-btn');
        if(searchBtn) {
            searchBtn.addEventListener('click', () => {
                const from = document.getElementById('from').value.trim();
                const to = document.getElementById('to').value.trim();
                const rightPanel = document.getElementById('right-panel');
                
                const cards = Array.from(document.querySelectorAll('.flight-card'));
                let anyVisible = false;

                cards.forEach(card => {
                    const route = card.getAttribute('data-route') || card.innerText;
                    const matchesFrom = !from || route.includes(from);
                    const matchesTo = !to || route.includes(to);
                    
                    if (matchesFrom && matchesTo) {
                        card.style.display = 'flex';
                        anyVisible = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (rightPanel) {
                    closeDetailsPanel(); 
                    if (!anyVisible) {
                        // Optional: Show "No results" message elsewhere
                        alert("No flights found matching your criteria.");
                    }
                }
            });
        }
    });
</script>
</body>
</html>