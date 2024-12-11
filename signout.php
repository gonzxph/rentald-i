<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID before destroying to prevent session fixation
session_regenerate_id(true);

// Clear all session data
$_SESSION = array();

// Delete the session cookie with secure settings
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// Clear any other authentication cookies with secure settings
setcookie('remember_me', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

setcookie('user_token', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Destroy the session
session_destroy();

// Security headers
header('Clear-Site-Data: "cache", "cookies", "storage"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Redirect with a success message using a more secure method
$base_url = 'index.php';
$params = array('logout' => 'success');
$redirect_url = $base_url . '?' . http_build_query($params);
header('Location: ' . filter_var($redirect_url, FILTER_SANITIZE_URL));
exit();
?> 