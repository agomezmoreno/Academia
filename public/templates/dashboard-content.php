<?php
// Verificar que el usuario está autenticado
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1>Bienvenido al Sistema</h1>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Información del Usuario</h5>
                    <p class="card-text">Usuario: <?php echo htmlspecialchars($username); ?></p>
                    <p class="card-text">Rol: <?php echo htmlspecialchars($role); ?></p>
                </div>
            </div>

            <?php if ($role === 'gestor'): ?>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Usuarios</h5>
                            <p class="card-text">Gestionar usuarios del sistema</p>
                            <a href="users.php" class="btn btn-primary">Gestionar Usuarios</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Materias</h5>
                            <p class="card-text">Gestionar materias y asignaciones</p>
                            <a href="subjects.php" class="btn btn-primary">Gestionar Materias</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Calificaciones</h5>
                            <p class="card-text">Gestionar calificaciones</p>
                            <a href="grades.php" class="btn btn-primary">Gestionar Calificaciones</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
