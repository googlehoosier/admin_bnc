<?php
// submit_booking.php
require_once 'config.php';
require_login();
// dev: show errors while testing
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Form not submitted (POST required).");
}

// --- collect + sanitize inputs ---
$venue_id = $_POST['venue_id'] ?? '';
$slot_id = $_POST['slot_id'] ?? '';
$booking_date = $_POST['booking_date'] ?? '';
$booking_name = $_POST['booking_name'] ?? '';
$persons = isset($_POST['persons']) ? (int)$_POST['persons'] : 1;
$whatsapp = $_POST['whatsapp'] ?? '';
$email = $_POST['email'] ?? '';
$decoration = isset($_POST['decoration']) ? true : false;
$advance_paid = isset($_POST['advance_paid']) ? true : false;
$event_type = $_POST['event_type'] ?? '';
$cake_name = $_POST['cake_name'] ?? null;
$cake_type = $_POST['cake_type'] ?? null;
$cake_size = $_POST['cake_size'] ?? null;
$selected_addons = isset($_POST['selected_addons']) ? $_POST['selected_addons'] : []; // array
$total_amount = isset($_POST['total_amount']) && $_POST['total_amount'] !== '' ? (float)$_POST['total_amount'] : null;
$extra_person_charges = isset($_POST['extra_person_charges']) ? (float)$_POST['extra_person_charges'] : 0.0;

// keep addons as comma-separated string (you can change to JSON if your DB column is json)
$selected_addons_str = is_array($selected_addons) ? implode(',', $selected_addons) : ($selected_addons ?: null);

// Minimal validations
$errors = [];
if (!$venue_id) $errors[] = "venue_id is required";
if (!$slot_id) $errors[] = "slot_id is required";
if (!$booking_date) $errors[] = "booking_date is required";
if (!$booking_name) $errors[] = "booking_name is required";
if (!$whatsapp) $errors[] = "whatsapp is required";
if (!$email) $errors[] = "email is required";

if (!empty($errors)) {
    echo "<b>Missing required fields:</b><br>";
    foreach ($errors as $e) echo "- " . htmlspecialchars($e) . "<br>";
    echo "<p><a href='booking_form.html'>Back</a></p>";
    exit;
}

// Build payload. NOTE: do NOT include "id" — let DB generate gen_random_uuid()
$payload = [
    "venue_id" => $venue_id,
    "slot_id" => $slot_id,
    "booking_date" => $booking_date,
    "booking_name" => $booking_name,
    "persons" => $persons,
    "whatsapp" => $whatsapp,
    "email" => $email,
    "decoration" => $decoration,
    "advance_paid" => $advance_paid,
    "event_type" => $event_type,
    "cake_selection" => $cake_name,
    "selected_addons" => $selected_addons_str,
    "payment_id" => ($advance_paid ? "admin_manual_advance" : "admin_manual"),
    "total_amount" => $total_amount,
    "extra_person_charges" => $extra_person_charges
];

// Supabase REST expects an array of rows for insert — send as [payload]
$body = json_encode([$payload]);

// Insert booking
$ch = curl_init($BOOKINGS_ENDPOINT);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: {$SUPABASE_SERVICE_ROLE_KEY}",
    "Authorization: Bearer {$SUPABASE_SERVICE_ROLE_KEY}",
    "Content-Type: application/json",
    "Prefer: return=representation"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err = curl_error($ch);
curl_close($ch);

if ($curl_err) {
    echo "cURL error inserting booking: " . htmlspecialchars($curl_err);
    exit;
}

// Debug: show raw response if not 2xx
if ($httpcode < 200 || $httpcode >= 300) {
    echo "<h3>Failed to insert booking (HTTP {$httpcode})</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    echo "<p><a href='booking_form.html'>Back</a></p>";
    exit;
}

// Parse inserted booking (returned because of Prefer: return=representation)
$inserted = json_decode($response, true);
$inserted_booking = is_array($inserted) && count($inserted) ? $inserted[0] : null;
$inserted_id = $inserted_booking['id'] ?? null;

// If advance_paid, update the slot row to mark as booked/unavailable
if ($advance_paid) {
    // Attempt to set both status and is_booked (match your schema; change keys if needed)
    $slotUpdate = [
        "status" => "booked",
        "is_booked" => true
    ];
    $updateUrl = $SLOTS_ENDPOINT . "?id=eq." . rawurlencode($slot_id);
    $ch2 = curl_init($updateUrl);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        "apikey: {$SUPABASE_SERVICE_ROLE_KEY}",
        "Authorization: Bearer {$SUPABASE_SERVICE_ROLE_KEY}",
        "Content-Type: application/json",
        "Prefer: return=representation"
    ]);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($slotUpdate));
    $updateResp = curl_exec($ch2);
    $updateCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    $updateErr = curl_error($ch2);
    curl_close($ch2);

    if ($updateErr) {
        // update failed — echo but don't rollback booking
        echo "<p>Warning: Slot update cURL error: " . htmlspecialchars($updateErr) . "</p>";
    } elseif ($updateCode < 200 || $updateCode >= 300) {
        echo "<p>Warning: Slot update returned HTTP {$updateCode}</p>";
        echo "<pre>" . htmlspecialchars($updateResp) . "</pre>";
    }
}

// Success — redirect to bookings view or show confirmation
echo "<script>alert('✅ Booking saved successfully." . ($advance_paid ? " Slot frozen (advance paid)." : "") . "'); window.location.href='view_bookings.php';</script>";
exit;
