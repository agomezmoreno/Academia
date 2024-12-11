<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academia - Los Excelentes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Academia</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['username'])): ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                        <?php if ($_SESSION['role'] === 'gestor'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="users.php">Usuarios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="subjects.php">Materias</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="grades.php">Calificaciones</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <span class="nav-link">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <form action="logout.php" method="post" class="d-inline">
                                <button type="submit" class="btn btn-link nav-link">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container-fluid py-4">
