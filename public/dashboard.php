<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Algom\Academia1\helpers\UrlHelper;

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    UrlHelper::redirect('login');
    exit();
}

$pageTitle = 'Dashboard';
$content = __DIR__ . '/templates/dashboard-content.php';

require_once __DIR__ . '/templates/layout.php';
