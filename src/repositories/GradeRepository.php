<?php

namespace Algom\Academia1\repositories;

use Algom\Academia1\models\Grade;
use PDO;

class GradeRepository extends BaseRepository {
    
    public function findAll(): array {
        $stmt = $this->db->query("
            SELECT g.*, CONCAT(u.name, ' ', u.surname1, ' ', u.surname2) as student_name, sub.name as subject_name
            FROM grades g
            INNER JOIN users u ON g.student_id = u.id
            INNER JOIN subjects sub ON g.subject_id = sub.id
            WHERE u.role = 'estudiante'
            ORDER BY g.created_at DESC
        ");
        
        $grades = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $grades[] = $this->createGradeFromRow($row);
        }
        
        return $grades;
    }
    
    public function findById(int $id): ?Grade {
        $stmt = $this->db->prepare("
            SELECT g.*, CONCAT(u.name, ' ', u.surname1, ' ', u.surname2) as student_name, sub.name as subject_name
            FROM grades g
            INNER JOIN users u ON g.student_id = u.id
            INNER JOIN subjects sub ON g.subject_id = sub.id
            WHERE g.id = ? AND u.role = 'estudiante'
        ");
        $stmt->execute([$id]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->createGradeFromRow($row);
        }
        
        return null;
    }
    
    public function save(Grade $grade): bool {
        try {
            // Verificar que el estudiante existe y tiene el rol correcto
            $stmt = $this->db->prepare("
                SELECT id FROM users 
                WHERE id = ? AND role = 'estudiante'
            ");
            $stmt->execute([$grade->getStudentId()]);
            if (!$stmt->fetch()) {
                throw new \Exception("El usuario no existe o no es estudiante");
            }

            // Verificar que la materia existe
            $stmt = $this->db->prepare("
                SELECT id FROM subjects 
                WHERE id = ?
            ");
            $stmt->execute([$grade->getSubjectId()]);
            if (!$stmt->fetch()) {
                throw new \Exception("La materia no existe");
            }

            // Verificar que la calificación está en el rango válido
            if ($grade->getGrade() < 0 || $grade->getGrade() > 10) {
                throw new \Exception("La calificación debe estar entre 0 y 10");
            }

            // Intentar insertar la calificación
            $stmt = $this->db->prepare("
                INSERT INTO grades (student_id, subject_id, grade, comments, created_by) 
                VALUES (:student_id, :subject_id, :grade, :comments, :created_by)
            ");
            
            $params = [
                ':student_id' => $grade->getStudentId(),
                ':subject_id' => $grade->getSubjectId(),
                ':grade' => $grade->getGrade(),
                ':comments' => $grade->getComments(),
                ':created_by' => $grade->getCreatedBy()
            ];

            // Debug: Imprimir los parámetros
            error_log("Intentando guardar calificación con parámetros: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if (!$result) {
                $error = $stmt->errorInfo();
                throw new \Exception("Error SQL: " . $error[2]);
            }
            
            return true;

        } catch (\PDOException $e) {
            error_log("Error PDO al guardar calificación: " . $e->getMessage());
            error_log("Código de error: " . $e->getCode());
            error_log("SQL State: " . $e->errorInfo[0]);
            throw new \Exception("Error de base de datos: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("Error al guardar calificación: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function update(Grade $grade): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE grades 
                SET grade = :grade,
                    comments = :comments
                WHERE id = :id
            ");
            
            return $stmt->execute([
                ':id' => $grade->getId(),
                ':grade' => $grade->getGrade(),
                ':comments' => $grade->getComments()
            ]);
        } catch (\PDOException $e) {
            error_log("Error al actualizar calificación: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM grades WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error al eliminar calificación: " . $e->getMessage());
            return false;
        }
    }

    public function getTotalGrades(): int {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM grades");
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error al obtener total de calificaciones: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalGradesByTeacher(int $teacherId): int {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM grades g
                INNER JOIN subjects s ON g.subject_id = s.id
                INNER JOIN teacher_subjects ts ON s.id = ts.subject_id
                WHERE ts.teacher_id = ?
            ");
            $stmt->execute([$teacherId]);
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error al obtener total de calificaciones del profesor: " . $e->getMessage());
            return 0;
        }
    }

    public function getGradesByTeacher(int $teacherId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT g.*, CONCAT(u.name, ' ', u.surname1, ' ', u.surname2) as student_name, sub.name as subject_name
                FROM grades g
                INNER JOIN users u ON g.student_id = u.id
                INNER JOIN subjects sub ON g.subject_id = sub.id
                INNER JOIN teacher_subjects ts ON sub.id = ts.subject_id
                WHERE ts.teacher_id = ? AND u.role = 'estudiante'
                ORDER BY g.created_at DESC
            ");
            $stmt->execute([$teacherId]);
            
            $grades = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $grades[] = $this->createGradeFromRow($row);
            }
            return $grades;
        } catch (\PDOException $e) {
            error_log("Error al obtener calificaciones del profesor: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentGrades(int $limit = 5): array {
        try {
            $stmt = $this->db->prepare("
                SELECT g.*, CONCAT(u.name, ' ', u.surname1, ' ', u.surname2) as student_name, sub.name as subject_name
                FROM grades g
                INNER JOIN users u ON g.student_id = u.id
                INNER JOIN subjects sub ON g.subject_id = sub.id
                WHERE u.role = 'estudiante'
                ORDER BY g.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            
            $grades = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $grades[] = $this->createGradeFromRow($row);
            }
            return $grades;
        } catch (\PDOException $e) {
            error_log("Error al obtener calificaciones recientes: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentGradesByTeacher(int $teacherId, int $limit = 5): array {
        try {
            $stmt = $this->db->prepare("
                SELECT g.*, CONCAT(u.name, ' ', u.surname1, ' ', u.surname2) as student_name, sub.name as subject_name
                FROM grades g
                INNER JOIN users u ON g.student_id = u.id
                INNER JOIN subjects sub ON g.subject_id = sub.id
                INNER JOIN teacher_subjects ts ON sub.id = ts.subject_id
                WHERE ts.teacher_id = ? AND u.role = 'estudiante'
                ORDER BY g.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$teacherId, $limit]);
            
            $grades = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $grades[] = $this->createGradeFromRow($row);
            }
            return $grades;
        } catch (\PDOException $e) {
            error_log("Error al obtener calificaciones recientes del profesor: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentGradesByStudents(array $studentIds, int $limit = 5): array {
        if (empty($studentIds)) {
            return [];
        }

        try {
            $placeholders = str_repeat('?,', count($studentIds) - 1) . '?';
            $sql = "
                SELECT g.*, CONCAT(u.name, ' ', u.surname1, ' ', u.surname2) as student_name, sub.name as subject_name
                FROM grades g
                INNER JOIN users u ON g.student_id = u.id
                INNER JOIN subjects sub ON g.subject_id = sub.id
                WHERE g.student_id IN ($placeholders) AND u.role = 'estudiante'
                ORDER BY g.created_at DESC
                LIMIT ?
            ";
            
            $params = array_merge($studentIds, [$limit]);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $grades = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $grades[] = $this->createGradeFromRow($row);
            }
            return $grades;
        } catch (\PDOException $e) {
            error_log("Error al obtener calificaciones recientes de los estudiantes: " . $e->getMessage());
            return [];
        }
    }
    
    private function createGradeFromRow(array $row): Grade {
        return new Grade([
            'id' => $row['id'],
            'student_id' => $row['student_id'],
            'subject_id' => $row['subject_id'],
            'grade' => $row['grade'],
            'comments' => $row['comments'],
            'created_by' => $row['created_by'],
            'created_at' => $row['created_at'],
            'student_name' => $row['student_name'],
            'subject_name' => $row['subject_name']
        ]);
    }
}
