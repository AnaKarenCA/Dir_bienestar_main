<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisión de Eventos | DG Bienestar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: #FEF7F0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 24px 28px;
            color: #2C241A;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 28px;
            border-bottom: 2px solid #E6D8C8;
            padding-bottom: 16px;
        }
        .logo-area img { height: 70px; width: auto; }
        .title-area h1 {
            font-size: 1.8rem;
            color: #800000;
            font-weight: 800;
        }
        .title-area p { color: #7A5A3A; font-weight: 500; }

        .tabla-container {
            background: white;
            border-radius: 28px;
            padding: 20px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.04);
            overflow-x: auto;
        }
        .tabla-container h2 {
            color: #800000;
            margin-bottom: 16px;
            font-size: 1.2rem;
        }
        .empty-msg { text-align: center; padding: 40px; color: #AB8E66; font-size: 0.9rem; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
            min-width: 950px;
        }
        th {
            background: #800000;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }
        td {
            border-bottom: 1px solid #EDE0D2;
            padding: 8px;
            vertical-align: middle;
        }
        tr:hover td { background: #FCF5EA; }

        .estado-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 40px;
            font-size: 0.65rem;
            font-weight: 700;
            color: white;
            white-space: nowrap;
        }
        .estado-entregado { background: #f39c12; }
        .estado-revisado { background: #9b59b6; }
        .estado-fuera_tiempo { background: #e74c3c; }
        .estado-aprobado { background: #27ae60; }
        .estado-justificado { background: #2e86c1; }
        .estado-pendiente { background: #6B7280; }

        .acciones-botones {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        .btn-accion {
            background: #E7DAC8;
            border: none;
            padding: 4px 8px;
            border-radius: 40px;
            font-size: 0.65rem;
            font-weight: 600;
            color: #5E3E22;
            cursor: pointer;
            transition: 0.1s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            margin: 2px 2px;
        }
        .btn-accion:hover { background: #D4C3AB; }
        .btn-accion .material-symbols-outlined { font-size: 1rem; }
        .btn-accion.success { background: #27ae60; color: white; }
        .btn-accion.success:hover { background: #1e8449; }
        .btn-accion.warning { background: #f39c12; color: white; }
        .btn-accion.warning:hover { background: #d68910; }
        .btn-accion.danger { background: #e74c3c; color: white; }
        .btn-accion.danger:hover { background: #c0392b; }
        .btn-accion.primary { background: #800000; color: white; }
        .btn-accion.primary:hover { background: #5a0000; }

        .justificacion-celda {
            max-width: 200px;
            white-space: normal;
            word-break: break-word;
        }
        .justificacion-texto {
            display: inline-block;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
        }
        .justificacion-boton {
            background: none;
            border: none;
            cursor: pointer;
            color: #2980b9;
            font-size: 0.9rem;
            padding: 0 4px;
            vertical-align: middle;
        }
        .justificacion-boton:hover { color: #1a5276; }

        footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.7rem;
            color: #B28B60;
        }
        @media (max-width: 650px) {
            .container { padding: 0 10px; }
            table { font-size: 0.7rem; min-width: 600px; }
            .btn-accion { padding: 2px 6px; font-size: 0.6rem; }
        }

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
            max-width: 600px;
            width: 100%;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: fadeSlide 0.2s ease;
            max-height: 80vh;
            overflow-y: auto;
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
        .modal-header h3 { color: #800000; font-size: 1.2rem; }
        .modal-header .cerrar {
            background: none; border: none;
            font-size: 1.5rem; color: #999; cursor: pointer;
        }
        .modal-header .cerrar:hover { color: #800000; }
        .modal-body {
            font-size: 0.9rem;
            line-height: 1.5;
            color: #2C241A;
        }
        .modal-body p {
            background: #f8f4ef;
            padding: 16px;
            border-radius: 12px;
            border-left: 4px solid #800000;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #eee;
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
        .btn-primary { background: #800000; color: white; }
        .btn-primary:hover { background: #5a0000; }
        .btn-secondary { background: #E7DAC8; color: #5E3E22; }
        .btn-secondary:hover { background: #D4C3AB; }
        .mensaje { padding: 10px 16px; border-radius: 24px; margin-top: 12px; font-size: 0.85rem; display: none; }
        .mensaje.exito { background: #d4edda; color: #155724; display: block; }
        .mensaje.error { background: #f8d7da; color: #721c24; display: block; }
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
            <h1>Revisión de Eventos</h1>
            <p>Gestiona las carpetas entregadas por los empleados para su aprobación o corrección</p>
        </div>
    </div>

    <div class="tabla-container">
        <h2>Pendientes de revisión</h2>

        <?php if (empty($carpetas)): ?>
            <div class="empty-msg">📭 No hay carpetas pendientes de revisión.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Responsable</th>
                        <th>Fecha entrega</th>
                        <th>Estado</th>
                        <th>Justificación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($carpetas as $carpeta): 
    $fechaLimite = !empty($carpeta['fecha_fin']) ? $carpeta['fecha_fin'] : $carpeta['fecha_inicio'];
    $subidoFueraTiempo = false;
    if (!empty($carpeta['fecha_entrega']) && !empty($fechaLimite)) {
        $entregaObj = new DateTime($carpeta['fecha_entrega']);
        $limiteObj = new DateTime($fechaLimite);
        if ($entregaObj > $limiteObj) {
            $subidoFueraTiempo = true;
        }
    }
    $estadoMostrar = $subidoFueraTiempo ? 'fuera_tiempo' : $carpeta['estado'];
    $badgeClass = 'estado-' . $estadoMostrar;
    $badgeLabel = ($estadoMostrar == 'fuera_tiempo') ? 'Fuera de tiempo' : ucfirst($carpeta['estado']);

    $justificacion = $carpeta['justificacion_fuera_tiempo'] ?? '';
    $tieneJustificacion = !empty(trim($justificacion));
    $textoMostrar = $tieneJustificacion ? $justificacion : 'El empleado no proporcionó justificación';
?>
<tr>
    <td><?= htmlspecialchars($carpeta['actividad_desc'] ?? '') ?></td>
    <td><?= htmlspecialchars($carpeta['usuario_nombre'] ?? '') ?></td>
    <td><?= date('d/m/Y', strtotime($carpeta['fecha_entrega'])) ?></td>
    <td><span class="estado-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
    <td class="justificacion-celda">
        <?php if ($tieneJustificacion): ?>
            <span class="justificacion-texto" title="<?= htmlspecialchars($textoMostrar) ?>">
                <?= htmlspecialchars(substr($textoMostrar, 0, 60)) . (strlen($textoMostrar) > 60 ? '…' : '') ?>
            </span>
            <button class="justificacion-boton" onclick="verJustificacion('<?= htmlspecialchars($textoMostrar) ?>')" title="Ver justificación completa">
                <span class="material-symbols-outlined" style="font-size:1rem;">description</span>
            </button>
        <?php else: ?>
            <span style="color:#999;"><?= htmlspecialchars($textoMostrar) ?></span>
        <?php endif; ?>
    </td>
    <td>
        <div class="acciones-botones">
            <!-- Ver evidencia (si existe) -->
            <?php if (!empty($carpeta['firma'])): ?>
                <!-- 🔥 CORRECCIÓN: ruta pública absoluta -->
                <a href="/<?= htmlspecialchars($carpeta['firma']) ?>" class="btn-accion" title="Ver evidencia" target="_blank">
                    <span class="material-symbols-outlined">visibility</span>
                </a>
            <?php else: ?>
                <a href="/Dir_bienestar/eventos/ver_carpeta/<?= $carpeta['id'] ?>" class="btn-accion" title="Ver detalles">
                    <span class="material-symbols-outlined">visibility</span>
                </a>
            <?php endif; ?>

            <!-- Aprobar -->
            <?php if ($carpeta['estado'] != 'aprobado' && $carpeta['estado'] != 'revisado'): ?>
                <a href="/Dir_bienestar/eventos/aprobar_carpeta/<?= $carpeta['id'] ?>" class="btn-accion success" onclick="return confirm('¿Aprobar esta carpeta?')">
                    <span class="material-symbols-outlined">check_circle</span>
                </a>
            <?php endif; ?>

            <!-- Corregir -->
            <?php if ($carpeta['estado'] != 'revisado' && $carpeta['estado'] != 'aprobado'): ?>
                <a href="/Dir_bienestar/eventos/solicitar_correcciones/<?= $carpeta['id'] ?>" class="btn-accion warning" onclick="return confirm('¿Solicitar correcciones?')">
                    <span class="material-symbols-outlined">edit</span>
                </a>
            <?php endif; ?>

            <!-- Validar justificación -->
            <?php if ($carpeta['estado'] == 'fuera_tiempo' && $tieneJustificacion): ?>
                <a href="/Dir_bienestar/eventos/validar_justificacion/<?= $carpeta['id'] ?>" class="btn-accion primary" onclick="return confirm('¿Validar justificación? El empleado podrá subir el archivo.')">
                    <span class="material-symbols-outlined">verified</span>
                </a>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top:12px; font-size:0.8rem; color:#888;">
                Las carpetas en estado <strong>"Entregado"</strong> esperan tu revisión. 
                Las que están en <strong style="color:#e74c3c;">"Fuera de tiempo"</strong> tienen justificación pendiente de validar.
            </p>
        <?php endif; ?>
    </div>

</div>

<!-- Modal justificación -->
<div class="modal-overlay" id="modalJustificacion">
    <div class="modal-container">
        <div class="modal-header">
            <h3>📄 Justificación completa</h3>
            <button class="cerrar" onclick="cerrarModal('modalJustificacion')">&times;</button>
        </div>
        <div class="modal-body">
            <p id="justificacionCompleta"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modalJustificacion')">Cerrar</button>
        </div>
    </div>
</div>

<script>
function verJustificacion(texto) {
    document.getElementById('justificacionCompleta').textContent = texto;
    document.getElementById('modalJustificacion').classList.add('activo');
}
function cerrarModal(id) {
    document.getElementById(id).classList.remove('activo');
}
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('activo');
    });
});
</script>
</body>
</html>