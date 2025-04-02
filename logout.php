<?php
// Main Logout Page
require_once 'init.php';

// Get User instance and logout
$user = User::getInstance();
$user->logout();

// Redirect to login page
header('Location: login.php');
exit;
?> 