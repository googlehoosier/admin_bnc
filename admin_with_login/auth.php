<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate credentials
    if ($username === $valid_username && verify_password($password, $password_hash)) {
        // Regenerate session ID to prevent fixation attacks
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        
        // ✅ FIXED: Redirect to booking_form.php instead of dashboard.php
        header("Location: booking_form.php");
        exit;
    } else {
        // Invalid credentials
        header("Location: index.php?error=1");
        exit;
    }
} else {
    // If not POST request, redirect to login
    header("Location: index.php");
    exit;
}
?>