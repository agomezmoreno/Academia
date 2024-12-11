<?php

namespace Algom\Academia1\models;

class User {
    private $id;
    private $username;
    private $password;
    private $name;
    private $surname1;
    private $surname2;
    private $email;
    private $dni;
    private $role;
    private $first_login;

    private static $validRoles = ['gestor', 'profesor', 'tutor', 'estudiante'];

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->surname1 = $data['surname1'] ?? '';
        $this->surname2 = $data['surname2'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->dni = $data['dni'] ?? '';
        $this->role = in_array($data['role'] ?? '', self::$validRoles) ? $data['role'] : '';
        $this->first_login = $data['first_login'] ?? true;
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function generateUsername(): void {
        // Primera letra del nombre
        $firstLetter = mb_substr(mb_strtolower($this->name), 0, 1);
        
        // Tres primeras letras de cada apellido
        $surname1Start = mb_substr(mb_strtolower($this->surname1), 0, 3);
        $surname2Start = mb_substr(mb_strtolower($this->surname2), 0, 3);
        
        // Últimos 3 dígitos del DNI
        $dniDigits = preg_replace('/[^0-9]/', '', $this->dni);
        $lastThreeDigits = substr($dniDigits, -3);
        
        // Combinar todo
        $this->username = $firstLetter . $surname1Start . $surname2Start . $lastThreeDigits;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function generatePassword(): string {
        // Generar una contraseña aleatoria de 8 caracteres
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < 8; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        $this->password = $password;
        return $password;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getSurname1() {
        return $this->surname1;
    }

    public function setSurname1($surname1) {
        $this->surname1 = $surname1;
    }

    public function getSurname2() {
        return $this->surname2;
    }

    public function setSurname2($surname2) {
        $this->surname2 = $surname2;
    }

    public function getFullSurname() {
        return trim($this->surname1 . ' ' . $this->surname2);
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setDni($dni) {
        $this->dni = $dni;
    }

    public function getDni() {
        return $this->dni;
    }

    public function setRole($role) {
        if (in_array($role, self::$validRoles)) {
            $this->role = $role;
        }
    }

    public function getRole() {
        return $this->role;
    }

    public function isFirstLogin() {
        return $this->first_login;
    }

    public function setFirstLogin($value) {
        $this->first_login = $value;
    }
}
