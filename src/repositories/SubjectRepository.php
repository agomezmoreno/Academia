<?php

namespace Algom\Academia1\repositories;

use Algom\Academia1\models\Subject;
use PDO;

class SubjectRepository extends BaseRepository {
    
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM subjects ORDER BY name");
        $subjects = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subjects[] = $this->createSubjectFromRow($row);
        }
        
        return $subjects;
    }
    
    public function findById(int $id): ?Subject {
        $stmt = $this->db->prepare("SELECT * FROM subjects WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->createSubjectFromRow($row);
        }
        
        return null;
    }
    
    public function save(Subject $subject): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO subjects (name, code, course) 
                VALUES (:name, :code, :course)
            ");
            
            return $stmt->execute([
                ':name' => $subject->getName(),
                ':code' => $subject->getCode(),
                ':course' => $subject->getCourse()
            ]);
        } catch (\PDOException $e) {
            error_log("Error al guardar materia: " . $e->getMessage());
            return false;
        }
    }
    
    public function update(Subject $subject): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE subjects 
                SET name = :name, 
                    code = :code, 
                    course = :course 
                WHERE id = :id
            ");
            
            return $stmt->execute([
                ':id' => $subject->getId(),
                ':name' => $subject->getName(),
                ':code' => $subject->getCode(),
                ':course' => $subject->getCourse()
            ]);
        } catch (\PDOException $e) {
            error_log("Error al actualizar materia: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM subjects WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error al eliminar materia: " . $e->getMessage());
            return false;
        }
    }

    public function getTotalSubjects(): int {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM subjects");
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error al obtener total de materias: " . $e->getMessage());
            return 0;
        }
    }
    
    private function createSubjectFromRow(array $row): Subject {
        $subject = new Subject();
        $subject->setId($row['id']);
        $subject->setName($row['name']);
        $subject->setCode($row['code']);
        $subject->setCourse($row['course']);
        return $subject;
    }
}
