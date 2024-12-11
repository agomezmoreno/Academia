<?php

namespace Algom\Academia1\controllers;

use Algom\Academia1\models\User;
use Algom\Academia1\repositories\UserRepository;
use Algom\Academia1\helpers\UrlHelper;

class AuthController {
    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function login(string $username, string $password) {
        $user = $this->userRepository->findByUsername($username);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        // Comparación directa de contraseñas
        if ($password !== $user->getPassword()) {
            return ['success' => false, 'message' => 'Contraseña incorrecta'];
        }

        // Store user data in session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role'] = $user->getRole();
        $_SESSION['first_login'] = $user->isFirstLogin();

        return [
            'success' => true,
            'first_login' => $user->isFirstLogin(),
            'redirect' => 'public/' . ($user->isFirstLogin() ? 'change-password' : 'dashboard')
        ];
    }

    public function register(array $userData) {
        if ($userData['role'] !== 'gestor' && !isset($_SESSION['role']) || $_SESSION['role'] !== 'gestor') {
            return ['success' => false, 'message' => 'No tiene permisos para registrar usuarios'];
        }

        $user = new User($userData);
        $user->generateUsername();
        $password = $user->generatePassword();
        $user->setPassword($password); // Contraseña en texto plano
        $user->setFirstLogin(true);

        if ($this->userRepository->create($user)) {
            // Aquí se enviaría el email con la contraseña
            // Por ahora solo mostramos las credenciales
            return [
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'user_credentials' => [
                    'username' => $user->getUsername(),
                    'password' => $password,
                    'email' => $user->getEmail()
                ]
            ];
        }

        return ['success' => false, 'message' => 'Error al crear el usuario'];
    }

    public function changePassword(int $userId, ?string $currentPassword, string $newPassword) {
        try {
            // Obtener usuario
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }

            // En primer inicio de sesión no validamos contraseña actual
            if (!$user->isFirstLogin()) {
                if (!$currentPassword || $currentPassword !== $user->getPassword()) {
                    return ['success' => false, 'message' => 'La contraseña actual es incorrecta'];
                }
            }

            // Validar la nueva contraseña
            if (strlen($newPassword) < 6) {
                return [
                    'success' => false, 
                    'message' => 'La contraseña debe tener al menos 6 caracteres'
                ];
            }

            // Actualizar en la base de datos
            if ($this->userRepository->updatePassword($userId, $newPassword)) {
                $_SESSION['first_login'] = false;
                return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar la contraseña'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        UrlHelper::redirect('public/login');
    }
}
