<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>AERONEXA - Master Interface</title>
    <style>
        /* --- RESET & BASIC STYLE --- */
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Poppins', sans-serif; background: #f4f7fa; height: 100vh; display: flex; }
        
        /* --- SIDEBAR --- */
        .sidebar { width: 260px; background: #04498D; color: white; display: flex; flex-direction: column; }
        .logo-area { height: 60px; display: flex; align-items: center; padding: 0 20px; font-weight: 700; font-size: 18px; background: rgba(0,0,0,0.1); }
        .menu { list-style: none; padding: 0; margin: 20px 0; }
        .menu li { padding: 15px 25px; cursor: pointer; transition: 0.2s; border-left: 4px solid transparent; }
        .menu li:hover { background: rgba(255,255,255,0.1); }
        .menu li.active { background: #fff; color: #04498D; border-left-color: #FFEC3D; font-weight: 700; }
        
        /* --- MAIN CONTENT --- */
        .main { flex: 1; padding: 30px; overflow-y: auto; }
        .panel { display: none; }
        .panel.active { display: block; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

        /* --- LAYOUTS --- */
        .split-view { display: flex; gap: 20px; height: calc(100vh - 100px); }
        .form-side { flex: 1; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow-y: auto; }
        .list-side { flex: 1.5; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow-y: auto; }

        /* --- FORMS --- */
        h2 { margin-top: 0; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; font-size: 18px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 10px; }
        .form-group.full { grid-column: span 2; }
        label { display: block; font-size: 12px; font-weight: 600; color: #666; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; }
        input:focus, select:focus { border-color: #04498D; outline: none; }
        
        button.submit-btn { width: 100%; padding: 12px; border: none; border-radius: 5px; color: white; font-weight: 700; cursor: pointer; margin-top: 20px; transition: 0.2s; }
        .btn-psa { background: #003366; }
        .btn-aur { background: #FF5A5F; }
        .btn-sky { background: #FFC107; color: #333; }

        /* --- LIST TABLES --- */
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #f8f9fa; text-align: left; padding: 12px; color: #666; position: sticky; top: 0; }
        td { padding: 12px; border-bottom: 1px solid #eee; cursor: pointer; }
        tr:hover { background: #f0f8ff; }
        tr.selected { background: #e0f0ff; border-left: 3px solid #04498D; }
        .price-tag { font-weight: 700; color: #04498D; }

        /* --- SKYROUTE SPECIAL --- */
        .sky-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
    </style>
</head>
<body>

    <div class="sidebar">
    
    <div class="logo-area" style="height: auto; min-height: 140px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding-top: 20px; padding-bottom: 10px; background: transparent;">
        
        <img src="/AeroNexa/AeroNexa-SmartSystem-forApi/AeroNexa/logo.png" 
     alt="AeroNexa" 
     style="width: 120px; height: auto; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
        
        <div style="margin-top: -2px; font-size: 20px; font-weight: 700; color: white; letter-spacing: 1px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
            AeroNexa
        </div>

    </div>
        <ul class="menu">
            <li class="active" onclick="switchTab('psa', this)">✈️ PSA (Flights)</li>
            <li onclick="switchTab('aureliya', this)">🏠 Aureliya (Hotels)</li>
            <li onclick="switchTab('skyroute', this)">🚌 SkyRoute (Transport)</li>
            <li onclick="switchTab('trutravel', this)">🌍 TruTravel (Packages)</li>
    
            <li onclick="switchTab('aeropay', this); loadTransactions();">💸 AeroPay (Transactions)</li>
        </ul>
    </div>

    <div class="main">
        
        <div id="psa" class="panel active">
            <div class="split-view">
                <div class="form-side">
                    <h2>Passenger Details</h2>
                    <input type="hidden" id="psa_flight_id">
                    <div class="form-group" style="background:#eef; padding:10px; border-radius:5px; margin-bottom:15px;">
                        <label>Selected Flight:</label>
                        <strong id="psa_selected_flight" style="color:#003366;">None</strong>
                    </div>
                    <div class="form-grid">
                        <div class="form-group"><label>First Name</label><input type="text" id="psa_fname"></div>
                        <div class="form-group"><label>Last Name</label><input type="text" id="psa_lname"></div>
                        <div class="form-group full"><label>Email Address</label><input type="email" id="psa_email"></div>
                        <div class="form-group"><label>Contact No.</label><input type="text" id="psa_contact"></div>
                        <div class="form-group"><label>Gender</label><select id="psa_gender"><option>Male</option><option>Female</option><option>Other</option></select></div>
                        <div class="form-group"><label>Birthdate</label><input type="date" id="psa_bdate"></div>
                        <div class="form-group"><label>Nationality</label><input type="text" id="psa_nation"></div>
                        <div class="form-group"><label>Passport No.</label><input type="text" id="psa_pass_no"></div>
                        <div class="form-group"><label>Passport Expiry</label><input type="date" id="psa_pass_exp"></div>
                        <div class="form-group full"><label>Special Assistance</label><input type="text" id="psa_special" placeholder="e.g. Wheelchair"></div>
                        <div class="form-group"><label>Emergency Name</label><input type="text" id="psa_em_name"></div>
                        <div class="form-group"><label>Emergency No.</label><input type="text" id="psa_em_num"></div>
                    </div>
                    <button class="submit-btn btn-psa" onclick="submitPSA()">BOOK FLIGHT</button>
                </div>
                
                <div class="list-side">
    <h2>Available Flights</h2>
    
    <div style="background:#f1f5f9; padding:15px; border-radius:8px; margin-bottom:15px; border:1px solid #e0e6ed;">
        <div style="font-size:11px; font-weight:700; color:#04498D; text-transform:uppercase; margin-bottom:8px;">Filter Flights</div>
        
        <div class="form-grid" style="grid-template-columns: repeat(3, 1fr); gap:10px;">
            <input type="text" id="psa_f_num" placeholder="Flight No..." onkeyup="filterPSA()">
            <input type="text" id="psa_f_org" placeholder="Origin (MNL)..." onkeyup="filterPSA()">
            <input type="text" id="psa_f_dst" placeholder="Dest (CEB)..." onkeyup="filterPSA()">
            
            <input type="time" id="psa_f_time" onchange="filterPSA()" title="Filter by Departure Time">
            <input type="number" id="psa_f_min" placeholder="Min Price" onkeyup="filterPSA()">
            <input type="number" id="psa_f_max" placeholder="Max Price" onkeyup="filterPSA()">
        </div>
        <button onclick="filterPSA()" style="width:100%; margin-top:10px; background:#04498D; color:white; border:none; padding:10px; border-radius:5px; cursor:pointer; font-weight:bold; transition:0.2s;">
        <i class="fas fa-search"></i> Search Flights
    </button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Flight No.</th>
                <th>Route</th>
                <th>Type</th>
                <th>Time</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody id="psa_list">
            </tbody>
    </table>
</div>
            </div>
        </div>

        <div id="aureliya" class="panel">
            <div class="split-view">
                <div class="form-side">
                    <h2>Booking Details</h2>
                    <input type="hidden" id="aur_prop_id">
                    <input type="hidden" id="aur_base_price">
                    <input type="hidden" id="aur_max_guests_limit"> <div class="form-group" style="background:#fee; padding:10px; border-radius:5px; margin-bottom:15px;">
                        <label>Selected Property:</label>
                        <strong id="aur_selected_prop" style="color:#FF5A5F;">None</strong>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group"><label>Check In</label><input type="date" id="aur_in" onchange="calcPrice()"></div>
                        <div class="form-group"><label>Check Out</label><input type="date" id="aur_out" onchange="calcPrice()"></div>
                        
                        <div class="form-group">
                            <label>Guests</label>
                            <input type="number" id="aur_guests" min="1" value="1">
                            <small id="aur_guest_hint" style="color:#FF5A5F; font-size:11px;">Max guests: -</small>
                        </div>
                    </div>

                    <div style="margin-top:20px; padding:15px; background:#f9f9f9; border-radius:8px; text-align:center;">
                        <label>Total Price</label>
                        <div id="aur_total_display" style="font-size:24px; font-weight:700; color:#333;">₱0.00</div>
                    </div>

                    <button class="submit-btn btn-aur" onclick="submitAureliya()">CONFIRM BOOKING</button>
                </div>

                <div class="list-side">
    <h2>Properties</h2>

    <div style="background:#fff5f5; padding:15px; border-radius:8px; margin-bottom:15px; border:1px solid #ffecec;">
        <div style="font-size:11px; font-weight:700; color:#FF5A5F; text-transform:uppercase; margin-bottom:8px;">Filter Properties</div>
        
        <div class="form-grid" style="grid-template-columns: repeat(2, 1fr); gap:10px;">
            <input type="text" id="aur_f_loc" placeholder="Location (City)..." onkeyup="filterAureliya()">
            <input type="text" id="aur_f_type" placeholder="Type (Hotel, Room)..." onkeyup="filterAureliya()">
            
            <input type="number" id="aur_f_guest" placeholder="Min Guests" onkeyup="filterAureliya()">
            <div style="display:flex; gap:5px;">
                <input type="number" id="aur_f_min" placeholder="Min ₱" onkeyup="filterAureliya()">
                <input type="number" id="aur_f_max" placeholder="Max ₱" onkeyup="filterAureliya()">
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Property</th>
                <th>Location</th>
                <th>Type</th>
                <th>Max</th>
                <th>Price/Night</th>
            </tr>
        </thead>
        <tbody id="aur_list">
            </tbody>
    </table>
</div>
            </div>
        </div>

        <div id="skyroute" class="panel">
            <div class="sky-container">
                <h2 style="color:#d4a000;">🚌 Book Transport</h2>
                
                <div class="form-grid">
                    <div class="form-group full">
                        <label>🏳️ Select Country (Region)</label>
                        <select id="sky_main_country" onchange="updateSkyCountry(this.value)" style="font-weight:bold; color:#04498D;">
                            <option value="">Select Country...</option>
                        </select>
                    </div>

                    <div class="form-group"><label>Vehicle Type</label>
                        <select id="sky_type" onchange="calcSkyRoute()">
                            <option value="Bus">Bus (Base: 50 | Max: 56)</option>
                            <option value="Car">Car (Base: 100 | Max: 5)</option>
                            <option value="SUV">SUV (Base: 200 | Max: 7)</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Passengers</label>
                        <input type="number" id="sky_pax" min="1" value="1" onchange="calcSkyRoute()" onkeyup="calcSkyRoute()">
                    </div>
                    
                    <div class="form-group"><label>Date</label><input type="date" id="sky_date"></div>
                    <div class="form-group"><label>Time</label><input type="time" id="sky_time"></div>
                </div>

                <hr style="border:0; border-top:1px solid #eee; margin:20px 0;">

                <h3>📍 Origin</h3>
                <div class="form-grid">
                    <div class="form-group"><label>Division</label>
                        <select id="sky_org_div" onchange="loadCities('sky_org', this.value)"></select>
                    </div>
                    <div class="form-group"><label>City</label>
                        <select id="sky_org_city" onchange="calcSkyRoute()"></select>
                    </div>
                </div>

                <h3>🏁 Destination</h3>
                <div class="form-grid">
                    <div class="form-group"><label>Division</label>
                        <select id="sky_dst_div" onchange="loadCities('sky_dst', this.value)"></select>
                    </div>
                    <div class="form-group"><label>City</label>
                        <select id="sky_dst_city" onchange="calcSkyRoute()"></select>
                    </div>
                </div>

                <div style="margin:20px 0; padding:15px; background:#fff8e1; border:1px solid #ffe082; border-radius:8px; text-align:center;">
                    <div style="display:flex; justify-content:space-around; align-items:center;">
                        <div>
                            <small style="color:#666;">Est. Distance</small>
                            <div id="sky_dist_display" style="font-weight:700; font-size:18px; color:#555;">0 km</div>
                        </div>
                        <div style="height:30px; border-left:1px solid #ddd;"></div>
                        <div>
                            <small style="color:#666;">Total Price</small>
                            <div id="sky_price_display" style="font-weight:700; font-size:24px; color:#d4a000;">₱0.00</div>
                        </div>
                    </div>
                </div>

                <button class="submit-btn btn-sky" onclick="submitSkyRoute()">CONFIRM BOOKING</button>
            </div>
        </div>

        <div id="trutravel" class="panel">
            <div class="split-view">
                
                <div class="form-side" style="flex:2; background:#fdfdfd; border-right:1px solid #eee; overflow-y:auto;">
                    
                    <div id="tru_featured_grid">
                        <h2 style="color:#2c3e50; margin-bottom:20px;">🌟 Featured Packages</h2>
                        <div id="tru_grid_container" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:15px;">
                            <div style="text-align:center; padding:20px; color:#999;">Loading...</div>
                        </div>
                    </div>

                    <div id="tru_booking_view" style="display:none;">
                        <button onclick="showTruGrid()" style="background:none; border:none; color:#04498D; cursor:pointer; margin-bottom:10px;">
                            <i class="fas fa-arrow-left"></i> Back to Featured
                        </button>

                        <div style="background:#2c3e50; color:white; padding:20px; border-radius:8px; position:relative;">
                            <span id="tru_type" style="background:#e74c3c; padding:3px 8px; border-radius:4px; font-size:11px; text-transform:uppercase; font-weight:bold;">TYPE</span>
                            <h1 id="tru_name" style="margin:10px 0 5px 0;">Package Name</h1>
                            <div style="font-size:14px; opacity:0.8;">Duration: <span id="tru_nights">3</span> Nights</div>
                        </div>

                        <p id="tru_desc" style="margin:20px 0; line-height:1.6; color:#555;">Description...</p>

                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:20px;">
                            <div style="background:#f0f8ff; padding:10px; border-radius:5px; text-align:center;">
                                <i class="fas fa-plane" style="color:#04498D;"></i><br><small>Roundtrip Flights</small>
                            </div>
                            <div style="background:#fff0f0; padding:10px; border-radius:5px; text-align:center;">
                                <i class="fas fa-hotel" style="color:#FF5A5F;"></i><br><small>Hotel Stay</small>
                            </div>
                            <div style="background:#fff8e1; padding:10px; border-radius:5px; text-align:center;">
                                <i class="fas fa-bus" style="color:#d4a000;"></i><br><small>Transfers</small>
                            </div>
                        </div>

                        <div style="background:#fff; padding:25px; border-radius:12px; border:1px solid #eef; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
    
    <div style="text-align:right; margin-bottom:25px; border-bottom:1px solid #f0f0f0; padding-bottom:15px;">
        <small style="color:#888; font-size:11px; text-transform:uppercase; letter-spacing:1px;">Total Package Price</small>
        <div id="tru_final" style="font-size:36px; font-weight:800; color:#2c3e50; line-height:1;">₱0.00</div>
        
        <div id="tru_price_details" style="font-size:13px; margin-top:5px;">
            <span id="tru_base" style="text-decoration:line-through; color:#aaa; margin-right:8px;">₱0.00</span>
            <span id="tru_disc" style="color:#d32f2f; font-weight:700; background:#ffebee; padding:3px 8px; border-radius:20px; font-size:10px;">0% OFF</span>
        </div>
    </div>
    
    <input type="hidden" id="tru_pkg_id">

    <div class="form-grid" style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px; margin-bottom:5px;">
        
        <div class="form-group">
            <label style="font-size:11px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">📅 Start Date</label>
            <input type="date" id="tru_date" 
                   style="width:100%; padding:12px; font-size:13px; border:2px solid #f1f1f1; border-radius:8px; outline:none; transition:0.2s;"
                   onfocus="this.style.borderColor='#04498D'" onblur="this.style.borderColor='#f1f1f1'">
        </div>

        <div class="form-group">
            <label style="font-size:11px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">⏰ Pickup Time</label>
            <input type="time" id="tru_time" value="08:00"
                   style="width:100%; padding:12px; font-size:13px; border:2px solid #f1f1f1; border-radius:8px; outline:none; transition:0.2s;"
                   onfocus="this.style.borderColor='#04498D'" onblur="this.style.borderColor='#f1f1f1'">
        </div>

        <div class="form-group">
            <label style="font-size:11px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">👥 Guests</label>
            <input type="number" id="tru_guests" value="2" min="1" 
                   style="width:100%; padding:12px; font-size:13px; border:2px solid #f1f1f1; border-radius:8px; font-weight:bold; color:#2c3e50; outline:none; transition:0.2s;"
                   onfocus="this.style.borderColor='#04498D'" onblur="this.style.borderColor='#f1f1f1'">
        </div>

    </div>
    
    <div style="background:#f9fbfd; padding:10px; border-radius:6px; margin-bottom:20px; font-size:12px; color:#667; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-info-circle" style="color:#04498D;"></i>
        <span>Itinerary & return date are auto-calculated.</span>
    </div>

    <button class="submit-btn" style="background:#2c3e50; width:100%; padding:15px; font-size:14px; letter-spacing:1px; border-radius:8px; box-shadow:0 4px 10px rgba(44, 62, 80, 0.2);" onclick="bookTruTravel()">
        CONFIRM & BOOK PACKAGE
    </button>
</div>
                    </div>
                </div>

                <div class="list-side" style="flex:1; background:#f8f9fa;">
                    <h3 style="margin-top:0;">📦 More Packages</h3>
                    <div id="tru_list" style="display:flex; flex-direction:column; gap:10px;">
                        </div>
                </div>
            </div>
        </div>

        <div id="aeropay" class="panel">
    <div style="background:white; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom:2px solid #f4f7fa; padding-bottom:15px;">
            <div>
                <h2 style="margin:0; color:#2c3e50; border:none; padding:0;">💳 AeroPay Central Ledger</h2>
                <small style="color:#888;">Live synchronization with partner booking systems.</small>
            </div>
            <button onclick="loadTransactions()" style="background:#04498D; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px; transition:0.2s;">
                <i class="fas fa-sync-alt"></i> Sync Data
            </button>
        </div>

        <table style="width:100%; border-collapse:separate; border-spacing:0 10px;">
            <thead>
                <tr style="background:none;">
                    <th style="color:#888; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:1px; border:none;">Transaction Code</th>
                    <th style="color:#888; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:1px; border:none;">Partner</th>
                    <th style="color:#888; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:1px; border:none;">Amount</th>
                    <th style="color:#888; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:1px; border:none;">Ref ID</th>
                    <th style="color:#888; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:1px; border:none;">Date</th>
                    <th style="color:#888; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:1px; border:none;">Status (Action)</th>
                </tr>
            </thead>
            <tbody id="apay_list">
                <tr><td colspan="6" style="text-align:center; padding:20px; color:#999;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

    </div>

    <script>

    // --- 1. TAB SWITCHING LOGIC ---
    function switchTab(id, element) {
            // Hide all panels
            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            // Show selected panel
            const panel = document.getElementById(id);
            if(panel) panel.classList.add('active');

            // Update Menu Styles
            document.querySelectorAll('.menu li').forEach(l => l.classList.remove('active'));
            // Highlight clicked menu item
            if(element) element.classList.add('active');
        }

        // --- REAL-TIME CALCULATION ---
        async function calcSkyRoute() {
            // 1. Get Values
            const type = document.getElementById('sky_type').value;
            const pax = document.getElementById('sky_pax').value;
            const orgCity = document.getElementById('sky_org_city').value;
            const dstCity = document.getElementById('sky_dst_city').value;

            // 2. Only calculate if we have both cities
            if (!orgCity || !dstCity || orgCity === dstCity) {
                document.getElementById('sky_dist_display').innerText = "0 km";
                document.getElementById('sky_price_display').innerText = "₱0.00";
                return;
            }

            // 3. Send to Calculator Endpoint
            try {
                const res = await fetch('http://127.0.0.1:8000/api/sky-calculate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        vehicle_type: type,
                        passengers: pax,
                        org_city: orgCity,
                        dst_city: dstCity
                    })
                });
                
                const data = await res.json();
                
                // 4. Update Display
                if (res.ok) {
                    document.getElementById('sky_dist_display').innerText = data.distance + " km";
                    document.getElementById('sky_price_display').innerText = "₱" + Number(data.price).toLocaleString();
                }
            } catch (e) {
                console.error("Calc Error:", e);
            }
        }
        
        async function loadCountries() {
            try {
                const res = await fetch('http://127.0.0.1:8000/api/sky-loc/countries');
                const countries = await res.json();
                
                const sel = document.getElementById('sky_main_country');
                if(sel) {
                    sel.innerHTML = '<option value="">Select Country...</option>';
                    countries.forEach(c => sel.innerHTML += `<option value="${c}">${c}</option>`);
                }
            } catch(e) { console.error("Error loading countries:", e); }
        }

        async function updateSkyCountry(country) {
            if(!country) return;
            
            // Clear Cities (Since country changed)
            document.getElementById('sky_org_city').innerHTML = '';
            document.getElementById('sky_dst_city').innerHTML = '';

            try {
                // Fetch Divisions for the selected country
                const res = await fetch(`http://127.0.0.1:8000/api/sky-loc/divisions/${country}`);
                const divs = await res.json();
                
                // Populate BOTH Division Dropdowns
                ['sky_org_div', 'sky_dst_div'].forEach(id => {
                    const sel = document.getElementById(id);
                    sel.innerHTML = '<option value="">Select Division...</option>';
                    divs.forEach(d => sel.innerHTML += `<option value="${d}">${d}</option>`);
                });
            } catch(e) { console.error("Error loading divisions:", e); }
        }

        async function loadDivisions(prefix, country) {
            if(!country) return;
            const sel = document.getElementById(`${prefix}_div`);
            sel.innerHTML = '<option value="">Loading...</option>';
            try {
                const res = await fetch(`http://127.0.0.1:8000/api/sky-loc/divisions/${country}`);
                const divs = await res.json();
                sel.innerHTML = '<option value="">Select Division...</option>';
                divs.forEach(d => sel.innerHTML += `<option value="${d}">${d}</option>`);
            } catch(e) { sel.innerHTML = '<option value="">Error</option>'; }
        }

        async function loadCities(prefix, div) {
            if(!div) return;
            const sel = document.getElementById(`${prefix}_city`);
            sel.innerHTML = '<option value="">Loading...</option>';
            try {
                const res = await fetch(`http://127.0.0.1:8000/api/sky-loc/cities/${div}`);
                const cities = await res.json();
                sel.innerHTML = '<option value="">Select City...</option>';
                cities.forEach(c => sel.innerHTML += `<option value="${c.city}">${c.city}</option>`);
            } catch(e) { sel.innerHTML = '<option value="">Error</option>'; }
        }

        // --- 3. PAGE LOAD INITIALIZATION ---
        document.addEventListener('DOMContentLoaded', async () => {
            console.log("Dashboard Loaded");

            // A. Trigger SkyRoute Countries
            loadCountries();

            // B. Load PSA Flights
            try {
                const res = await fetch('http://127.0.0.1:8000/api/psa-flights'); 
                const flights = await res.json();
                const tbody = document.getElementById('psa_list');
                if(tbody) {
                    tbody.innerHTML = ''; 
                    if (flights.length === 0) tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No flights found.</td></tr>';
                    
                    flights.forEach(f => {
                        const row = document.createElement('tr');
                        const num = f.flight_number || f.flight_code || '???';
                        const route = `${f.origin || '?'} ➝ ${f.destination || '?'}`;
                        const type = f.type || 'Standard';
                        const time = f.departure_time || 'TBA'; 
                        const price = f.price ? '₱' + Number(f.price).toLocaleString() : '₱0';
                        const mongoId = f._id || f.id; 

                        row.innerHTML = `<td><strong>${num}</strong></td><td>${route}</td><td>${type}</td><td>${time}</td><td class="price-tag">${price}</td>`;
                        row.onclick = () => {
                            document.querySelectorAll('#psa_list tr').forEach(r => r.classList.remove('selected'));
                            row.classList.add('selected');
                            document.getElementById('psa_flight_id').value = mongoId; 
                            document.getElementById('psa_selected_flight').innerText = `${num} (${route})`;
                        };
                        tbody.appendChild(row);
                    });
                }
            } catch(e) { console.error("PSA Load Error:", e); }

            // C. Load Aureliya Properties
            try {
                const res = await fetch('http://127.0.0.1:8000/api/aur-props');
                const props = await res.json();
                const tbody = document.getElementById('aur_list');
                if(tbody) {
                    tbody.innerHTML = '';
                    if (!Array.isArray(props) || props.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No properties found.</td></tr>';
                    } else {
                        props.forEach(p => {
                            const row = document.createElement('tr');
                            const title = p.title || p.name || 'Property';
                            const location = (p.city || '') + ', ' + (p.country || '');
                            const type = p.property_type || p.type || 'Hotel';
                            const guests = p.max_guests || p.guests || 2;
                            const price = p.price_per_night || p.price || 0;
                            const safeId = p.id || p._id;

                            row.innerHTML = `<td><strong>${title}</strong></td><td>${location}</td><td>${type}</td><td>${guests}</td><td class="price-tag">₱${Number(price).toLocaleString()}</td>`;
                            row.onclick = () => {
                                document.querySelectorAll('#aur_list tr').forEach(r => r.classList.remove('selected'));
                                row.classList.add('selected');
                                document.getElementById('aur_prop_id').value = safeId;
                                document.getElementById('aur_base_price').value = price;
                                document.getElementById('aur_max_guests_limit').value = guests;
                                document.getElementById('aur_selected_prop').innerText = title;
                                document.getElementById('aur_guest_hint').innerText = `Max guests: ${guests}`;
                                calcPrice();
                            };
                            tbody.appendChild(row);
                        });
                    }
                }
            } catch(e) { console.error("Aureliya Load Error:", e); }

            loadPackages();
        });


        // --- PSA FILTERING LOGIC ---
function renderPSA(data) {
    const tbody = document.getElementById('psa_list');
    tbody.innerHTML = ''; 
    if (data.length === 0) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No flights match filters.</td></tr>'; return; }

    data.forEach(f => {
        const row = document.createElement('tr');
        const num = f.flight_number || f.flight_code || '???';
        const route = `${f.origin || '?'} ➝ ${f.destination || '?'}`;
        const type = f.type || 'Standard';
        const time = f.departure_time || 'TBA'; 
        const price = f.price ? '₱' + Number(f.price).toLocaleString() : '₱0';
        const mongoId = f._id || f.id; 

        row.innerHTML = `<td><strong>${num}</strong></td><td>${route}</td><td>${type}</td><td>${time}</td><td class="price-tag">${price}</td>`;
        row.onclick = () => {
            document.querySelectorAll('#psa_list tr').forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');
            document.getElementById('psa_flight_id').value = mongoId; 
            document.getElementById('psa_selected_flight').innerText = `${num} (${route})`;
        };
        tbody.appendChild(row);
    });
}

function filterPSA() {
    const num = document.getElementById('psa_f_num').value.toLowerCase();
    const org = document.getElementById('psa_f_org').value.toLowerCase();
    const dst = document.getElementById('psa_f_dst').value.toLowerCase();
    const time = document.getElementById('psa_f_time').value;
    const min = parseFloat(document.getElementById('psa_f_min').value) || 0;
    const max = parseFloat(document.getElementById('psa_f_max').value) || 999999;

    const filtered = allFlights.filter(f => {
        const fNum = (f.flight_number || '').toLowerCase();
        const fOrg = (f.origin || '').toLowerCase();
        const fDst = (f.destination || '').toLowerCase();
        const fTime = (f.departure_time || '');
        const fPrice = parseFloat(f.price || 0);

        return fNum.includes(num) &&
               fOrg.includes(org) &&
               fDst.includes(dst) &&
               (!time || fTime.startsWith(time) || fTime >= time) && 
               fPrice >= min &&
               fPrice <= max;
    });
    renderPSA(filtered);
}

// --- AURELIYA FILTERING LOGIC ---
function renderAureliya(data) {
    const tbody = document.getElementById('aur_list');
    tbody.innerHTML = '';
    if (data.length === 0) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No properties match filters.</td></tr>'; return; }

    data.forEach(p => {
        const row = document.createElement('tr');
        const title = p.title || p.name || 'Property';
        const location = (p.city || '') + ', ' + (p.country || '');
        const type = p.property_type || p.type || 'Hotel';
        const guests = p.max_guests || p.guests || 2;
        const price = p.price_per_night || p.price || 0;
        const safeId = p.id || p._id;

        row.innerHTML = `<td><strong>${title}</strong></td><td>${location}</td><td>${type}</td><td>${guests}</td><td class="price-tag">₱${Number(price).toLocaleString()}</td>`;
        row.onclick = () => {
            document.querySelectorAll('#aur_list tr').forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');
            document.getElementById('aur_prop_id').value = safeId;
            document.getElementById('aur_base_price').value = price;
            document.getElementById('aur_max_guests_limit').value = guests;
            document.getElementById('aur_selected_prop').innerText = title;
            document.getElementById('aur_guest_hint').innerText = `Max guests: ${guests}`;
            calcPrice();
        };
        tbody.appendChild(row);
    });
}

function filterAureliya() {
    const loc = document.getElementById('aur_f_loc').value.toLowerCase();
    const type = document.getElementById('aur_f_type').value.toLowerCase();
    const guest = parseInt(document.getElementById('aur_f_guest').value) || 0;
    const min = parseFloat(document.getElementById('aur_f_min').value) || 0;
    const max = parseFloat(document.getElementById('aur_f_max').value) || 999999;

    const filtered = allProperties.filter(p => {
        const pLoc = ((p.city || '') + ' ' + (p.country || '')).toLowerCase();
        const pType = (p.property_type || p.type || '').toLowerCase();
        const pGuests = parseInt(p.max_guests || p.guests || 0);
        const pPrice = parseFloat(p.price_per_night || p.price || 0);

        return pLoc.includes(loc) &&
               pType.includes(type) &&
               pGuests >= guest &&
               pPrice >= min &&
               pPrice <= max;
    });
    renderAureliya(filtered);
}
        // --- 4. SUBMIT FUNCTIONS ---
        
        // PSA Submit
        async function submitPSA() {
            const flightId = document.getElementById('psa_flight_id').value;
            if(!flightId) { alert("❌ Please select a flight first."); return; }
            
            const payload = {
                flight_id: flightId,
                first_name: document.getElementById('psa_fname').value,
                last_name: document.getElementById('psa_lname').value,
                email: document.getElementById('psa_email').value,
                contact_number: document.getElementById('psa_contact').value,
                gender: document.getElementById('psa_gender').value,
                birthdate: document.getElementById('psa_bdate').value,
                nationality: document.getElementById('psa_nation').value,
                passport_number: document.getElementById('psa_pass_no').value,
                passport_expiry: document.getElementById('psa_pass_exp').value,
                special_assistance: document.getElementById('psa_special').value,
                emergency_contact_name: document.getElementById('psa_em_name').value,
                emergency_contact_number: document.getElementById('psa_em_num').value
            };

            const btn = document.querySelector('.btn-psa'); btn.innerText = "Sending..."; btn.disabled = true;
            try {
                const res = await fetch('http://127.0.0.1:8000/api/psa-book', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
                const data = await res.json();
                if(res.ok) alert("✅ SUCCESS!\nBooking ID: " + data.booking_id + "\nUser ID: " + data.auto_user_id);
                else alert("❌ Error: " + (data.message || "Unknown Error"));
            } catch(e) { alert("❌ Network Error"); }
            btn.innerText = "BOOK FLIGHT"; btn.disabled = false;
        }

        // Aureliya Logic
        function calcPrice() {
            const base = parseFloat(document.getElementById('aur_base_price').value) || 0;
            const start = new Date(document.getElementById('aur_in').value);
            const end = new Date(document.getElementById('aur_out').value);
            if(base && start && end && end > start) {
                const nights = (end - start) / (1000 * 60 * 60 * 24);
                document.getElementById('aur_total_display').innerText = "₱" + (nights * base).toLocaleString();
            } else {
                document.getElementById('aur_total_display').innerText = "₱0.00";
            }
        }

        async function submitAureliya() {
            // 1. Capture Values
            const propId = document.getElementById('aur_prop_id').value;
            const checkIn = document.getElementById('aur_in').value;
            const checkOut = document.getElementById('aur_out').value;
            const guests = parseInt(document.getElementById('aur_guests').value) || 1;
            const maxGuests = parseInt(document.getElementById('aur_max_guests_limit').value) || 99;

            // 2. Validation
            if(!propId) { alert("❌ Please select a property from the list first."); return; }
            if(!checkIn || !checkOut) { alert("❌ Please select check-in and check-out dates."); return; }
            
            // 🔴 GUEST LIMIT CHECK
            if (guests > maxGuests) {
                alert(`❌ Guest Limit Exceeded!\n\nThis property only allows ${maxGuests} guests.\nYou entered: ${guests}`);
                return;
            }

            const payload = {
                property_id: propId,
                check_in: checkIn,
                check_out: checkOut,
                guests: guests
                // No user_id here!
            };

            const btn = document.querySelector('.btn-aur'); 
            btn.innerText = "Booking..."; 
            btn.disabled = true;

            try {
                const res = await fetch('http://127.0.0.1:8000/api/aur-book', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (res.ok) {
                    alert("✅ SUCCESS!\nBooking Confirmed.\n\nUser ID: " + data.user_id + "\nTransaction: " + data.booking_id);
                    // Reset
                    document.getElementById('aur_in').value = "";
                    document.getElementById('aur_out').value = "";
                    document.getElementById('aur_guests').value = "1";
                    document.getElementById('aur_total_display').innerText = "₱0.00";
                } else {
                    alert("❌ Booking Failed:\n" + (data.error || JSON.stringify(data.errors || data)));
                }
            } catch (e) {
                alert("❌ Network Error: " + e.message);
            } finally {
                btn.innerText = "CONFIRM BOOKING";
                btn.disabled = false;
            }
        }

        // SkyRoute Logic
        async function submitSkyRoute() {
            // 1. Capture Values (Using Scoped Selectors)
            // This looks specifically INSIDE the #skyroute div
            const panel = document.getElementById('skyroute');
            
            const type = panel.querySelector('#sky_type').value;
            const pax = panel.querySelector('#sky_pax').value;
            const orgCity = panel.querySelector('#sky_org_city').value;
            const dstCity = panel.querySelector('#sky_dst_city').value;
            
            // 🔴 Use querySelector to find the date/time inputs specifically in this panel
            const dateInput = panel.querySelector('input[type="date"]');
            const timeInput = panel.querySelector('input[type="time"]');
            
            const date = dateInput ? dateInput.value : "";
            const time = timeInput ? timeInput.value : "";

            // 🔴 DEBUGGING ALERT
            // If this pops up empty, we know the HTML is still missing the input values
            if (!date || !time) {
                console.log("Debug Date:", date);
                console.log("Debug Time:", time);
                alert(`❌ Debug Error: Inputs detected as empty.\nDate: "${date}"\nTime: "${time}"`);
                return;
            }

            // 2. Validation
            if(!orgCity || !dstCity) { alert("❌ Please select Origin and Destination."); return; }
            if(orgCity === dstCity) { alert("❌ Origin and Destination cannot be the same."); return; }

            // 3. Prepare Payload
            const payload = {
                vehicle_type: type,
                passengers: pax,
                org_city: orgCity,
                dst_city: dstCity,
                date: date,
                time: time
            };

            const btn = document.querySelector('.btn-sky'); 
            btn.innerText = "Calculating..."; 
            btn.disabled = true;

            try {
                const res = await fetch('http://127.0.0.1:8000/api/sky-book', {
                    method: 'POST', 
                    headers: {'Content-Type':'application/json', 'Accept':'application/json'}, 
                    body: JSON.stringify(payload)
                });
                
                const data = await res.json();

                if(res.ok) {
                    alert(`✅ BOOKED!\n\nUser ID: ${data.user_id}\nVehicle: ${data.vehicle} (${data.plate_number})\nDate: ${date} ${time}\nDistance: ${data.distance}\nPrice: ₱${data.price}`);
                } else {
                    alert("❌ Error: " + (data.message || data.error));
                }
            } catch(e) { 
                alert("❌ Network Error: " + e.message); 
            } finally {
                btn.innerText = "CALCULATE & BOOK RIDE";
                btn.disabled = false;
            }
        } async function submitSkyRoute() {
            // Find inputs inside the panel
            const panel = document.getElementById('skyroute');
            const type = panel.querySelector('#sky_type').value;
            const pax = panel.querySelector('#sky_pax').value;
            
            // Note: We don't need to send Country/Division to backend, just City Names
            const orgCity = panel.querySelector('#sky_org_city').value;
            const dstCity = panel.querySelector('#sky_dst_city').value;
            
            const dateInput = panel.querySelector('input[type="date"]');
            const timeInput = panel.querySelector('input[type="time"]');
            const date = dateInput ? dateInput.value : "";
            const time = timeInput ? timeInput.value : "";

            // Validation
            if(!orgCity || !dstCity) { alert("❌ Please select Origin and Destination Cities."); return; }
            if(orgCity === dstCity) { alert("❌ Origin and Destination cannot be the same."); return; }
            if(!date || !time) { alert("❌ Please select Date and Time."); return; }

            const payload = {
                vehicle_type: type,
                passengers: pax,
                org_city: orgCity,
                dst_city: dstCity,
                date: date,
                time: time
            };

            const btn = document.querySelector('.btn-sky'); 
            btn.innerText = "Calculating..."; 
            btn.disabled = true;

            try {
                const res = await fetch('http://127.0.0.1:8000/api/sky-book', {
                    method: 'POST', 
                    headers: {'Content-Type':'application/json'}, 
                    body: JSON.stringify(payload)
                });
                
                const data = await res.json();

                if(res.ok) {
                    alert(`✅ BOOKED!\n\nUser ID: ${data.user_id}\nVehicle: ${data.vehicle} (${data.plate_number})\nDate: ${date} ${time}\nDistance: ${data.distance}\nPrice: ₱${data.price}`);
                } else {
                    alert("❌ Error: " + (data.message || data.error));
                }
            } catch(e) { 
                alert("❌ Network Error: " + e.message); 
            } finally {
                btn.innerText = "CONFIRM BOOKING";
                btn.disabled = false;
            }
        }

        // --- TRUTRAVEL LOGIC ---
       // --- TRUTRAVEL LOGIC ---
        let allPackages = [];

        async function loadPackages() {
    try {
        const res = await fetch('http://127.0.0.1:8000/api/tru-packages');
        let data = await res.json();
        
        // 🔴 SORT BY PRICE (Low to High)
        data.sort((a, b) => parseFloat(a.final_price) - parseFloat(b.final_price));

        allPackages = data; 
        
        if (allPackages.length > 0) {
            renderTruInterface();
        } else {
            document.getElementById('tru_grid_container').innerHTML = "No packages found.";
        }
    } catch(e) { console.error("Error loading packages:", e); }
}

        function renderTruInterface() {
            const grid = document.getElementById('tru_grid_container');
            const list = document.getElementById('tru_list');
            grid.innerHTML = '';
            list.innerHTML = '';

            // 1. FEATURED GRID (First 5 Packages)
            const featured = allPackages.slice(0, 5); 
            featured.forEach((pkg, index) => {
                const card = document.createElement('div');
                card.style.cssText = "background:white; border:1px solid #ddd; border-radius:8px; padding:15px; cursor:pointer; transition:0.2s; box-shadow:0 2px 5px rgba(0,0,0,0.05); position:relative; overflow:hidden;";
                
                // Hover Effect
                card.onmouseover = () => { card.style.transform = "translateY(-3px)"; card.style.borderColor = "#04498D"; };
                card.onmouseout = () => { card.style.transform = "translateY(0)"; card.style.borderColor = "#ddd"; };
                
                // Calculate Discount Percentage
                // If DB has 0.15, we multiply by 100 to get 15
                let discVal = parseFloat(pkg.discount || 0);
                if(discVal > 0 && discVal <= 1) discVal = discVal * 100;
                const discPercent = Math.round(discVal);

                card.innerHTML = `
                    <div style="height:5px; background:#04498D; border-radius:5px 5px 0 0; margin:-15px -15px 10px -15px;"></div>
                    
                    ${discPercent > 0 ? `<div style="position:absolute; top:10px; right:10px; background:#e74c3c; color:white; font-size:10px; font-weight:bold; padding:2px 6px; border-radius:3px;">${discPercent}% OFF</div>` : ''}
                    
                    <strong style="color:#2c3e50; font-size:14px; display:block; margin-bottom:5px;">${pkg.name}</strong>
                    
                    <div style="font-size:12px; color:#666;">
                        <i class="fas fa-moon"></i> ${pkg.nights} Nights
                    </div>

                    <div style="margin-top:10px;">
                        <div style="font-size:16px; font-weight:800; color:#2c3e50;">₱${Number(pkg.final_price).toLocaleString()}</div>
                        ${discPercent > 0 ? `<div style="font-size:11px; text-decoration:line-through; color:#999;">₱${Number(pkg.base_price).toLocaleString()}</div>` : ''}
                    </div>
                `;
                card.onclick = () => openTruBooking(index); 
                grid.appendChild(card);
            });

            // 2. SIDE LIST (All Packages)
            allPackages.forEach((pkg, index) => {
                const item = document.createElement('div');
                item.style.cssText = "background:white; padding:10px; border-radius:6px; cursor:pointer; border:1px solid #ddd; font-size:13px; display:flex; justify-content:space-between; align-items:center;";
                item.innerHTML = `
                    <div>
                        <strong>${pkg.name}</strong>
                        <div style="font-size:11px; color:#888;">${pkg.package_type}</div>
                    </div>
                    <div style="font-weight:bold; color:#2c3e50;">₱${Number(pkg.final_price).toLocaleString()}</div>
                `;
                item.onclick = () => openTruBooking(index);
                list.appendChild(item);
            });
        }

        function openTruBooking(index) {
            const p = allPackages[index];
            
            // 1. Show Booking View
            document.getElementById('tru_featured_grid').style.display = 'none';
            document.getElementById('tru_booking_view').style.display = 'block';

            // 2. Fill Data
            document.getElementById('tru_pkg_id').value = p._id || p.id;
            document.getElementById('tru_type').innerText = p.package_type;
            document.getElementById('tru_name').innerText = p.name;
            document.getElementById('tru_desc').innerText = p.description;
            document.getElementById('tru_nights').innerText = p.nights;
            
            // 3. Price Logic
            const base = parseFloat(p.base_price || 0);
            const final = parseFloat(p.final_price || 0);
            
            // 🔴 FIX: Look for 'discount_rate' first, then 'discount'
            let rawDisc = parseFloat(p.discount_rate || p.discount || 0);
            let discPercent = 0;

            if (rawDisc > 0) {
                // Convert decimal (0.15) to percent (15)
                if (rawDisc <= 1) {
                    discPercent = Math.round(rawDisc * 100);
                } else {
                    discPercent = Math.round(rawDisc);
                }
            }

            // Update Final Price
            document.getElementById('tru_final').innerText = '₱' + final.toLocaleString();
            
            // 4. Force Original Price & Discount Display
            const detailBox = document.getElementById('tru_price_details');
            const baseText = document.getElementById('tru_base');
            const badge = document.getElementById('tru_disc');

            if (detailBox) {
                detailBox.style.display = 'block'; // Ensure container is visible
                
                if (discPercent > 0) {
                    // SHOW EVERYTHING
                    baseText.style.display = 'inline';
                    baseText.innerText = '₱' + base.toLocaleString();
                    
                    badge.style.display = 'inline-block';
                    badge.innerText = discPercent + "% OFF";
                } else {
                    // HIDE IF 0% (Optional: You can remove this else block if you ALWAYS want to see it)
                    baseText.style.display = 'none';
                    badge.style.display = 'none';
                }
            }
        }

        function showTruGrid() {
            document.getElementById('tru_booking_view').style.display = 'none';
            document.getElementById('tru_featured_grid').style.display = 'block';
        }

        async function bookTruTravel() {
    const pkgId = document.getElementById('tru_pkg_id').value;
    const date = document.getElementById('tru_date').value;
    const guests = document.getElementById('tru_guests').value || 2;
    // 1. Get Time Value
    const time = document.getElementById('tru_time').value || "08:00"; 

    if(!date) { alert("❌ Please select a Travel Date."); return; }
    if(!time) { alert("❌ Please select a Pickup Time."); return; }

    const btn = document.querySelector('#tru_booking_view button.submit-btn');
    const originalText = btn.innerText;
    btn.innerText = "Processing..."; btn.disabled = true;

    try {
        const res = await fetch('http://127.0.0.1:8000/api/tru-book', {
            method: 'POST',
            headers: {'Content-Type':'application/json', 'Accept': 'application/json'},
            // 2. Add travel_time to payload
            body: JSON.stringify({ 
                package_id: pkgId, 
                travel_date: date,
                travel_time: time, // <--- Sent here
                guests: guests 
            })
        });

        const contentType = res.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            const data = await res.json();
            if(res.ok) {
                alert(`✅ PACKAGE BOOKED!\n\nUser ID: ${data.user_id}\nPickup: ${time}\nVehicle: ${data.vehicle_assigned}\n\nSuccess! All services reserved.`);
                showTruGrid();
            } else {
                alert("❌ Server Error:\n" + (data.message || data.error || JSON.stringify(data)));
            }
        } else {
            const text = await res.text();
            console.error("Critical Server Error:", text);
            alert("❌ CRITICAL ERROR (Check Console F12): The server crashed.");
        }

    } catch(e) { 
        alert("❌ Connection Error: " + e.message); 
    } finally {
        btn.innerText = originalText; btn.disabled = false;
    }
}

// --- AEROPAY LOGIC ---

async function loadTransactions() {
    const tbody = document.getElementById('apay_list');
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">🔄 Syncing Ledger...</td></tr>';

    try {
        const res = await fetch('http://127.0.0.1:8000/api/aeropay/transactions');
        const data = await res.json();

        tbody.innerHTML = '';
        if(data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">No transactions found.</td></tr>';
            return;
        }

        // Sort: Newest first
        data.sort((a, b) => new Date(b.created_at || b.updated_at) - new Date(a.created_at || a.updated_at));

        data.forEach(tx => {
            const row = document.createElement('tr');
            row.style.background = "white";
            row.style.boxShadow = "0 2px 5px rgba(0,0,0,0.02)";
            
            // Determine Status Color
            let bg = '#eee'; let color = '#555';
            const st = tx.status ? tx.status.toLowerCase() : 'pending';
            
            if(st === 'paid' || st === 'confirmed') { bg = '#d4edda'; color = '#155724'; } // Green
            else if(st === 'pending') { bg = '#fff3cd'; color = '#856404'; } // Yellow
            else if(st === 'failed' || st === 'cancelled') { bg = '#f8d7da'; color = '#721c24'; } // Red

            row.innerHTML = `
                <td style="padding:15px; font-family:monospace; font-weight:bold; color:#04498D; border-left:4px solid ${bg === '#d4edda' ? '#28a745' : '#ccc'}; border-radius:4px 0 0 4px;">${tx.transaction_code}</td>
                <td style="padding:15px;"><span style="background:#eef; padding:4px 10px; border-radius:15px; font-weight:bold; font-size:11px; color:#04498D;">${tx.partner}</span></td>
                <td style="padding:15px; font-weight:800; font-size:15px; color:#2c3e50;">₱${Number(tx.amount).toLocaleString()}</td>
                <td style="padding:15px; font-size:11px; color:#888; font-family:monospace;">${tx.partner_reference_id}</td>
                <td style="padding:15px; font-size:12px; color:#666;">${new Date(tx.created_at).toLocaleString()}</td>
                <td style="padding:15px; border-radius:0 4px 4px 0;">
                    <select onchange="updateTxStatus('${tx.transaction_code}', this)" 
                            style="width:100%; padding:8px; border-radius:6px; font-weight:bold; cursor:pointer; border:1px solid ${bg}; background:${bg}; color:${color}; outline:none;">
                        <option value="pending" ${st === 'pending' ? 'selected' : ''}>⏳ Pending</option>
                        <option value="paid" ${st === 'paid' ? 'selected' : ''}>✅ Paid</option>
                        <option value="confirmed" ${st === 'confirmed' ? 'selected' : ''}>🚀 Confirmed</option>
                        <option value="failed" ${st === 'failed' ? 'selected' : ''}>❌ Failed</option>
                        <option value="cancelled" ${st === 'cancelled' ? 'selected' : ''}>🚫 Cancelled</option>
                    </select>
                </td>
            `;
            tbody.appendChild(row);
        });

    } catch(e) {
        console.error(e);
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:red; padding:20px;">Connection Error: ${e.message}</td></tr>`;
    }
}

async function updateTxStatus(code, selectElem) {
    const newStatus = selectElem.value;
    
    // UI Feedback
    selectElem.disabled = true;
    selectElem.style.opacity = '0.5';
    
    try {
        const res = await fetch(`http://127.0.0.1:8000/api/aeropay/transactions/${code}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        });

        const data = await res.json();

        if(res.ok) {
            // Update the color dynamically without reloading
            let bg = '#eee'; let color = '#555';
            if(newStatus === 'paid' || newStatus === 'confirmed') { bg = '#d4edda'; color = '#155724'; }
            else if(newStatus === 'pending') { bg = '#fff3cd'; color = '#856404'; }
            else { bg = '#f8d7da'; color = '#721c24'; }
            
            selectElem.style.background = bg;
            selectElem.style.color = color;
            selectElem.style.borderColor = bg;
            
            // Also update the left border of the row for visual consistency
            selectElem.closest('tr').querySelector('td').style.borderLeftColor = (newStatus === 'paid' || newStatus === 'confirmed') ? '#28a745' : '#ccc';
        } else {
            alert("❌ Update Failed: " + (data.message || "Unknown error"));
        }
    } catch(e) {
        alert("❌ Network Error: " + e.message);
    } finally {
        selectElem.disabled = false;
        selectElem.style.opacity = '1';
    }
}
    </script>
</body>
</html>