<?php

namespace Algom\Academia1\models;

class Grade {
    private ?int $id = null;
    private int $student_id;
    private int $subject_id;
    private float $grade;
    private ?string $comments = null;
    private int $created_by;
    private ?string $created_at = null;
    private ?string $studentName = null;
    private ?string $subjectName = null;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int)$data['id'] : null;
            $this->student_id = (int)($data['student_id'] ?? 0);
            $this->subject_id = (int)($data['subject_id'] ?? 0);
            $this->grade = (float)($data['grade'] ?? 0.0);
            $this->comments = $data['comments'] ?? null;
            $this->created_by = (int)($data['created_by'] ?? 0);
            $this->created_at = $data['created_at'] ?? null;
            $this->studentName = $data['student_name'] ?? null;
            $this->subjectName = $data['subject_name'] ?? null;
        }
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getStudentId(): int {
        return $this->student_id;
    }

    public function getSubjectId(): int {
        return $this->subject_id;
    }

    public function getGrade(): float {
        return $this->grade;
    }

    public function getComments(): ?string {
        return $this->comments;
    }

    public function getCreatedBy(): int {
        return $this->created_by;
    }

    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    public function getStudentName(): ?string {
        return $this->studentName;
    }

    public function getSubjectName(): ?string {
        return $this->subjectName;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setStudentId(int $student_id): void {
        $this->student_id = $student_id;
    }

    public function setSubjectId(int $subject_id): void {
        $this->subject_id = $subject_id;
    }

    public function setGrade(float $grade): void {
        $this->grade = $grade;
    }

    public function setComments(?string $comments): void {
        $this->comments = $comments;
    }

    public function setCreatedBy(int $created_by): void {
        $this->created_by = $created_by;
    }

    public function setCreatedAt(?string $created_at): void {
        $this->created_at = $created_at;
    }

    public function setStudentName(?string $studentName): void {
        $this->studentName = $studentName;
    }

    public function setSubjectName(?string $subjectName): void {
        $this->subjectName = $subjectName;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'subject_id' => $this->subject_id,
            'grade' => $this->grade,
            'comments' => $this->comments,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'student_name' => $this->studentName,
            'subject_name' => $this->subjectName
        ];
    }

    public function validate(): array {
        $errors = [];
        
        if ($this->student_id <= 0) {
            $errors[] = "El estudiante es requerido";
        }
        
        if ($this->subject_id <= 0) {
            $errors[] = "La materia es requerida";
        }
        
        if ($this->grade < 0 || $this->grade > 10) {
            $errors[] = "La calificación debe estar entre 0 y 10";
        }
        
        if ($this->created_by <= 0) {
            $errors[] = "El usuario que crea la calificación es requerido";
        }
        
        return $errors;
    }
}
