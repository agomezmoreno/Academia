<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Algom\Academia1\controllers\AuthController;
use Algom\Academia1\helpers\UrlHelper;

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    UrlHelper::redirect('login');
    exit();
}

// Inicializar variables de mensaje
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? null;
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($newPassword) || empty($confirmPassword)) {
        $error = 'Por favor, complete todos los campos';
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Las contraseñas no coinciden";
    } else {
        $authController = new AuthController();
        $result = $authController->changePassword($_SESSION['user_id'], $currentPassword, $newPassword);
        
        if ($result['success']) {
            $_SESSION['success_message'] = "Contraseña actualizada correctamente";
            UrlHelper::redirect('dashboard');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Cambiar Contraseña';
$content = __DIR__ . '/templates/change-password-content.php';

require_once __DIR__ . '/templates/layout.php';
