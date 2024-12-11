<div class="container py-4">
    <!-- Encabezado y botón de agregar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-book"></i> Materias
        </h2>
        <?php if ($_SESSION['role'] === 'gestor'): ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i class="bi bi-plus-lg"></i> Nueva Materia
        </button>
        <?php endif; ?>
    </div>

    <!-- Mensajes de alerta -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Lista de materias -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($subjects as $subject): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <?php echo htmlspecialchars($subject->getName()); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <i class="bi bi-info-circle"></i>
                            <strong>Código:</strong> <?php echo htmlspecialchars($subject->getCode()); ?>
                        </p>
                        <p class="card-text">
                            <i class="bi bi-calendar3"></i>
                            <strong>Curso:</strong> <?php echo htmlspecialchars($subject->getCourse()); ?>
                        </p>
                    </div>
                    <?php if ($_SESSION['role'] === 'gestor'): ?>
                    <div class="card-footer bg-white border-0">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                    data-bs-toggle="modal" data-bs-target="#editSubjectModal<?php echo $subject->getId(); ?>">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <form action="/academia1/subjects" method="POST" class="d-inline" 
                                  onsubmit="return confirm('¿Está seguro de que desea eliminar esta materia?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $subject->getId(); ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Modal de edición para cada materia -->
            <?php if ($_SESSION['role'] === 'gestor'): ?>
            <div class="modal fade" id="editSubjectModal<?php echo $subject->getId(); ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Materia</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="/academia1/subjects" method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo $subject->getId(); ?>">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name<?php echo $subject->getId(); ?>" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="name<?php echo $subject->getId(); ?>" 
                                           name="name" value="<?php echo htmlspecialchars($subject->getName()); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="code<?php echo $subject->getId(); ?>" class="form-label">Código</label>
                                    <input type="text" class="form-control" id="code<?php echo $subject->getId(); ?>" 
                                           name="code" value="<?php echo htmlspecialchars($subject->getCode()); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="course<?php echo $subject->getId(); ?>" class="form-label">Curso</label>
                                    <input type="text" class="form-control" id="course<?php echo $subject->getId(); ?>" 
                                           name="course" value="<?php echo htmlspecialchars($subject->getCourse()); ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal para agregar materia -->
<?php if ($_SESSION['role'] === 'gestor'): ?>
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Materia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/academia1/subjects" method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Código</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="course" class="form-label">Curso</label>
                        <input type="text" class="form-control" id="course" name="course" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
