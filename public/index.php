<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// If not logged in, redirect to login page
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
    exit();
}

// If first login, force password change
if (isset($_SESSION['first_login']) && $_SESSION['first_login'] && basename($_SERVER['PHP_SELF']) !== 'change-password.php') {
    header('Location: change-password.php');
    exit();
}
