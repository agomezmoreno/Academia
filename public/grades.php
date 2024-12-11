<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Algom\Academia1\repositories\GradeRepository;
use Algom\Academia1\repositories\SubjectRepository;
use Algom\Academia1\repositories\UserRepository;
use Algom\Academia1\models\Grade;
use Algom\Academia1\helpers\UrlHelper;

// Verificar si hay una sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    UrlHelper::redirect('login');
    exit();
}

// Verificar el rol para acceso (solo gestores y profesores pueden ver/gestionar calificaciones)
if (!in_array($_SESSION['role'], ['gestor', 'profesor'])) {
    UrlHelper::redirect('dashboard');
    exit();
}

$gradeRepository = new GradeRepository();
$subjectRepository = new SubjectRepository();
$userRepository = new UserRepository();
$error = '';
$success = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'create':
                // Validar datos requeridos
                if (empty($_POST['student_id'])) {
                    throw new \Exception('Debe seleccionar un estudiante');
                }
                if (empty($_POST['subject_id'])) {
                    throw new \Exception('Debe seleccionar una materia');
                }
                if (!isset($_POST['grade'])) {
                    throw new \Exception('Debe ingresar una calificación');
                }

                // Validar que el estudiante existe y es estudiante
                $student = $userRepository->findById($_POST['student_id']);
                if (!$student || $student->getRole() !== 'estudiante') {
                    throw new \Exception('El estudiante seleccionado no es válido');
                }

                // Debug: Imprimir información de la sesión
                error_log("Session user_id: " . $_SESSION['user_id']);
                error_log("Session role: " . $_SESSION['role']);

                // Verificar que el usuario creador existe
                $creator = $userRepository->findById($_SESSION['user_id']);
                if (!$creator) {
                    throw new \Exception('Error: Usuario creador no encontrado en la base de datos');
                }

                // Crear nueva calificación
                $grade = new Grade([
                    'student_id' => (int)$_POST['student_id'],
                    'subject_id' => (int)$_POST['subject_id'],
                    'grade' => (float)$_POST['grade'],
                    'comments' => trim($_POST['comments'] ?? ''),
                    'created_by' => (int)$_SESSION['user_id']
                ]);

                // Debug: Imprimir datos de la calificación
                error_log("Datos de la calificación:");
                error_log("student_id: " . $grade->getStudentId());
                error_log("subject_id: " . $grade->getSubjectId());
                error_log("grade: " . $grade->getGrade());
                error_log("created_by: " . $grade->getCreatedBy());

                // Validar que la materia existe
                $subject = $subjectRepository->findById($_POST['subject_id']);
                if (!$subject) {
                    throw new \Exception('La materia seleccionada no es válida');
                }

                // Validar permisos
                if ($_SESSION['role'] === 'profesor') {
                    // Verificar que el profesor tenga asignada esta materia
                    $teacherSubjects = $subjectRepository->getSubjectsByTeacher($_SESSION['user_id']);
                    $hasPermission = false;
                    foreach ($teacherSubjects as $teacherSubject) {
                        if ($teacherSubject->getId() == $_POST['subject_id']) {
                            $hasPermission = true;
                            break;
                        }
                    }
                    if (!$hasPermission) {
                        throw new \Exception('No tiene permiso para calificar esta materia');
                    }
                }

                // Guardar en la base de datos
                if (!$gradeRepository->save($grade)) {
                    throw new \Exception('Error al guardar la calificación en la base de datos');
                }

                $_SESSION['success_message'] = 'Calificación registrada exitosamente';
                UrlHelper::redirect('grades');
                exit();

            case 'update':
                // Verificar ID
                if (!isset($_POST['id'])) {
                    throw new \Exception('ID de calificación no proporcionado');
                }

                // Obtener calificación existente
                $grade = $gradeRepository->findById($_POST['id']);
                if (!$grade) {
                    throw new \Exception('Calificación no encontrada');
                }

                // Validar permisos
                if ($_SESSION['role'] === 'profesor') {
                    // Solo puede modificar calificaciones que él creó
                    if ($grade->getCreatedBy() !== $_SESSION['user_id']) {
                        throw new \Exception('No tiene permiso para modificar esta calificación');
                    }
                }

                // Actualizar datos
                $grade->setGrade((float)$_POST['grade']);
                $grade->setComments(trim($_POST['comments'] ?? ''));

                // Guardar cambios
                if (!$gradeRepository->update($grade)) {
                    throw new \Exception('Error al actualizar la calificación');
                }

                $_SESSION['success_message'] = 'Calificación actualizada exitosamente';
                UrlHelper::redirect('grades');
                exit();

            case 'delete':
                if (!isset($_POST['id'])) {
                    throw new \Exception('ID de calificación no proporcionado');
                }

                $grade = $gradeRepository->findById($_POST['id']);
                if (!$grade) {
                    throw new \Exception('Calificación no encontrada');
                }

                // Validar permisos
                if ($_SESSION['role'] === 'profesor') {
                    if ($grade->getCreatedBy() !== $_SESSION['user_id']) {
                        throw new \Exception('No tiene permiso para eliminar esta calificación');
                    }
                }

                if (!$gradeRepository->delete($_POST['id'])) {
                    throw new \Exception('Error al eliminar la calificación');
                }

                $_SESSION['success_message'] = 'Calificación eliminada exitosamente';
                UrlHelper::redirect('grades');
                exit();
        }
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

// Obtener lista de estudiantes
$students = $userRepository->findByRole('estudiante');

// Obtener lista de materias según el rol
if ($_SESSION['role'] === 'profesor') {
    $subjects = $subjectRepository->getSubjectsByTeacher($_SESSION['user_id']);
} else {
    $subjects = $subjectRepository->findAll();
}

// Obtener calificaciones
if ($_SESSION['role'] === 'profesor') {
    $grades = $gradeRepository->getGradesByTeacher($_SESSION['user_id']);
} else {
    $grades = $gradeRepository->findAll();
}

// Definir el título y contenido para el layout
$pageTitle = 'Gestión de Calificaciones';
$content = __DIR__ . '/templates/grades-content.php';

// Incluir el layout principal
require_once __DIR__ . '/templates/layout.php';
