<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Algom\Academia1\repositories\SubjectRepository;
use Algom\Academia1\models\Subject;

// Verificar si hay una sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /academia1/login');
    exit();
}

// Verificar el rol para acceso
if ($_SESSION['role'] !== 'gestor') {
    header('Location: /academia1/dashboard');
    exit();
}

$subjectRepository = new SubjectRepository();
$error = '';
$success = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'create':
                // Debug
                error_log("Datos recibidos para crear materia: " . print_r($_POST, true));
                
                // Crear nueva materia
                $subject = new Subject([
                    'name' => trim($_POST['name'] ?? ''),
                    'code' => trim($_POST['code'] ?? ''),
                    'course' => trim($_POST['course'] ?? '')
                ]);

                // Debug
                error_log("Objeto Subject creado: " . print_r($subject->toArray(), true));

                // Validar datos
                $errors = $subject->validate();
                if (!empty($errors)) {
                    error_log("Errores de validación: " . implode(', ', $errors));
                    throw new \Exception(implode('. ', $errors));
                }

                // Guardar en la base de datos
                if (!$subjectRepository->save($subject)) {
                    error_log("Error al guardar la materia en la base de datos");
                    throw new \Exception('Error al crear la materia en la base de datos');
                }

                $success = 'Materia creada exitosamente';
                break;

            case 'update':
                // Verificar ID
                if (!isset($_POST['id'])) {
                    throw new \Exception('ID de materia no proporcionado');
                }

                // Obtener materia existente
                $subject = $subjectRepository->findById($_POST['id']);
                if (!$subject) {
                    throw new \Exception('Materia no encontrada');
                }

                // Actualizar datos
                $subject->setName(trim($_POST['name'] ?? ''));
                $subject->setCode(trim($_POST['code'] ?? ''));
                $subject->setCourse(trim($_POST['course'] ?? ''));

                // Validar datos
                $errors = $subject->validate();
                if (!empty($errors)) {
                    throw new \Exception(implode('. ', $errors));
                }

                // Guardar cambios
                if (!$subjectRepository->update($subject)) {
                    throw new \Exception('Error al actualizar la materia');
                }

                $success = 'Materia actualizada exitosamente';
                break;

            case 'delete':
                // Verificar ID
                if (!isset($_POST['id'])) {
                    throw new \Exception('ID de materia no proporcionado');
                }

                // Eliminar materia
                if (!$subjectRepository->delete($_POST['id'])) {
                    throw new \Exception('Error al eliminar la materia');
                }

                $success = 'Materia eliminada exitosamente';
                break;

            default:
                throw new \Exception('Acción no válida');
        }
    } catch (\PDOException $e) {
        error_log("Error de base de datos: " . $e->getMessage());
        $error = 'Error al procesar la operación. El código podría estar duplicado.';
    } catch (\Exception $e) {
        error_log("Error: " . $e->getMessage());
        $error = $e->getMessage();
    }
}

// Obtener lista de materias
try {
    $subjects = $subjectRepository->findAll();
} catch (\Exception $e) {
    error_log("Error al obtener materias: " . $e->getMessage());
    $error = 'Error al cargar las materias';
    $subjects = [];
}

// Configurar la página
$pageTitle = 'Gestión de Materias';
$content = __DIR__ . '/templates/subjects-content.php';

require_once __DIR__ . '/templates/layout.php';
