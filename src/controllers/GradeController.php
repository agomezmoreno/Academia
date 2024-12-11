<?php

namespace Algom\Academia1\controllers;

use Algom\Academia1\models\Grade;
use Algom\Academia1\repositories\GradeRepository;
use Algom\Academia1\repositories\UserRepository;
use Algom\Academia1\repositories\SubjectRepository;

class GradeController {
    private GradeRepository $gradeRepository;
    private UserRepository $userRepository;
    private SubjectRepository $subjectRepository;

    public function __construct() {
        $this->gradeRepository = new GradeRepository();
        $this->userRepository = new UserRepository();
        $this->subjectRepository = new SubjectRepository();
    }

    public function getAllGrades(): array {
        return $this->gradeRepository->findAll();
    }

    public function getStudents(): array {
        return $this->userRepository->findByRole('estudiante');
    }

    public function getSubjects(): array {
        return $this->subjectRepository->findAll();
    }

    public function saveGrade(array $data): array {
        $grade = new Grade([
            'student_id' => $data['student_id'],
            'subject_id' => $data['subject_id'],
            'grade' => $data['grade'],
            'comments' => $data['comments'] ?? null,
            'created_by' => $_SESSION['user_id']
        ]);

        if ($this->gradeRepository->save($grade)) {
            return [
                'success' => true,
                'message' => 'Calificación guardada correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al guardar la calificación'
        ];
    }

    public function updateGrade(array $data): array {
        $grade = $this->gradeRepository->findById($data['id']);
        if (!$grade) {
            return [
                'success' => false,
                'message' => 'Calificación no encontrada'
            ];
        }

        $grade->setGrade($data['grade']);
        $grade->setComments($data['comments'] ?? null);

        if ($this->gradeRepository->update($grade)) {
            return [
                'success' => true,
                'message' => 'Calificación actualizada correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al actualizar la calificación'
        ];
    }

    public function deleteGrade(int $id): array {
        if ($this->gradeRepository->delete($id)) {
            return [
                'success' => true,
                'message' => 'Calificación eliminada correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al eliminar la calificación'
        ];
    }
}
