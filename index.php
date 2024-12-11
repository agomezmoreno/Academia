<?php
require_once __DIR__ . '/vendor/autoload.php';

session_start();

// Get the request URI and remove any query string
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = str_replace('/academia1/', '', $request);

// If not logged in and not trying to access login page, redirect to login
if (!isset($_SESSION['user_id']) && $request !== 'login') {
    header('Location: /academia1/login');
    exit();
}

// If logged in and trying to access login page, redirect to dashboard
if (isset($_SESSION['user_id']) && $request === 'login') {
    header('Location: /academia1/dashboard');
    exit();
}

// Route the request
switch ($request) {
    case '':
        header('Location: /academia1/dashboard');
        exit();
    case 'dashboard':
        require __DIR__ . '/public/dashboard.php';
        break;
    case 'login':
        require __DIR__ . '/public/login.php';
        break;
    case 'logout':
        require __DIR__ . '/public/logout.php';
        break;
    case 'change-password':
        require __DIR__ . '/public/change-password.php';
        break;
    case 'users':
        // Check if user is gestor
        if ($_SESSION['role'] !== 'gestor') {
            header('HTTP/1.0 403 Forbidden');
            echo 'Acceso denegado';
            break;
        }
        require __DIR__ . '/public/users.php';
        break;
    case 'subjects':
        require __DIR__ . '/public/subjects.php';
        break;
    case 'grades':
        require __DIR__ . '/public/grades.php';
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        echo 'Page not found';
        break;
}
