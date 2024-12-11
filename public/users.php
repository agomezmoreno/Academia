<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Algom\Academia1\models\User;
use Algom\Academia1\controllers\AuthController;
use Algom\Academia1\repositories\UserRepository;
use Algom\Academia1\helpers\UrlHelper;

// Verificar si hay una sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado y es gestor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gestor') {
    UrlHelper::redirect('login');
    exit();
}

$userRepository = new UserRepository();
$error = '';
$success = '';

function generateUsername($name, $surname1, $surname2, $dni) {
    // Primera letra del nombre
    $firstLetter = mb_substr(trim($name), 0, 1, 'UTF-8');
    
    // Tres primeras letras de cada apellido
    $firstSurname = mb_substr(trim($surname1), 0, 3, 'UTF-8');
    $secondSurname = mb_substr(trim($surname2), 0, 3, 'UTF-8');
    
    // Tres últimos dígitos del DNI (excluyendo la letra)
    $dniNumbers = preg_replace('/[^0-9]/', '', $dni);
    $lastThreeDigits = substr($dniNumbers, -3);
    
    // Combinar todo y convertir a minúsculas
    return mb_strtolower($firstLetter . $firstSurname . $secondSurname . $lastThreeDigits, 'UTF-8');
}

function generateTemporaryPassword() {
    // Generar una contraseña de 8 caracteres
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'create':
                // Generar usuario y contraseña
                $generatedUsername = generateUsername(
                    $_POST['name'],
                    $_POST['surname1'],
                    $_POST['surname2'] ?? '',
                    $_POST['dni']
                );
                $generatedPassword = generateTemporaryPassword();

                // Crear nuevo usuario
                $user = new User([
                    'name' => $_POST['name'],
                    'surname1' => $_POST['surname1'],
                    'surname2' => $_POST['surname2'] ?? '',
                    'dni' => $_POST['dni'],
                    'email' => $_POST['email'],
                    'username' => $generatedUsername,
                    'password' => $generatedPassword,
                    'role' => $_POST['role'],
                    'first_login' => true
                ]);

                if ($userRepository->create($user)) {
                    $success = "Usuario creado exitosamente\n";
                    $success .= "Usuario generado: " . $generatedUsername . "\n";
                    $success .= "Contraseña temporal: " . $generatedPassword;
                } else {
                    throw new \Exception('Error al crear el usuario');
                }
                break;

            case 'edit':
                // Validar ID
                if (!isset($_POST['id'])) {
                    throw new \Exception('ID de usuario no proporcionado');
                }

                // Obtener usuario existente
                $user = $userRepository->findById($_POST['id']);
                if (!$user) {
                    throw new \Exception('Usuario no encontrado');
                }

                // Actualizar datos
                $user->setName($_POST['name']);
                $user->setSurname1($_POST['surname1']);
                $user->setSurname2($_POST['surname2'] ?? '');
                $user->setDni($_POST['dni']);
                $user->setEmail($_POST['email']);
                $user->setUsername($_POST['username']);
                $user->setRole($_POST['role']);

                if ($userRepository->update($user)) {
                    $success = 'Usuario actualizado exitosamente';
                } else {
                    throw new \Exception('Error al actualizar el usuario');
                }
                break;

            case 'delete':
                if (!isset($_POST['id'])) {
                    throw new \Exception('ID de usuario no proporcionado');
                }

                if ($userRepository->delete($_POST['id'])) {
                    $success = 'Usuario eliminado exitosamente';
                } else {
                    throw new \Exception('Error al eliminar el usuario');
                }
                break;

            default:
                throw new \Exception('Acción no válida');
        }
    } catch (\Exception $e) {
        error_log("Error en users.php: " . $e->getMessage());
        $error = $e->getMessage();
    }
}

// Obtener lista de usuarios
try {
    $users = $userRepository->findAll();
} catch (\Exception $e) {
    error_log("Error al obtener usuarios: " . $e->getMessage());
    $error = 'Error al cargar la lista de usuarios';
    $users = [];
}

// Definir el título y contenido para el layout
$pageTitle = 'Gestión de Usuarios';
$content = __DIR__ . '/templates/users-content.php';

// Incluir el layout principal
require_once __DIR__ . '/templates/layout.php';
