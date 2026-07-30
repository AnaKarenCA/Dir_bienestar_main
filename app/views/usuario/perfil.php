<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | DG Bienestar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #FEF7F0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 24px 28px;
            color: #2C241A;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 28px;
            border-bottom: 2px solid #E6D8C8;
            padding-bottom: 16px;
        }
        .logo-area img {
            height: 70px;
            width: auto;
        }
        .title-area h1 {
            font-size: 1.8rem;
            color: #800000;
            font-weight: 800;
        }
        .title-area p {
            color: #7A5A3A;
            font-weight: 500;
        }

        .perfil-card {
            background: white;
            border-radius: 28px;
            padding: 30px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.04);
            border: 1px solid #EFE4D6;
            margin-bottom: 24px;
        }
        .perfil-card h2 {
            color: #800000;
            font-size: 1.2rem;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #E6D8C8;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .perfil-card h2 .material-symbols-outlined {
            font-size: 1.4rem;
        }

        .campo {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #f0ebe4;
        }
        .campo .label {
            font-weight: 700;
            color: #800000;
            width: 180px;
            flex-shrink: 0;
            font-size: 0.85rem;
        }
        .campo .valor {
            color: #2C241A;
            font-size: 0.9rem;
        }
        .campo .valor .badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
        }
        .badge-activo { background: #27ae60; }
        .badge-inactivo { background: #f39c12; }
        .badge-bloqueado { background: #e74c3c; }

        /* Formularios */
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-weight: 700;
            font-size: 0.8rem;
            color: #800000;
            margin-bottom: 4px;
        }
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 24px;
            border: 1.5px solid #DBCAB2;
            font-size: 0.9rem;
            background: white;
        }
        .form-group input:focus {
            border-color: #800000;
            outline: none;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .btn {
            padding: 10px 28px;
            border-radius: 40px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.15s;
            font-size: 0.85rem;
        }
        .btn-primary {
            background: #800000;
            color: white;
        }
        .btn-primary:hover {
            background: #5a0000;
        }
        .btn-secondary {
            background: #E7DAC8;
            color: #5E3E22;
        }
        .btn-secondary:hover {
            background: #D4C3AB;
        }
        .btn-success {
            background: #27ae60;
            color: white;
        }
        .btn-success:hover {
            background: #1e8449;
        }
        .acciones {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            flex-wrap: wrap;
        }
        .mensaje {
            padding: 10px 16px;
            border-radius: 24px;
            margin-top: 12px;
            font-size: 0.85rem;
            display: none;
        }
        .mensaje.exito {
            background: #d4edda;
            color: #155724;
            display: block;
        }
        .mensaje.error {
            background: #f8d7da;
            color: #721c24;
            display: block;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }
        .modal-overlay.activo { display: flex; }
        .modal-container {
            background: white;
            border-radius: 28px;
            max-width: 500px;
            width: 100%;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: fadeSlide 0.2s ease;
        }
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #E6D8C8;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .modal-header h3 {
            color: #800000;
            font-size: 1.2rem;
        }
        .modal-header .cerrar {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #999;
            cursor: pointer;
        }
        .modal-header .cerrar:hover { color: #800000; }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #eee;
        }

        @media (max-width: 700px) {
            .campo { flex-direction: column; gap: 4px; }
            .campo .label { width: 100%; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<div class="container">
    <div class="header">
        <div class="logo-area">
            <img src="/img/logo_d_bienestar.png" alt="DG Bienestar" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 70%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%23800000%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 fill=%22white%22 text-anchor=%22middle%22%3EDGB%3C/text%3E%3C/svg%3E'">
        </div>
        <div class="title-area">
            <h1>👤 Mi Perfil</h1>
            <p>Administra tus datos personales y seguridad</p>
        </div>
    </div>

    <!-- ======================================== -->
    <!-- SECCIÓN 1: INFORMACIÓN DEL PERFIL (VIEW) -->
    <!-- ======================================== -->
    <div class="perfil-card">
        <h2><span class="material-symbols-outlined">badge</span> Información del perfil</h2>
        <div class="campo">
            <span class="label">Nombre completo</span>
            <span class="valor"><?= htmlspecialchars($usuario['nombre']) ?></span>
        </div>
        <div class="campo">
            <span class="label">Correo electrónico</span>
            <span class="valor"><?= htmlspecialchars($usuario['correo']) ?></span>
        </div>
        <div class="campo">
            <span class="label">Puesto</span>
            <span class="valor"><?= htmlspecialchars($usuario['puesto'] ?? 'No especificado') ?></span>
        </div>
        <div class="campo">
            <span class="label">Rol</span>
            <span class="valor"><?= htmlspecialchars($usuario['tipo_rol']) ?></span>
        </div>
        <div class="campo">
            <span class="label">Unidad administrativa</span>
            <span class="valor"><?= htmlspecialchars($usuario['unidad'] ?? 'Sin asignar') ?></span>
        </div>
        <div class="campo">
            <span class="label">Estado</span>
            <span class="valor">
                <span class="badge badge-<?= strtolower($usuario['estatus']) ?>">
                    <?= $usuario['estatus'] ?>
                </span>
            </span>
        </div>
    </div>

    <!-- ======================================== -->
    <!-- SECCIÓN 2: EDITAR PERFIL -->
    <!-- ======================================== -->
    <div class="perfil-card">
        <h2><span class="material-symbols-outlined">edit</span> Editar perfil</h2>
        <form id="formEditarPerfil">
            <div class="form-group">
                <label>Nombre completo *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label>Correo electrónico *</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
            </div>
            <div class="form-group" style="display:none;">
                <label>Puesto</label>
                <input type="text" name="puesto" value="<?= htmlspecialchars($usuario['puesto'] ?? '') ?>" readonly>
            </div>
            <div id="mensajePerfil" class="mensaje"></div>
            <div class="acciones">
                <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
            </div>
        </form>
    </div>

    <!-- ======================================== -->
    <!-- SECCIÓN 3: SEGURIDAD (CAMBIO DE CONTRASEÑA) -->
    <!-- ======================================== -->
    <div class="perfil-card">
        <h2><span class="material-symbols-outlined">lock</span> Seguridad</h2>
        <p style="color:#666; font-size:0.85rem; margin-bottom:16px;">
            Cambia tu contraseña de acceso. La nueva contraseña debe tener al menos 8 caracteres.
        </p>
        <button class="btn btn-secondary" id="btnAbrirModalPassword">
            🔐 Cambiar contraseña
        </button>
    </div>
</div>

<!-- ======================================== -->
<!-- MODAL: CAMBIAR CONTRASEÑA -->
<!-- ======================================== -->
<div class="modal-overlay" id="modalPassword">
    <div class="modal-container">
        <div class="modal-header">
            <h3>🔐 Cambiar contraseña</h3>
            <button class="cerrar" onclick="cerrarModal()">&times;</button>
        </div>
        <form id="formCambiarPassword">
            <div class="form-group">
                <label>Contraseña actual *</label>
                <input type="password" name="actual" required placeholder="Ingresa tu contraseña actual">
            </div>
            <div class="form-group">
                <label>Nueva contraseña *</label>
                <input type="password" name="nueva" required minlength="8" placeholder="Mínimo 8 caracteres">
            </div>
            <div class="form-group">
                <label>Confirmar nueva contraseña *</label>
                <input type="password" name="confirmar" required placeholder="Repite la nueva contraseña">
            </div>
            <div id="mensajePassword" class="mensaje"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
            </div>
        </form>
    </div>
</div>

<script>
// ============================================================
// EDICIÓN DE PERFIL (AJAX)
// ============================================================
document.getElementById('formEditarPerfil').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }

    const mensaje = document.getElementById('mensajePerfil');
    mensaje.className = 'mensaje';
    mensaje.style.display = 'none';

    try {
        const response = await fetch('/Dir_bienestar/usuario/actualizarPerfil', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            mensaje.className = 'mensaje exito';
            mensaje.textContent = '✅ Perfil actualizado correctamente.';
            mensaje.style.display = 'block';
            // Actualizar nombre en el menú (si está visible)
            const nombreMenu = document.querySelector('.user-name');
            if (nombreMenu) nombreMenu.textContent = data.nombre;
        } else {
            mensaje.className = 'mensaje error';
            mensaje.textContent = '❌ ' + (result.error || 'Error al actualizar');
            mensaje.style.display = 'block';
        }
    } catch (error) {
        mensaje.className = 'mensaje error';
        mensaje.textContent = '❌ Error de conexión. Intenta de nuevo.';
        mensaje.style.display = 'block';
    }
});

// ============================================================
// MODAL CAMBIO DE CONTRASEÑA
// ============================================================
function abrirModal() {
    document.getElementById('modalPassword').classList.add('activo');
    document.getElementById('formCambiarPassword').reset();
    const mensaje = document.getElementById('mensajePassword');
    mensaje.className = 'mensaje';
    mensaje.style.display = 'none';
}

function cerrarModal() {
    document.getElementById('modalPassword').classList.remove('activo');
}

document.getElementById('btnAbrirModalPassword').addEventListener('click', abrirModal);

// Cerrar modal al hacer clic en el overlay
document.getElementById('modalPassword').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

// ============================================================
// CAMBIO DE CONTRASEÑA (AJAX)
// ============================================================
document.getElementById('formCambiarPassword').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }

    const mensaje = document.getElementById('mensajePassword');
    mensaje.className = 'mensaje';
    mensaje.style.display = 'none';

    try {
        const response = await fetch('/Dir_bienestar/usuario/cambiarContrasena', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            mensaje.className = 'mensaje exito';
            mensaje.textContent = '✅ Contraseña cambiada exitosamente.';
            mensaje.style.display = 'block';
            setTimeout(() => cerrarModal(), 1500);
        } else {
            mensaje.className = 'mensaje error';
            mensaje.textContent = '❌ ' + (result.error || 'Error al cambiar contraseña');
            mensaje.style.display = 'block';
        }
    } catch (error) {
        mensaje.className = 'mensaje error';
        mensaje.textContent = '❌ Error de conexión. Intenta de nuevo.';
        mensaje.style.display = 'block';
    }
});
</script>
</body>
</html>