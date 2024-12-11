<?php
$roles = ['gestor' => 'Gestor', 'profesor' => 'Profesor', 'tutor' => 'Tutor', 'estudiante' => 'Estudiante'];
?>

<div class="container mt-4">
    <h2>Gestión de Usuarios</h2>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
            <?php 
            echo nl2br(htmlspecialchars($_SESSION['message']));
            if (isset($_SESSION['user_credentials'])) {
                echo '<br>Username: ' . htmlspecialchars($_SESSION['user_credentials']['username']);
                echo '<br>Password: ' . htmlspecialchars($_SESSION['user_credentials']['password']);
            }
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            unset($_SESSION['user_credentials']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Botón para crear nuevo usuario -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
        Crear Nuevo Usuario
    </button>

    <!-- Tabla de usuarios -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>DNI</th>
                    <th>Email</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user->getName()); ?></td>
                        <td><?php echo htmlspecialchars($user->getSurname1() . ' ' . $user->getSurname2()); ?></td>
                        <td><?php echo htmlspecialchars($user->getDni()); ?></td>
                        <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                        <td><?php echo htmlspecialchars($user->getUsername()); ?></td>
                        <td><?php echo htmlspecialchars($roles[$user->getRole()] ?? 'Sin rol'); ?></td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary edit-user" 
                                        data-id="<?php echo $user->getId(); ?>"
                                        data-name="<?php echo htmlspecialchars($user->getName()); ?>"
                                        data-surname1="<?php echo htmlspecialchars($user->getSurname1()); ?>"
                                        data-surname2="<?php echo htmlspecialchars($user->getSurname2()); ?>"
                                        data-dni="<?php echo htmlspecialchars($user->getDni()); ?>"
                                        data-email="<?php echo htmlspecialchars($user->getEmail()); ?>"
                                        data-username="<?php echo htmlspecialchars($user->getUsername()); ?>"
                                        data-role="<?php echo htmlspecialchars($user->getRole()); ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUserModal">
                                    Editar
                                </button>

                                <form action="" method="post" class="d-inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $user->getId(); ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este usuario?')">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para crear usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="surname1" class="form-label">Primer Apellido</label>
                        <input type="text" class="form-control" id="surname1" name="surname1" required>
                    </div>
                    <div class="mb-3">
                        <label for="surname2" class="form-label">Segundo Apellido</label>
                        <input type="text" class="form-control" id="surname2" name="surname2">
                    </div>
                    <div class="mb-3">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text" class="form-control" id="dni" name="dni" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol</label>
                        <select class="form-select" id="role" name="role" required>
                            <?php foreach ($roles as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_surname1" class="form-label">Primer Apellido</label>
                        <input type="text" class="form-control" id="edit_surname1" name="surname1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_surname2" class="form-label">Segundo Apellido</label>
                        <input type="text" class="form-control" id="edit_surname2" name="surname2">
                    </div>
                    <div class="mb-3">
                        <label for="edit_dni" class="form-label">DNI</label>
                        <input type="text" class="form-control" id="edit_dni" name="dni" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Rol</label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <?php foreach ($roles as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
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

<style>
.table th {
    background-color: #f8f9fa;
}
.btn-group {
    gap: 0.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Editar usuario
    document.querySelectorAll('.edit-user').forEach(button => {
        button.addEventListener('click', function() {
            const userData = this.dataset;
            document.getElementById('edit_id').value = userData.id;
            document.getElementById('edit_name').value = userData.name;
            document.getElementById('edit_surname1').value = userData.surname1;
            document.getElementById('edit_surname2').value = userData.surname2;
            document.getElementById('edit_dni').value = userData.dni;
            document.getElementById('edit_email').value = userData.email;
            document.getElementById('edit_username').value = userData.username;
            document.getElementById('edit_role').value = userData.role;
        });
    });
});
</script>
