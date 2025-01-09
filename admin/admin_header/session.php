<?php
session_start();

// Check if user is not logged in OR is not an admin
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN'){
    header('Location: ../signin.php');
    exit();
}
?>