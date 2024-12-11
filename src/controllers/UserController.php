<?php

namespace Algom\Academia1\controllers;

use Algom\Academia1\models\User;
use Algom\Academia1\repositories\UserRepository;

class UserController {
    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function getAllUsers() {
        return $this->userRepository->findAll();
    }

    public function createUser($userData) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestor') {
            return ['success' => false, 'message' => 'No tiene permisos para crear usuarios'];
        }
        
        try {
            $user = new User($userData);
            $user->generateUsername();
            
            // Generar contraseña aleatoria
            $plainPassword = bin2hex(random_bytes(4));
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
            $user->setPassword($hashedPassword);
            $user->setFirstLogin(true);

            if ($this->userRepository->create($user)) {
                return [
                    'success' => true,
                    'username' => $user->getUsername(),
                    'password' => $plainPassword
                ];
            }
            
            return ['success' => false, 'message' => 'Error al crear el usuario'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteUser($id) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestor') {
            return ['success' => false, 'message' => 'No tiene permisos para eliminar usuarios'];
        }

        try {
            if ($this->userRepository->delete($id)) {
                return ['success' => true];
            }
            return ['success' => false, 'message' => 'Error al eliminar el usuario'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function resetPassword($id) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestor') {
            return ['success' => false, 'message' => 'No tiene permisos para restablecer contraseñas'];
        }

        try {
            // Generar nueva contraseña
            $plainPassword = bin2hex(random_bytes(4));
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

            if ($this->userRepository->updatePassword($id, $hashedPassword)) {
                return [
                    'success' => true,
                    'password' => $plainPassword
                ];
            }
            
            return ['success' => false, 'message' => 'Error al restablecer la contraseña'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
