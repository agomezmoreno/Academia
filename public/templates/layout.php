<?php
// Base URL for the application
define('BASE_URL', '/academia1');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Academia Los Excelentes'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <base href="<?php echo BASE_URL; ?>/">
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard">Academia Los Excelentes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($_SESSION['role'] === 'gestor'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="users">
                                <i class="bi bi-people"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="subjects">
                                <i class="bi bi-book"></i> Materias
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['role'] === 'gestor' || $_SESSION['role'] === 'profesor'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="grades">
                                <i class="bi bi-card-checklist"></i> Calificaciones
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-file-text"></i> Informes
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="reports/grades-by-subject">Por Asignatura</a></li>
                                <li><a class="dropdown-item" href="reports/grades-by-student">Por Alumno</a></li>
                                <li><a class="dropdown-item" href="reports/average-by-subject">Media por Asignatura</a></li>
                                <li><a class="dropdown-item" href="reports/average-by-student">Media por Alumno</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['role'] === 'tutor'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="student-grades">
                                <i class="bi bi-card-checklist"></i> Notas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="download-pdf">
                                <i class="bi bi-file-pdf"></i> Descargar PDF
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="logout">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <div class="container mt-4">
        <?php 
        if (isset($content) && file_exists($content)) {
            include $content;
        } else {
            echo "Error: Content template not found";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
