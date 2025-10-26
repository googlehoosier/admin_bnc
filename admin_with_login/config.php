<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// ===== LOGIN CREDENTIALS =====
$valid_username = 'bingencelebrations';
$password_hash = '$2y$10$6UaSVhAOvgg2uvbUyjHDw.GMdFCOQc/hKza9uG1FeEzIfUWVaWTH2';

function verify_password($input_password, $stored_hash) {
    return password_verify($input_password, $stored_hash);
}

// ===== SUPABASE CONFIGURATION =====
$SUPABASE_URL = "https://fpgwsouvcsrmwxlbrpez.supabase.co";
$SUPABASE_SERVICE_ROLE_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZwZ3dzb3V2Y3NybXd4bGJycGV6Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc0ODExODM0NiwiZXhwIjoyMDYzNjk0MzQ2fQ.BMFBVToQNjo9r91WHXPhe8QJdmgWtIi_sc5uGP29k5k";
$BOOKINGS_ENDPOINT = $SUPABASE_URL . "/rest/v1/bookings";
$SLOTS_ENDPOINT = $SUPABASE_URL . "/rest/v1/slots";
$CURL_TIMEOUT = 30;

// ===== SESSION PROTECTION =====
function require_login() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: index.php?error=2");
        exit;
    }
}
?>