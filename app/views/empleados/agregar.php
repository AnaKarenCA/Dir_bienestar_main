<?php
$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errores'], $_SESSION['old']);
?>

<div class="empleados-container">
    <div class="empleados-header">
        <h2>Agregar empleado</h2>
    </div>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/Dir_bienestar/empleados/guardar" method="POST" class="empleados-form">
        <div class="form-group">
            <label for="nombre">Nombre completo *</label>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="correo">Correo electrónico *</label>
            <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($old['correo'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="puesto">Puesto</label>
            <input type="text" id="puesto" name="puesto" value="<?= htmlspecialchars($old['puesto'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="clave">Contraseña * (mínimo 6 caracteres)</label>
            <input type="password" id="clave" name="clave" required>
        </div>
        <div class="form-group">
            <label for="estatus">Estatus</label>
            <select id="estatus" name="estatus">
                <option value="Activo" <?= (isset($old['estatus']) && $old['estatus'] === 'Activo') ? 'selected' : '' ?>>Activo</option>
                <option value="Inactivo" <?= (isset($old['estatus']) && $old['estatus'] === 'Inactivo') ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        <!-- Rol fijo: Personal (id=3) -->
        <input type="hidden" name="rol_id" value="3">

        <div class="form-actions">
            <button type="submit" class="btn-guardar">
                <span class="material-symbols-outlined">save</span> Guardar
            </button>
            <a href="/Dir_bienestar/empleados" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>

<style>
/* ===== CONTENEDOR PRINCIPAL (estilo vidrio) ===== */
.empleados-container {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 28px;
    padding: 30px 28px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    margin: 20px auto;
    max-width: 800px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.empleados-header {
    border-bottom: 2px solid rgba(128, 0, 0, 0.15);
    padding-bottom: 16px;
    margin-bottom: 24px;
}

.empleados-header h2 {
    color: #800000;
    font-weight: 700;
    font-size: 1.6rem;
    margin: 0;
    letter-spacing: -0.5px;
}

/* ===== ALERTA ===== */
.alert {
    padding: 16px 20px;
    border-radius: 16px;
    margin-bottom: 24px;
    background: rgba(244, 67, 54, 0.08);
    border-left: 4px solid #d32f2f;
    color: #b71c1c;
}

.alert ul {
    padding-left: 20px;
    margin: 0;
}

/* ===== FORMULARIO ===== */
.empleados-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group label {
    font-weight: 600;
    color: #800000;
    font-size: 0.9rem;
    letter-spacing: 0.3px;
}

.form-group input,
.form-group select {
    padding: 12px 16px;
    border: 1px solid rgba(128, 0, 0, 0.15);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(4px);
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #800000;
    box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
    background: rgba(255, 255, 255, 0.8);
}

.form-group input::placeholder {
    color: #aaa;
}

/* ===== BOTONES ===== */
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 8px;
    flex-wrap: wrap;
}

.btn-guardar,
.btn-cancelar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.25s ease;
    border: none;
    cursor: pointer;
}

.btn-guardar {
    background: #800000;
    color: white;
    box-shadow: 0 4px 12px rgba(128, 0, 0, 0.25);
}

.btn-guardar:hover {
    background: #660000;
    transform: scale(1.03);
    box-shadow: 0 6px 20px rgba(128, 0, 0, 0.35);
}

.btn-cancelar {
    background: rgba(0, 0, 0, 0.05);
    color: #555;
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.btn-cancelar:hover {
    background: rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: #333;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 600px) {
    .empleados-container {
        padding: 20px 16px;
        margin: 12px;
    }
    .empleados-header h2 {
        font-size: 1.3rem;
    }
    .form-actions {
        flex-direction: column;
    }
    .btn-guardar,
    .btn-cancelar {
        justify-content: center;
        padding: 14px 20px;
    }
}
</style>