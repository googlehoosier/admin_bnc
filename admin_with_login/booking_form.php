<?php
require_once 'config.php';
require_login(); // Protect this page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Booking Entry</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* keep your original styles untouched */
        main-content { padding: 20px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input[type="text"], input[type="email"], input[type="number"], input[type="date"], select {
            width: 300px;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .addons-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            width: 650px;
            margin-top: 5px;
            border: 1px solid #eee;
            padding: 10px;
        }
        .header-logo {
            height: 48px; width: 48px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .header-logo img { object-fit: contain; max-width: 100%; max-height: 100%; }
        .header-title { font-size: 1.25rem; font-weight: bold; color: white; letter-spacing: 0.05em; }
        .nav-links a { color: white; font-weight: 500; text-decoration: none; margin-left: 16px; transition: opacity 0.2s; }
        .nav-links a:hover { opacity: 0.8; }
        .logout-btn { 
            background: #dc3545; 
            color: white; 
            border: none; 
            padding: 8px 16px; 
            border-radius: 5px; 
            cursor: pointer; 
            margin-left: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .logout-btn:hover { background: #c82333; }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header with links -->
    <header class="bg-blue-500 shadow-lg sticky top-0 z-50 py-2 px-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="header-logo">
                    <img src="BINGEN.png" alt="BINGE'N Logo" style="height:100%;width:100%;object-fit:contain;border-radius:50%;">
                </div>
                <h1 class="header-title">BINGE'N CELEBRATIONS</h1>
            </div>
            <div class="nav-links">
                <a href="booking_form.php">Booking Form</a>
                <a href="view_bookings.php">View Bookings</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div style="padding: 20px;">
        <h2>Admin Panel - Add Booking</h2>

        <form action="submit_booking.php" method="POST" class="space-y-3">

            <label for="venue_id_select">Venue:</label><br>
            <select id="venue_id_select" name="venue_id" required>
                <option value="">-- Select Venue --</option>
                <option value="399e2ade-5d6d-4535-81a6-93ae43a637a5">Aura (399e2ade-...)</option>
                <option value="18c00f9e-21d0-4d77-ad5a-d831aa4ede07">Lunar (18c00f9e-...)</option>
                <option value="fdb9e954-7810-4ab5-9b7d-1483ec53669a">Minimax (fdb9e954-...)</option>
                <option value="771c4da3-851e-431c-a490-8bb6bf93aa77">Couple (771c4da3-...)</option>
            </select><br><br>

            <label for="slot_id_select">Slot:</label><br>
            <select id="slot_id_select" name="slot_id" required disabled>
                <option value="">-- Select Slot --</option>
            </select><br><br>

            <label>Booking Date:</label><br>
            <input type="date" name="booking_date" required><br><br>

            <label>Booking Name:</label><br>
            <input type="text" name="booking_name" required><br><br>

            <label for="persons_select">Number of Persons:</label><br>
            <select id="persons_select" name="persons" required disabled>
                <option value="">-- Select Guest Count --</option>
            </select><br><br>

            <label>WhatsApp Number:</label><br>
            <input type="text" name="whatsapp" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>

            <label>Decoration:</label>
            <input type="checkbox" name="decoration" value="true"><br><br>

            <!-- NEW: Advance Payment AFTER decoration -->
            <label>Advance Payment:</label>
            <input type="checkbox" name="advance_paid" value="true"><br><br>

            <label for="event_type_select">Event Type:</label><br>
            <select id="event_type_select" name="event_type" required>
                <option value="">-- Select Event Type --</option>
                <option value="Birthday">Birthday</option>
                <option value="Anniversary">Anniversary</option>
                <option value="Romantic Date">Romantic Date</option>
                <option value="Marriage Proposal">Marriage Proposal</option>
                <option value="Groom to Be">Groom to Be</option>
                <option value="Bride to Be">Bride to Be</option>
                <option value="Baby Shower">Baby Shower</option>
                <option value="Private Party">Private Party</option>
                <option value="none">none</option>
            </select><br><br>

            <label for="cake_select">Cake Selection:</label><br>
            <div style="display: flex; gap: 10px;">
                <select id="cake_select" name="cake_name">
                    <option value="">-- Select Cake --</option>
                    <option value="Vanilla">Vanilla</option>
                    <option value="Strawberry">Strawberry</option>
                    <option value="Butterscotch">Butterscotch</option>
                    <option value="Pineapple">Pineapple</option>
                    <option value="Blueberry">Blueberry</option>
                    <option value="Pistamalai">Pistamalai</option>
                    <option value="Choco Truffle">Choco Truffle</option>
                    <option value="Choco Kitkat">Choco Kitkat</option>
                    <option value="White Forest">White Forest</option>
                    <option value="Black Forest">Black Forest</option>
                    <option value="none">none</option>
                </select>
            </div><br>

            <label>Selected Addons:</label><br>
            <div class="addons-grid">
                <label><input type="checkbox" name="selected_addons[]" value="LED HBD|119"> LED HBD (₹119)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Fog Entry|700"> Fog Entry (₹700)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Fog Entry + Cold Fire (2)|1400"> Fog Entry + Cold Fire (2) (₹1400)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Photo Props|189"> Photo Props (₹189)</label>
                <label><input type="checkbox" name="selected_addons[]" value="LED Name Letters|299"> LED Name Letters (₹299)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Table Décor|299"> Table Décor (₹299)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Candles|199"> Candles (₹199)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Photoshoot (30 mins)|600"> Photoshoot (30 mins) (₹600)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Photoshoot (60 mins)|1200"> Photoshoot (60 mins) (₹1200)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Sash & Crown|199"> Sash & Crown (₹199)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Cold Fire|700"> Cold Fire (₹700)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Candle Faith|199"> Candle Faith (₹199)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Fog in Room|499"> Fog in Room (₹499)</label>
                <label><input type="checkbox" name="selected_addons[]" value="LOVE|99"> LOVE (₹99)</label>
                <label><input type="checkbox" name="selected_addons[]" value="LED Numbers|99"> LED Numbers (₹99)</label>
                <label><input type="checkbox" name="selected_addons[]" value="Bubble Entry|200"> Bubble Entry (₹200)</label>
                <label><input type="checkbox" name="selected_addons[]" value="none"> none</label>
            </div><br>

            <label>Total Amount:</label><br>
            <input type="number" name="total_amount" id="total_amount"><br><br>

            <label>Extra Person Charges (₹):</label><br>
            <input type="number" name="extra_person_charges" id="extra_person_charges" value="0" readonly><br><br>

            <!-- Submit button (styled) -->
            <button type="submit" class="bg-blue-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-600">
                Submit Booking
            </button>
        </form>
    </div>

    <script>
        // --- VENUE DATA ---
        const venueSlots = {
            '399e2ade-5d6d-4535-81a6-93ae43a637a5': [ // Aura
                { time: '09:30:00 - 12:30:00', id: 'dcc7cb82-7915-4dff-9a66-e0cd33e8ca92' },
                { time: '13:00:00 - 16:00:00', id: 'c513c0ec-5b53-4c24-bb53-9c1a14b7197f' },
                { time: '16:30:00 - 18:00:00', id: '7912de97-0587-4e9c-b3a2-82e7379fac9e' },
                { time: '18:30:00 - 21:30:00', id: '07682981-ae4c-47fc-8435-24b064ff2d0c' },
                { time: '22:00:00 - 01:00:00', id: '106a4eb2-713f-4074-96c0-b6c827009c42' }
            ],
            '18c00f9e-21d0-4d77-ad5a-d831aa4ede07': [ // Lunar
                { time: '09:30:00 - 12:30:00', id: '93382ee1-819b-4459-aecb-e4ec7af8a7ed' },
                { time: '13:00:00 - 16:00:00', id: 'd0849d08-3b41-4d06-afb9-2fa1747c5235' },
                { time: '16:30:00 - 18:00:00', id: '8f04a740-4df3-47db-bc88-423486257014' },
                { time: '18:30:00 - 21:30:00', id: 'bf341419-5f73-40fd-b2f0-6d93e195e1af' },
                { time: '22:00:00 - 01:00:00', id: '0d183089-a38d-4de1-9208-f3f3b46861d3' }
            ],
            'fdb9e954-7810-4ab5-9b7d-1483ec53669a': [ // Minimax
                { time: '10:00:00 - 13:00:00', id: 'b2c068ce-16be-4b4c-a888-a283cb17bba1' },
                { time: '13:30:00 - 16:30:00', id: 'c76f7f9d-cf0e-41c3-84d0-823c8db67289' },
                { time: '17:00:00 - 18:30:00', id: 'f549927c-787c-4116-a82b-a11faf4519fe' },
                { time: '19:00:00 - 22:00:00', id: '5dd9eddb-4ea7-4956-b069-42c0c2c532bf' },
                { time: '22:30:00 - 01:00:00', id: '56b5ff0b-e8ae-4e92-94db-0fc3a2bc1333' }
            ],
            '771c4da3-851e-431c-a490-8bb6bf93aa77': [ // Couple
                { time: '09:00:00 - 12:00:00', id: 'b9f73b3a-c40a-4fd9-b56d-055dbe8a3fa3' },
                { time: '12:30:00 - 15:30:00', id: '2fe129f7-76ba-41dc-8a16-c43d9bed8923' },
                { time: '16:00:00 - 17:30:00', id: 'dfac91a6-99d5-418b-a70e-7a75c7b207a2' },
                { time: '18:00:00 - 21:00:00', id: '2f3a9da9-a778-43cb-9505-69c9fea101be' },
                { time: '21:30:00 - 00:30:00', id: '0edf9c1f-4c90-4ce4-a06e-a0b06f88759e' }
            ]
        };

        const venueMaxGuests = {
            '399e2ade-5d6d-4535-81a6-93ae43a637a5': 12,
            '18c00f9e-21d0-4d77-ad5a-d831aa4ede07': 8,
            'fdb9e954-7810-4ab5-9b7d-1483ec53669a': 20,
            '771c4da3-851e-431c-a490-8bb6bf93aa77': 2
        };

        const venueCharges = {
            '399e2ade-5d6d-4535-81a6-93ae43a637a5': { included: 6, charge: 250 },
            '18c00f9e-21d0-4d77-ad5a-d831aa4ede07': { included: 4, charge: 250 },
            'fdb9e954-7810-4ab5-9b7d-1483ec53669a': { included: 8, charge: 250 },
            '771c4da3-851e-431c-a490-8bb6bf93aa77': { included: 2, charge: 0 }
        };

        // --- DOM REFS ---
        const venueSelect = document.getElementById('venue_id_select');
        const slotSelect = document.getElementById('slot_id_select');
        const personsSelect = document.getElementById('persons_select');
        const extraChargesInput = document.getElementById('extra_person_charges');

        function calculateExtraCharges() {
            const selectedVenueId = venueSelect.value;
            const selectedPersons = parseInt(personsSelect.value);

            if (!selectedVenueId || isNaN(selectedPersons) || selectedPersons <= 0) {
                extraChargesInput.value = 0;
                return;
            }

            const chargeData = venueCharges[selectedVenueId];
            if (!chargeData) {
                extraChargesInput.value = 0;
                return;
            }

            const includedGuests = chargeData.included;
            const extraChargePerPerson = chargeData.charge;

            let extraPersons = 0;
            if (selectedPersons > includedGuests) {
                extraPersons = selectedPersons - includedGuests;
            }

            const totalExtraCharge = extraPersons * extraChargePerPerson;
            extraChargesInput.value = totalExtraCharge;
        }

        function handleVenueChange() {
            const selectedVenueId = this.value;

            // Update slots
            slotSelect.innerHTML = '<option value="">-- Select Slot --</option>';
            if (selectedVenueId && venueSlots[selectedVenueId]) {
                venueSlots[selectedVenueId].forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.id;
                    option.textContent = `${slot.time} (${slot.id})`;
                    slotSelect.appendChild(option);
                });
                slotSelect.disabled = false;
            } else {
                slotSelect.disabled = true;
            }

            // Update persons options
            personsSelect.innerHTML = '<option value="">-- Select Guest Count --</option>';
            const maxGuests = venueMaxGuests[selectedVenueId];
            if (maxGuests) {
                for (let i = 1; i <= maxGuests; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    personsSelect.appendChild(option);
                }
                personsSelect.disabled = false;
            } else {
                personsSelect.disabled = true;
            }

            calculateExtraCharges();
        }

        // event listeners remain the same
        venueSelect.addEventListener('change', handleVenueChange);
        personsSelect.addEventListener('change', calculateExtraCharges);
    </script>
</body>
</html>