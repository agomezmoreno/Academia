<?php

namespace Algom\Academia1\models;

class Subject {
    private ?int $id = null;
    private string $name = '';
    private string $code = '';
    private string $course = '';
    private ?int $teacherId = null;
    private ?string $created_at = null;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int)$data['id'] : null;
            $this->name = $data['name'] ?? '';
            $this->code = $data['code'] ?? '';
            $this->course = $data['course'] ?? '';
            $this->teacherId = isset($data['teacherId']) ? (int)$data['teacherId'] : null;
            $this->created_at = $data['created_at'] ?? null;
        }
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getCourse(): string {
        return $this->course;
    }

    public function getTeacherId(): ?int {
        return $this->teacherId;
    }

    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setName(string $name): void {
        $this->name = trim($name);
    }

    public function setCode(string $code): void {
        $this->code = trim($code);
    }

    public function setCourse(string $course): void {
        $this->course = trim($course);
    }

    public function setTeacherId(?int $teacherId): void {
        $this->teacherId = $teacherId;
    }

    public function setCreatedAt(?string $created_at): void {
        $this->created_at = $created_at;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'course' => $this->course,
            'teacherId' => $this->teacherId,
            'created_at' => $this->created_at
        ];
    }

    public function validate(): array {
        $errors = [];
        
        if (empty($this->name)) {
            $errors[] = "El nombre es requerido";
        }
        
        if (empty($this->code)) {
            $errors[] = "El código es requerido";
        }
        
        if (empty($this->course)) {
            $errors[] = "El curso es requerido";
        }
        
        return $errors;
    }
}
