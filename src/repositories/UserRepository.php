<?php

namespace Algom\Academia1\repositories;

use Algom\Academia1\config\Database;
use Algom\Academia1\models\User;
use PDO;

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $userData ? new User($userData) : null;
    }

    public function findByUsername(string $username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $userData ? new User($userData) : null;
    }

    public function findByEmail(string $email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $userData ? new User($userData) : null;
    }

    public function findByRole(string $role) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? ORDER BY surname1, surname2, name");
        $stmt->execute([$role]);
        $users = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row);
        }
        
        return $users;
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY surname1, surname2, name");
        $users = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row);
        }
        
        return $users;
    }

    public function create(User $user): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (name, surname1, surname2, dni, email, username, password, role, first_login)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $user->getName(),
                $user->getSurname1(),
                $user->getSurname2(),
                $user->getDni(),
                $user->getEmail(),
                $user->getUsername(),
                $user->getPassword(), // Contraseña en texto plano
                $user->getRole(),
                $user->isFirstLogin()
            ]);
        } catch (\PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword(int $userId, string $password): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET password = ?, first_login = false
                WHERE id = ?
            ");
            return $stmt->execute([$password, $userId]); // Contraseña en texto plano
        } catch (\PDOException $e) {
            error_log("Error al actualizar contraseña: " . $e->getMessage());
            return false;
        }
    }

    public function update(User $user): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET name = :name,
                    surname1 = :surname1,
                    surname2 = :surname2,
                    dni = :dni,
                    email = :email,
                    username = :username,
                    role = :role
                WHERE id = :id
            ");
            
            return $stmt->execute([
                ':name' => $user->getName(),
                ':surname1' => $user->getSurname1(),
                ':surname2' => $user->getSurname2(),
                ':dni' => $user->getDni(),
                ':email' => $user->getEmail(),
                ':username' => $user->getUsername(),
                ':role' => $user->getRole(),
                ':id' => $user->getId()
            ]);
        } catch (\PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }
}
