<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Algom\Academia1\controllers\AuthController;
use Algom\Academia1\helpers\UrlHelper;

// Si ya está autenticado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    UrlHelper::redirect('dashboard');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $authController = new AuthController();
    $result = $authController->login($username, $password);
    
    if ($result['success']) {
        if ($result['first_login']) {
            UrlHelper::redirect('change-password');
        } else {
            UrlHelper::redirect('dashboard');
        }
        exit();
    } else {
        $error = $result['message'];
    }
}

$pageTitle = 'Iniciar Sesión';
$content = __DIR__ . '/templates/login-content.php';
require_once __DIR__ . '/templates/layout.php';
