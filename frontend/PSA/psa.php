<?php
// --- DIRECT FETCH FUNCTION ---
function getPSAFlights() {
    $url = 'http://127.0.0.1:8000/api/psa/flights';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $output = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err || empty($output)) {
        $url = 'http://localhost:8000/api/psa/flights';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }

    return json_decode($output, true);
}

$flights = getPSAFlights();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="psa.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>AERONEXA - Book Flight</title>

    <style>
        /* --- FIX: CENTERED MODAL & Z-INDEX --- */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999; /* Higher than navbar */
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(2px);
        }

        .hidden { display: none !important; }

        .modal-content {
            background: white; padding: 30px; border-radius: 12px;
            width: 90%; max-width: 550px; max-height: 90vh; overflow-y: auto;
            position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            pointer-events: auto;
        }

        .close-modal {
            position: absolute; top: 15px; right: 20px; font-size: 28px;
            cursor: pointer; color: #888; z-index: 10001;
        }
        .close-modal:hover { color: #d4070f; }

        /* INPUT FIXES */
        .passenger-form input, .passenger-form select {
            border: 1px solid #ccc; border-radius: 4px; padding: 12px;
            font-family: 'Poppins', sans-serif; font-size: 13px; width: 100%;
            box-sizing: border-box; background: #fff; color: #333;
        }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; }
        .section-header {
            margin: 20px 0 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;
            color: #037FBE; font-size: 13px; font-weight: 700; text-transform: uppercase;
        }
        
        /* RESTORED LAYOUT HELPERS */
        .booking-container {
            display: flex;
            gap: 20px;
            position: relative;
        }
        .booking-container.three-column .left-panel {
            /* Adjust width when right panel is open */
            flex: 2; 
        }
        .right-panel {
            width: 350px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: -5px 0 15px rgba(0,0,0,0.05);
            height: fit-content;
        }
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
        <li><a href="/AeroNexa/frontend/PSA/psa.php" class="nav-link active">PHILIPPINE SKY AIRWAY</a></li>
        <li><a href="/AeroNexa/frontend/AURELI-YAH!/aureliya.php" class="nav-link">AURELI-YA!</a></li>
        <li><a href="/AeroNexa/frontend/TRUTRAVEL/trutravel.php" class="nav-link">TRUTRAVEL</a></li>
        <li><a href="/AeroNexa/frontend/SKYROUTE/skyroute.php" class="nav-link">SKYROUTE</a></li>
    </ul>
    <div class="nav-profile">
        <a href="/frontend/account.html"><img src="/frontend/ASSETS/profile.png" class="profile-icon"></a>
    </div>
</nav>

<div class="page-wrap">
    <div class="filter-bar">
        <div class="filter-item"><label>From</label><select id="from"><option>Manila</option><option>Cebu</option></select></div>
        <div class="filter-item"><label>To</label><select id="to"><option>Cebu</option><option>Davao</option></select></div>
        <div class="filter-item"><label>Date</label><input id="departure" type="date" /></div>
        <div class="filter-action"><button id="search-btn" class="search-btn">Search</button></div>
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
            <h2 class="panel-title">AVAILABLE FLIGHTS</h2>
            <?php if (!empty($flights)): ?>
                <?php foreach ($flights as $flight): ?>
                    <?php 
                        $flightId = $flight['id']; 
                        $origin = htmlspecialchars($flight['origin'] ?? $flight['route']['origin']['city'] ?? 'Origin');
                        $destination = htmlspecialchars($flight['destination'] ?? $flight['route']['destination']['city'] ?? 'Dest');
                        $depTime = substr($flight['departure_time'] ?? '00:00', 0, 5);
                        $arrTime = substr($flight['arrival_time'] ?? '00:00', 0, 5);
                        $price = number_format($flight['price'] ?? 0, 0, '.', '');
                        $flightCode = htmlspecialchars($flight['flight_number'] ?? $flight['code'] ?? 'FL-XXX');
                        $routeStr = "$origin → $destination";
                        $timeStr = "$depTime — $arrTime";
                    ?>
                    <div class="flight-card" onclick="showRightPanel('<?= $flightId ?>', '<?= $routeStr ?>', '<?= $depTime ?>', '<?= $arrTime ?>', '<?= $price ?>', '<?= $flightCode ?>')">
                        <div class="flight-info">
                            <h3><?= $routeStr ?></h3>
                            <p class="time"><?= $timeStr ?></p>
                            <p class="meta">Economy • <?= $flightCode ?></p>
                        </div>
                        <div class="flight-actions">
                            <div class="fare">₱<?= number_format($price) ?></div>
                            <button class="book-btn">Select</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding: 20px; text-align: center;"><h3>No flights available.</h3><p>Check PSA Server (Port 8000).</p></div>
            <?php endif; ?>
        </div>

        <div class="right-panel hidden" id="right-panel">
            <button class="close-btn" id="close-details-panel" onclick="closeRightPanel()" style="position:relative; top:0; right:0; float:right;">&times;</button>
            <div id="details-content" class="tab-content" style="margin-top:20px;">
                <h3 class="muted">Select a flight</h3>
            </div>
        </div>
    </div>
</div>

<div id="booking-modal" class="modal-overlay hidden">
    <div class="modal-content">
        <span class="close-modal" onclick="closeBookingModal()">&times;</span>
        <h2 style="margin:0; color:#037FBE;">Flight Itinerary</h2>
        <p style="margin:5px 0 15px; color:#666; font-size:14px;">Complete your travel details.</p>
        
        <input type="hidden" id="selected-flight-id" value="">
        <div id="modal-flight-summary" style="background:#f4f9fd; padding:10px; border-radius:5px; margin-bottom:15px; font-weight:bold; color:#555;"></div>

        <div class="passenger-form">
            <div class="section-header">Personal Details</div>
            <div class="form-row"><input type="text" id="p-fname" placeholder="First Name"><input type="text" id="p-lname" placeholder="Last Name"></div>
            <div class="form-row"><input type="email" id="p-email" placeholder="Email Address"><input type="text" id="p-contact" placeholder="Mobile No."></div>
            <div class="form-row">
                <select id="p-gender"><option value="" disabled selected>Gender</option><option value="Male">Male</option><option value="Female">Female</option></select>
                <select id="p-civil"><option value="" disabled selected>Status</option><option value="Single">Single</option><option value="Married">Married</option></select>
            </div>
            <div class="form-row">
                <div style="flex:1"><label style="font-size:11px;">Birthdate</label><input type="date" id="p-dob"></div>
                <div style="flex:1"><label style="font-size:11px;">Nationality</label><input type="text" id="p-nationality" placeholder="Filipino"></div>
            </div>

            <div class="section-header">Passport Info</div>
            <div class="form-row">
                <div style="flex:1"><input type="text" id="p-passport" placeholder="Passport No."></div>
                <div style="flex:1"><label style="font-size:11px;">Expiry Date</label><input type="date" id="p-expiry"></div>
            </div>

            <div class="section-header">Emergency Contact</div>
            <div class="form-row"><input type="text" id="p-emergency-name" placeholder="Contact Person"><input type="text" id="p-emergency-number" placeholder="Contact No."></div>
        </div>

        <div class="modal-footer" style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
            <div class="total-price" id="modal-total-price">₱0</div>
            <button class="confirm-btn" onclick="submitBookingToDatabase()">Confirm & Pay</button>
        </div>
    </div>
</div>

<div id="success-modal" class="modal-overlay hidden">
    <div class="modal-content" style="text-align: center; max-width:400px;">
        <span class="close-modal" onclick="closeSuccessModal()">&times;</span>
        <div style="font-size: 60px; color: #28a745;">&#10004;</div>
        <h2>Booking Confirmed!</h2>
        <p>Saved to Database.</p>
        <button class="confirm-btn" onclick="closeSuccessModal()" style="margin-top:20px;">OK</button>
    </div>
</div>

<footer class="main-footer">
    <div class="footer-bottom"><p>&copy; 2025 AERONEXA. All rights reserved.</p></div>
</footer>

<script>
    // 1. Show Right Panel Logic
    function showRightPanel(id, route, dep, arr, price, flightNum) {
        const rightPanel = document.getElementById('right-panel');
        const bookingContainer = document.getElementById('booking-container');
        
        rightPanel.classList.remove('hidden');
        bookingContainer.classList.add('three-column');

        document.getElementById('details-content').innerHTML = `
            <h2>${route}</h2>
            <p><strong>Flight:</strong> ${flightNum}</p>
            <p><strong>Time:</strong> ${dep} - ${arr}</p>
            <p><strong>Price:</strong> ₱${price}</p>
            <button class="book-btn" style="width:100%; margin-top:15px;" 
                onclick="openBookingModal('${id}', '${route}', '${price}', '${flightNum}')">
                Proceed to Book
            </button>
        `;
    }

    function closeRightPanel() {
        document.getElementById('right-panel').classList.add('hidden');
        document.getElementById('booking-container').classList.remove('three-column');
    }

    // 2. Open Modal Logic
    function openBookingModal(id, route, price, flightNum) {
        document.getElementById('booking-modal').classList.remove('hidden');
        document.getElementById('selected-flight-id').value = id;
        document.getElementById('modal-total-price').innerText = "₱" + price;
        document.getElementById('modal-flight-summary').innerText = flightNum + ": " + route;
    }

    // 3. Submit Logic (FIXED FOR MONGODB)
    async function submitBookingToDatabase() {
        const btn = document.querySelector('.confirm-btn');
        const originalText = btn.innerText;
        btn.innerText = "Processing..."; btn.disabled = true;

        try {
            // A. Gather Data
            const payload = {
                user_id: "1",
                first_name: document.getElementById('p-fname').value,
                last_name: document.getElementById('p-lname').value,
                email: document.getElementById('p-email').value,
                contact_number: document.getElementById('p-contact').value,
                gender: document.getElementById('p-gender').value,
                civil_status: document.getElementById('p-civil').value,
                birthdate: document.getElementById('p-dob').value,
                nationality: document.getElementById('p-nationality').value,
                passport_number: document.getElementById('p-passport').value,
                passport_expiry: document.getElementById('p-expiry').value,
                special_assistance: "None",
                emergency_contact_name: document.getElementById('p-emergency-name').value,
                emergency_contact_number: document.getElementById('p-emergency-number').value,
                type: "adult"
            };

            // B. Validate
            if(!payload.first_name || !payload.email || !payload.passport_number || !payload.emergency_contact_name) {
                alert("Please complete ALL details.");
                btn.innerText = originalText; btn.disabled = false; return;
            }

            console.log("Creating Passenger...", payload);

            // C. Create Passenger
            const passRes = await fetch('http://localhost:8000/api/psa/passengers', {
                method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json'}, 
                body: JSON.stringify(payload)
            });
            const passData = await passRes.json();
            
            if (!passRes.ok) {
                throw new Error("Passenger Error: " + (passData.message || JSON.stringify(passData)));
            }

            // D. Extract ID (UPDATED FOR MONGODB)
            // MongoDB often returns '_id' OR 'id'. We check both.
            let passengerId = passData.id || passData._id || (passData.data ? (passData.data.id || passData.data._id) : null);
            
            if (!passengerId) {
                // FALLBACK ONLY IF REALLY NECESSARY, BUT ALERTING TO DEBUG
                console.warn("ID not found in response:", passData);
                throw new Error("Passenger created but ID could not be retrieved from MongoDB.");
            }

            // E. Book Flight
            let flightDate = document.getElementById('departure').value || new Date().toISOString().split('T')[0];
            const bookRes = await fetch('http://localhost:8000/api/psa/bookings', {
                method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                body: JSON.stringify({
                    flight_id: document.getElementById('selected-flight-id').value,
                    user_id: "1",
                    passenger_id: String(passengerId), // MongoDB IDs are strings
                    flight_date: flightDate,
                    status: "confirmed",
                    seat_number: "A1",
                    booking_date: new Date().toISOString().split('T')[0]
                })
            });

            const bookData = await bookRes.json();

            if(bookRes.ok) {
                closeBookingModal();
                document.getElementById('success-modal').classList.remove('hidden');
            } else {
                alert("Booking Failed: " + (bookData.message || JSON.stringify(bookData)));
            }

        } catch (e) {
            console.error(e);
            alert("Error: " + e.message);
        } finally {
            btn.innerText = originalText; btn.disabled = false;
        }
    }

    function closeBookingModal() { document.getElementById('booking-modal').classList.add('hidden'); }
    function closeSuccessModal() { document.getElementById('success-modal').classList.add('hidden'); }

    document.getElementById('close-details-panel').addEventListener('click', () => {
        closeRightPanel();
    });
</script>

</body>
</html>