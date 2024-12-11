<?php

namespace Algom\Academia1\repositories;

use Algom\Academia1\models\Student;
use PDO;

class StudentRepository extends BaseRepository {
    
    public function findAll(): array {
        try {
            $stmt = $this->db->query("
                SELECT s.*, CONCAT(s.name, ' ', s.surname1, IFNULL(CONCAT(' ', s.surname2), '')) as full_name
                FROM students s
                ORDER BY s.surname1, s.surname2, s.name
            ");
            
            $students = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $students[] = $this->createStudentFromRow($row);
            }
            
            return $students;
        } catch (\PDOException $e) {
            error_log("Error al obtener estudiantes: " . $e->getMessage());
            return [];
        }
    }
    
    public function findById(int $id): ?Student {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, CONCAT(s.name, ' ', s.surname1, IFNULL(CONCAT(' ', s.surname2), '')) as full_name
                FROM students s
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->createStudentFromRow($row);
            }
            
            return null;
        } catch (\PDOException $e) {
            error_log("Error al obtener estudiante: " . $e->getMessage());
            return null;
        }
    }
    
    public function save(Student $student): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO students (name, surname1, surname2, tutor_id) 
                VALUES (:name, :surname1, :surname2, :tutor_id)
            ");
            
            return $stmt->execute([
                ':name' => $student->getName(),
                ':surname1' => $student->getSurname1(),
                ':surname2' => $student->getSurname2(),
                ':tutor_id' => $student->getTutorId()
            ]);
        } catch (\PDOException $e) {
            error_log("Error al guardar estudiante: " . $e->getMessage());
            return false;
        }
    }
    
    public function update(Student $student): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE students 
                SET name = :name,
                    surname1 = :surname1,
                    surname2 = :surname2,
                    tutor_id = :tutor_id
                WHERE id = :id
            ");
            
            return $stmt->execute([
                ':id' => $student->getId(),
                ':name' => $student->getName(),
                ':surname1' => $student->getSurname1(),
                ':surname2' => $student->getSurname2(),
                ':tutor_id' => $student->getTutorId()
            ]);
        } catch (\PDOException $e) {
            error_log("Error al actualizar estudiante: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM students WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error al eliminar estudiante: " . $e->getMessage());
            return false;
        }
    }

    public function getTotalStudents(): int {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM students");
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error al obtener total de estudiantes: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalStudentsByTeacher(int $teacherId): int {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT s.id)
                FROM students s
                INNER JOIN grades g ON s.id = g.student_id
                INNER JOIN subjects sub ON g.subject_id = sub.id
                INNER JOIN teacher_subjects ts ON sub.id = ts.subject_id
                WHERE ts.teacher_id = ?
            ");
            $stmt->execute([$teacherId]);
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error al obtener total de estudiantes del profesor: " . $e->getMessage());
            return 0;
        }
    }

    public function getStudentsByTutor(int $tutorId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, CONCAT(s.name, ' ', s.surname1, IFNULL(CONCAT(' ', s.surname2), '')) as full_name
                FROM students s
                WHERE s.tutor_id = ?
                ORDER BY s.surname1, s.surname2, s.name
            ");
            $stmt->execute([$tutorId]);
            
            $students = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $students[] = $this->createStudentFromRow($row);
            }
            return $students;
        } catch (\PDOException $e) {
            error_log("Error al obtener estudiantes del tutor: " . $e->getMessage());
            return [];
        }
    }
    
    private function createStudentFromRow(array $row): Student {
        $student = new Student();
        $student->setId($row['id']);
        $student->setName($row['name']);
        $student->setSurname1($row['surname1']);
        $student->setSurname2($row['surname2'] ?? null);
        $student->setTutorId($row['tutor_id'] ?? null);
        $student->setFullName($row['full_name']);
        if (isset($row['created_at'])) {
            $student->setCreatedAt($row['created_at']);
        }
        return $student;
    }
}
