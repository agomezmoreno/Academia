<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-key"></i> Cambiar Contraseña
                    </h3>
                </div>
                <div class="card-body">
                    <?php if ($error !== null): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" id="changePasswordForm" class="needs-validation" novalidate>
                        <?php if (!$_SESSION['first_login']): ?>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password" 
                                       name="new_password" 
                                       required 
                                       minlength="6" 
                                       placeholder="Mínimo 6 caracteres">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('new_password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                La contraseña debe tener al menos 6 caracteres
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required 
                                       minlength="6" 
                                       placeholder="Repita la contraseña">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('confirm_password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Las contraseñas deben coincidir
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Actualizar Contraseña
                            </button>
                            <a href="/academia1/profile" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver al Perfil
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        button.classList.remove('bi-eye');
        button.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        button.classList.remove('bi-eye-slash');
        button.classList.add('bi-eye');
    }
}

// Validación del formulario
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        document.getElementById('confirm_password').setCustomValidity('Las contraseñas no coinciden');
    } else {
        document.getElementById('confirm_password').setCustomValidity('');
    }
    
    this.classList.add('was-validated');
});
</script>
