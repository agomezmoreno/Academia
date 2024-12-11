<div class="container py-4">
    <!-- Encabezado y botón de agregar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-mortarboard"></i> Calificaciones
        </h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
            <i class="bi bi-plus-lg"></i> Nueva Calificación
        </button>
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

    <!-- Tabla de calificaciones -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Materia</th>
                            <th>Calificación</th>
                            <th>Comentarios</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($grade->getStudentName()); ?></td>
                                <td><?php echo htmlspecialchars($grade->getSubjectName()); ?></td>
                                <td><?php echo htmlspecialchars(number_format($grade->getGrade(), 2)); ?></td>
                                <td><?php echo htmlspecialchars($grade->getComments()); ?></td>
                                <td><?php echo htmlspecialchars($grade->getCreatedAt()); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editGradeModal<?php echo $grade->getId(); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="/academia1/grades" method="POST" class="d-inline" 
                                              onsubmit="return confirm('¿Está seguro de que desea eliminar esta calificación?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $grade->getId(); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal de edición para cada calificación -->
                            <div class="modal fade" id="editGradeModal<?php echo $grade->getId(); ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar Calificación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="/academia1/grades" method="POST">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?php echo $grade->getId(); ?>">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Estudiante</label>
                                                    <input type="text" class="form-control" 
                                                           value="<?php echo htmlspecialchars($grade->getStudentName()); ?>" 
                                                           disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Materia</label>
                                                    <input type="text" class="form-control" 
                                                           value="<?php echo htmlspecialchars($grade->getSubjectName()); ?>" 
                                                           disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="grade<?php echo $grade->getId(); ?>" class="form-label">Calificación</label>
                                                    <input type="number" class="form-control" id="grade<?php echo $grade->getId(); ?>" 
                                                           name="grade" value="<?php echo $grade->getGrade(); ?>" 
                                                           min="0" max="10" step="0.01" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="comments<?php echo $grade->getId(); ?>" class="form-label">Comentarios</label>
                                                    <textarea class="form-control" id="comments<?php echo $grade->getId(); ?>" 
                                                              name="comments" rows="3"><?php echo htmlspecialchars($grade->getComments()); ?></textarea>
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
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar calificación -->
<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Calificación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/academia1/grades" method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Estudiante</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">Seleccione un estudiante</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student->getId(); ?>">
                                    <?php echo htmlspecialchars($student->getName()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Materia</label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">Seleccione una materia</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject->getId(); ?>">
                                    <?php echo htmlspecialchars($subject->getName()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="grade" class="form-label">Calificación</label>
                        <input type="number" class="form-control" id="grade" name="grade" 
                               min="0" max="10" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="comments" class="form-label">Comentarios</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
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

<style>
.table th {
    background-color: #f8f9fa;
}
.btn-group {
    gap: 0.25rem;
}
</style>
