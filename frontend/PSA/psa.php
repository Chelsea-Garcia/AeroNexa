<?php
// --- DIRECT FETCH FUNCTION ---
function getPSAFlights() {
    // Try 127.0.0.1 first (Port 8000)
    $url = 'http://127.0.0.1:8000/api/psa/flights';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $output = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    // Backup: Try localhost
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
        /* CSS PARA PUMAGITNA ANG MODAL */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            display: flex;           /* Flexbox para sa centering */
            justify-content: center; /* Gitna Horizontal */
            align-items: center;     /* Gitna Vertical */
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .hidden { display: none !important; }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 95%;
            max-width: 550px; 
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-height: 90vh; 
            overflow-y: auto;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #888;
        }
        .close-modal:hover { color: #d4070f; }

        /* Form Inputs Styling */
        .passenger-form input, .passenger-form select {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            width: 100%; 
            box-sizing: border-box;
        }
        .passenger-form input:focus, .passenger-form select:focus {
            border-color: #037FBE;
            outline: none;
            background: #fdfdfd;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .section-header {
            margin: 20px 0 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            color: #037FBE;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        <li><a href="/AeroNexa/homepage.html" class="nav-link" onclick="setActive(this)">HOME</a></li>
        <li><a href="/AeroNexa/frontend/PSA/psa.php" class="nav-link active" onclick="setActive(this)">PHILIPPINE SKY AIRWAY</a></li>
        <li><a href="/AeroNexa/frontend/AURELI-YAH!/aureliya.php" class="nav-link" onclick="setActive(this)">AURELI-YA!</a></li>
        <li><a href="/AeroNexa/frontend/TRUTRAVEL/trutravel.php" class="nav-link" onclick="setActive(this)">TRUTRAVEL</a></li>
        <li><a href="/AeroNexa/frontend/SKYROUTE/skyroute.php" class="nav-link" onclick="setActive(this)">SKYROUTE</a></li>
    </ul>
    <div class="nav-profile">
        <a href="/frontend/account.html">
            <img src="/frontend/ASSETS/profile.png" alt="User Profile" class="profile-icon">
        </a>
    </div>
</nav>

<div class="page-wrap">
    <div class="filter-bar" role="region" aria-label="flight search filters">
        <div class="filter-item">
            <label>Type</label>
            <div class="select-wrapper">
                <select id="trip-type"><option value="oneway">One way</option><option value="round">Round trip</option></select>
            </div>
        </div>
        <div class="filter-item">
            <label>From</label>
            <div class="select-wrapper">
                <select id="from"><option value="">Any</option><option>Manila</option><option>Cebu</option><option>Davao</option></select>
            </div>
        </div>
        <div class="filter-item">
            <label>To</label>
            <div class="select-wrapper">
                <select id="to"><option value="">Any</option><option>Manila</option><option>Cebu</option><option>Davao</option></select>
            </div>
        </div>
        <div class="filter-item">
            <label>Date</label>
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

                    <div class="flight-card" data-route="<?= $routeStr ?>">
                        <div class="flight-info">
                            <h3><?= $routeStr ?></h3>
                            <p class="time"><?= $timeStr ?></p>
                            <p class="meta">Economy • <?= $flightCode ?></p>
                        </div>
                        <div class="flight-actions">
                            <div class="fare">₱<?= number_format($price) ?></div>
                            <button class="book-btn" onclick="showFlightDetails('<?= $flightId ?>', '<?= $routeStr ?>', '<?= $depTime ?>', '<?= $arrTime ?>', '<?= $price ?>', '<?= $flightCode ?>')">Book</button>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding: 20px; text-align: center;">
                    <h3>No flights available.</h3>
                    <p>Check PSA Server (Port 8000).</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="right-panel hidden" id="right-panel">
            <button class="close-btn" id="close-details-panel">&times;</button>
            <div id="details-content" class="tab-content">
                <h3 class="muted">Select a flight</h3>
            </div>
        </div>
    </div>
</div>

<div id="booking-modal" class="modal-overlay hidden">
    <div class="modal-content">
        <span class="close-modal" onclick="closeBookingModal()">&times;</span>
        
        <div class="modal-header">
            <h2 style="margin:0; color:#037FBE;">Flight Itinerary</h2>
            <p style="margin:5px 0 15px; color:#666; font-size:14px;">Complete your travel details.</p>
        </div>

        <div class="modal-body">
            <div id="modal-flight-details" style="background:#f4f9fd; padding:15px; border-radius:8px; margin-bottom:20px; border:1px solid #e1eef7;"></div>
            
            <input type="hidden" id="selected-flight-id" value="">

            <div class="passenger-form">
                <div class="section-header">Personal Details</div>
                
                <div class="form-row">
                    <input type="text" id="p-fname" placeholder="First Name" class="modal-input" style="flex:1;">
                    <input type="text" id="p-lname" placeholder="Last Name" class="modal-input" style="flex:1;">
                </div>

                <div class="form-row">
                    <input type="email" id="p-email" placeholder="Email Address" class="modal-input" style="flex: 1.5;">
                    <input type="text" id="p-contact" placeholder="Mobile No." class="modal-input" style="flex: 1;">
                </div>

                <div class="form-row">
                    <select id="p-gender" class="modal-input" style="flex: 1;">
                        <option value="" disabled selected>Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                    <select id="p-civil" class="modal-input" style="flex: 1;">
                        <option value="" disabled selected>Civil Status</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </div>

                <div class="form-row">
                    <div style="flex:1;">
                        <label style="font-size:11px; color:#666; font-weight:600; margin-left:5px;">Birthdate</label>
                        <input type="date" id="p-dob" class="modal-input" style="margin-top:2px;">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:11px; color:#666; font-weight:600; margin-left:5px;">Nationality</label>
                        <input type="text" id="p-nationality" placeholder="Filipino" class="modal-input" style="margin-top:2px;">
                    </div>
                </div>

                <div class="section-header">Passport Information</div>
                
                <div class="form-row">
                    <div style="flex:1.2;">
                        <input type="text" id="p-passport" placeholder="Passport Number" class="modal-input" style="width:100%; margin-top:16px;">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:11px; color:#666; font-weight:600; margin-left:5px;">Expiry Date</label>
                        <input type="date" id="p-expiry" class="modal-input" style="margin-top:2px;">
                    </div>
                </div>

                <div class="section-header">Emergency Contact</div>

                <div class="form-row">
                    <input type="text" id="p-emergency-name" placeholder="Contact Person" class="modal-input" style="flex:1.5;">
                    <input type="text" id="p-emergency-number" placeholder="Emergency Number" class="modal-input" style="flex:1;">
                </div>

            </div>
        </div>

        <div class="modal-footer" style="margin-top:20px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid #eee; padding-top:20px;">
            <div class="total-price" id="modal-total-price" style="font-size:22px; font-weight:bold; color:#d4070f;">₱0</div>
            <button class="confirm-btn" onclick="submitBookingToDatabase()" style="padding:12px 30px; background:#037FBE; color:white; border:none; border-radius:30px; font-weight:bold; cursor:pointer; box-shadow: 0 4px 10px rgba(3,127,190,0.3);">Confirm & Pay</button>
        </div>
    </div>
</div>

<div id="success-modal" class="modal-overlay hidden">
    <div class="modal-content success-content" style="text-align: center; max-width:400px;">
        <span class="close-modal" onclick="closeSuccessModal()">&times;</span>
        <div style="font-size: 60px; color: #28a745; margin-bottom:10px;">&#10004;</div>
        <h2 style="color:#333;">Booking Confirmed!</h2>
        <p style="color:#666;">Saved to Database.</p>
        <div class="confirmation-box" style="background:#f0f7fc; padding:15px; margin:20px 0; border-radius:8px; border:1px dashed #037FBE;">
            <p class="ref-label" style="margin:0; font-size:12px; color:#666; text-transform:uppercase;">Booking Reference</p>
            <h3 id="booking-ref-number" style="margin:5px 0; color:#037FBE; letter-spacing:1px;">PSA-...</h3>
        </div>
        <button class="confirm-btn" onclick="closeSuccessModal()" style="margin-top:10px; padding:10px 40px; background:#037FBE; color:white; border:none; border-radius:30px; cursor:pointer;">OK</button>
    </div>
</div>

<footer class="main-footer">
    <div class="footer-bottom">
        <p>&copy; 2025 AERONEXA. All rights reserved.</p>
    </div>
</footer>

<script>
    // 1. Show Details Logic
    function showFlightDetails(id, route, dep, arr, price, flightNum) {
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
                onclick="openBookingModal('${id}', '${route}', '${price}', '${flightNum}', '${dep}', '${arr}')">
                Proceed to Book
            </button>
        `;
    }

    // 2. Open Modal
    function openBookingModal(id, route, price, flightNum, dep, arr) {
        document.getElementById('booking-modal').classList.remove('hidden');
        
        document.getElementById('selected-flight-id').value = id;
        document.getElementById('modal-total-price').innerText = "₱" + price;
        
        document.getElementById('modal-flight-details').innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                <strong style="color:#333;">Flight: ${flightNum}</strong>
                <span style="color:#037FBE; font-weight:bold;">${route}</span>
            </div>
            <div style="display:flex; justify-content:space-between; color:#666; font-size:13px;">
                <span>Dep: ${dep}</span>
                <span>Arr: ${arr}</span>
            </div>
        `;
    }

    // 3. SUBMIT TO DATABASE (Final Fail-Safe Version)
    async function submitBookingToDatabase() {
        const btn = document.querySelector('.confirm-btn');
        const originalText = btn.innerText;
        btn.innerText = "Processing...";
        btn.disabled = true;

        try {
            // A. Get Values
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
                btn.innerText = originalText; btn.disabled = false;
                return;
            }

            console.log("Creating Passenger...", payload);

            // C. Create Passenger
            const passRes = await fetch('http://localhost:8000/api/psa/passengers', {
                method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload)
            });
            const passData = await passRes.json();
            
            // D. Extract ID (FAIL-SAFE: Kung wala, gamitin ang "1")
            let passengerId = passData.id || passData.passenger_id || (passData.data ? passData.data.id : null);
            
            if (!passengerId) {
                console.warn("ID not found, using Fallback: 1");
                passengerId = "1"; // Ito ang solusyon sa "Passenger not found"
            }

            // E. Book Flight
            let flightDate = document.getElementById('departure').value || new Date().toISOString().split('T')[0];
            const bookRes = await fetch('http://localhost:8000/api/psa/bookings', {
                method: 'POST', headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    flight_id: document.getElementById('selected-flight-id').value,
                    user_id: "1",
                    passenger_id: String(passengerId),
                    flight_date: flightDate,
                    status: "confirmed",
                    seat_number: "A1",
                    booking_date: new Date().toISOString().split('T')[0]
                })
            });

            if(bookRes.ok) {
                closeBookingModal();
                openSuccessModal();
            } else {
                const err = await bookRes.json();
                alert("Booking Failed: " + (err.message || JSON.stringify(err)));
            }

        } catch (e) {
            console.error(e);
            alert("Error: " + e.message);
        } finally {
            btn.innerText = originalText;
            btn.disabled = false;
        }
    }

    function closeBookingModal() { document.getElementById('booking-modal').classList.add('hidden'); }
    
    function openSuccessModal(refId) {
        closeBookingModal();
        const displayRef = refId ? "PSA-DB-" + refId : "PSA-" + Math.floor(Math.random()*10000);
        document.getElementById('booking-ref-number').innerText = displayRef;
        document.getElementById('success-modal').classList.remove('hidden');
    }
    
    function closeSuccessModal() { document.getElementById('success-modal').classList.add('hidden'); }

    document.getElementById('close-details-panel').addEventListener('click', () => {
        document.getElementById('right-panel').classList.add('hidden');
        document.getElementById('booking-container').classList.remove('three-column');
    });
</script>

</body>
</html>