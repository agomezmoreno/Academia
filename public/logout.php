<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Algom\Academia1\controllers\AuthController;

session_start();

$auth = new AuthController();
$result = $auth->logout();

header('Location: login.php');
exit();
