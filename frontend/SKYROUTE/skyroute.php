<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="skyroute.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>AERONEXA - SKYROUTE</title>
    <style>
        /* MODAL STYLES */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); z-index: 9999;
            display: flex; justify-content: center; align-items: center;
        }
        .hidden { display: none !important; }
        .modal-content {
            background: white; padding: 25px; border-radius: 12px; width: 95%; max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;
            max-height: 90vh; overflow-y: auto;
        }
        .close-modal { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #888; }
        
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; color: #444; }
        .form-group input, .form-group select {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-family: 'Poppins', sans-serif;
        }
        .form-row { display: flex; gap: 10px; }
        .section-header { font-size: 14px; font-weight: 700; color: #04498D; margin-top: 15px; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        
        .confirm-btn {
            background: #04498D; color: white; border: none; padding: 12px; width: 100%;
            border-radius: 5px; font-weight: bold; cursor: pointer; margin-top: 15px; transition: 0.3s;
        }
        .confirm-btn:hover { background: #033a70; }
        .confirm-btn:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-logo">
            <img src="/frontend/ASSETS/aero.logo.png" alt="Logo" class="nav-logo-img" />
            <span class="nav-title">AERONEXA</span>
        </div>
        <ul class="nav-menu">
            <li><a href="/AeroNexa/homepage.html" class="nav-link">HOME</a></li>
            <li><a href="/AeroNexa/frontend/PSA/psa.php" class="nav-link">PHILIPPINE SKY AIRWAY</a></li>
            <li><a href="/AeroNexa/frontend/AURELI-YAH!/aureliya.php" class="nav-link">AURELI-YA!</a></li>
            <li><a href="/AeroNexa/frontend/TRUTRAVEL/trutravel.php" class="nav-link">TRUTRAVEL</a></li>
            <li><a href="skyroute.php" class="nav-link active">SKYROUTE</a></li>
        </ul>
        <div class="nav-profile"><a href="/frontend/account.html"><img src="/frontend/ASSETS/profile.png" class="profile-icon"></a></div>
    </nav>

    <header class="hero-banner">
        <div class="hero-overlay"></div>
        <div class="search-card">
            <div class="search-row" style="justify-content: center; height: 100%; align-items: center;">
                <h2 style="color: #333;">Choose Your Ride</h2>
            </div>
        </div>
    </header>

    <section class="vehicles-section">
        <div class="container">
            <h2 class="section-title">VEHICLES</h2>
            <div class="vehicles-grid">
                
                <div class="vehicle-card">
                    <img src="assets/BUS.png" alt="Bus" class="vehicle-img" />
                    <div class="vehicle-title">BUS</div>
                    <p style="font-size:12px; color:#666;">Fixed: 56 Seats</p>
                    <button class="book-now" onclick="openModal('Bus', 56)">Book Now</button>
                </div>

                <div class="vehicle-card">
                    <img src="assets/CAR.png" alt="Car" class="vehicle-img" />
                    <div class="vehicle-title">CAR</div>
                    <p style="font-size:12px; color:#666;">Fixed: 5 Seats</p>
                    <button class="book-now" onclick="openModal('Car', 5)">Book Now</button>
                </div>

                <div class="vehicle-card">
                    <img src="assets/SUV.png" alt="SUV" class="vehicle-img" />
                    <div class="vehicle-title">SUV</div>
                    <p style="font-size:12px; color:#666;">Fixed: 7 Seats</p>
                    <button class="book-now" onclick="openModal('SUV', 7)">Book Now</button>
                </div>

            </div>
        </div>
    </section>

    <footer class="main-footer">
        <div class="footer-bottom"><p>&copy; 2025 AERONEXA. All rights reserved.</p></div>
    </footer>

    <div id="booking-modal" class="modal-overlay hidden">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 style="color:#04498D;">Book a <span id="modal-type">Vehicle</span></h2>
            
            <input type="hidden" id="v-type">
            <input type="hidden" id="v-capacity"> <div class="section-header">ORIGIN DETAILS</div>
            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label>City</label>
                    <input type="text" id="origin-city" placeholder="e.g. Makati">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Division/Province</label>
                    <input type="text" id="origin-div" placeholder="e.g. Metro Manila">
                </div>
            </div>
            <div class="form-group">
                <label>Country</label>
                <input type="text" id="origin-country" value="Philippines">
            </div>

            <div class="section-header">DESTINATION DETAILS</div>
            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label>City</label>
                    <input type="text" id="dest-city" placeholder="e.g. Baguio">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Division/Province</label>
                    <input type="text" id="dest-div" placeholder="e.g. Benguet">
                </div>
            </div>
            <div class="form-group">
                <label>Country</label>
                <input type="text" id="dest-country" value="Philippines">
            </div>

            <div class="section-header">SCHEDULE & PAX</div>
            <div class="form-row">
                <div class="form-group" style="flex:1;"><label>Date</label><input type="date" id="date"></div>
                <div class="form-group" style="flex:1;"><label>Time</label><input type="time" id="time"></div>
            </div>

            <div class="form-group">
                <label>Passengers (Max: <span id="p-max"></span>)</label>
                <input type="number" id="passengers" min="1" value="1">
            </div>

            <button class="confirm-btn" onclick="submitBooking()">Confirm Booking</button>
        </div>
    </div>

    <script>
        function openModal(type, capacity) {
            document.getElementById('booking-modal').classList.remove('hidden');
            document.getElementById('modal-type').innerText = type;
            document.getElementById('v-type').value = type;
            document.getElementById('v-capacity').value = capacity;
            document.getElementById('p-max').innerText = capacity;
            document.getElementById('passengers').setAttribute('max', capacity);
            document.getElementById('passengers').value = 1;
        }

        function closeModal() {
            document.getElementById('booking-modal').classList.add('hidden');
        }

        async function submitBooking() {
            const btn = document.querySelector('.confirm-btn');
            const type = document.getElementById('v-type').value;
            const maxCap = parseInt(document.getElementById('v-capacity').value);
            const pax = parseInt(document.getElementById('passengers').value);
            
            // VALIDATION
            if (pax < 1 || pax > maxCap) {
                alert(`Invalid passengers. Max capacity for ${type} is ${maxCap}.`);
                return;
            }

            const payload = {
                user_id: "1", 
                vehicle_type: type,
                
                // Manual Inputs
                origin: {
                    city: document.getElementById('origin-city').value,
                    division: document.getElementById('origin-div').value,
                    country: document.getElementById('origin-country').value
                },
                destination: {
                    city: document.getElementById('dest-city').value,
                    division: document.getElementById('dest-div').value,
                    country: document.getElementById('dest-country').value
                },
                
                date: document.getElementById('date').value,
                time: document.getElementById('time').value,
                passengers: pax
            };

            if(!payload.origin.city || !payload.destination.city || !payload.date || !payload.time) {
                alert("Please complete all details."); return;
            }

            btn.innerText = "Processing..."; btn.disabled = true;

            try {
                const res = await fetch('http://localhost:8000/api/skyroute/bookings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (res.ok) {
                    alert("✅ BOOKING SUCCESSFUL!\nAssigned Vehicle: " + data.vehicle_assigned + "\nPlate: " + data.plate_number);
                    closeModal();
                } else {
                    alert("❌ " + (data.message || "Booking Failed"));
                }
            } catch (e) {
                alert("Error: " + e.message);
            } finally {
                btn.innerText = "Confirm Booking"; btn.disabled = false;
            }
        }
    </script>
</body>
</html>