<?php

namespace Algom\Academia1\controllers;

use Algom\Academia1\models\Subject;
use Algom\Academia1\repositories\SubjectRepository;
use Algom\Academia1\repositories\UserRepository;

class SubjectController {
    private $subjectRepository;
    private $userRepository;

    public function __construct() {
        $this->subjectRepository = new SubjectRepository();
        $this->userRepository = new UserRepository();
    }

    public function index(): array {
        return $this->subjectRepository->findAll();
    }

    public function create(array $data): array {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestor') {
            return [
                'success' => false,
                'message' => 'No tiene permisos para crear asignaturas'
            ];
        }

        $subject = new Subject($data);
        $errors = $subject->validate();

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => implode(', ', $errors)
            ];
        }

        if ($this->subjectRepository->findByName($subject->getName())) {
            return [
                'success' => false,
                'message' => 'Ya existe una asignatura con ese nombre'
            ];
        }

        if ($this->subjectRepository->create($subject)) {
            return [
                'success' => true,
                'message' => 'Asignatura creada correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al crear la asignatura'
        ];
    }

    public function update(int $id, array $data): array {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestor') {
            return [
                'success' => false,
                'message' => 'No tiene permisos para modificar asignaturas'
            ];
        }

        $subject = new Subject($data);
        $subject->setId($id);
        $errors = $subject->validate();

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => implode(', ', $errors)
            ];
        }

        $existingSubject = $this->subjectRepository->findByName($subject->getName());
        if ($existingSubject && $existingSubject->getId() !== $id) {
            return [
                'success' => false,
                'message' => 'Ya existe una asignatura con ese nombre'
            ];
        }

        if ($this->subjectRepository->update($id, $data)) {
            return [
                'success' => true,
                'message' => 'Asignatura actualizada correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al actualizar la asignatura'
        ];
    }

    public function delete(int $id): array {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestor') {
            return [
                'success' => false,
                'message' => 'No tiene permisos para eliminar asignaturas'
            ];
        }

        if ($this->subjectRepository->delete($id)) {
            return [
                'success' => true,
                'message' => 'Asignatura eliminada correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al eliminar la asignatura'
        ];
    }

    public function assignTeacher(int $subjectId, int $teacherId): array {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestor') {
            return [
                'success' => false,
                'message' => 'No tiene permisos para asignar profesores'
            ];
        }

        $teacher = $this->userRepository->findById($teacherId);
        if (!$teacher || $teacher->getRole() !== 'profesor') {
            return [
                'success' => false,
                'message' => 'Profesor no válido'
            ];
        }

        if ($this->subjectRepository->assignTeacher($subjectId, $teacherId)) {
            return [
                'success' => true,
                'message' => 'Profesor asignado correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al asignar el profesor'
        ];
    }

    public function removeTeacher(int $subjectId, int $teacherId): array {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestor') {
            return [
                'success' => false,
                'message' => 'No tiene permisos para eliminar profesores'
            ];
        }

        if ($this->subjectRepository->removeTeacher($subjectId, $teacherId)) {
            return [
                'success' => true,
                'message' => 'Profesor eliminado correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al eliminar el profesor'
        ];
    }
}
