<?php
require_once 'includes/session.php';  // Session management

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Include the rest of your index.php content, but with session-aware modifications
include 'index.php'; 