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
        .toast {
            padding: 10px 16px;
            border-radius: 40px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .toast-success { background: #d4edda; color: #155724; }
        .toast-warning { background: #fff3cd; color: #856404; }
        .toast-danger { background: #f8d7da; color: #721c24; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
            min-width: 900px;
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
        .estado-aprobado { background: #27ae60; }
        .estado-fuera_tiempo { background: #e74c3c; }
        .estado-justificado { background: #2e86c1; }
        .estado-pendiente { background: #95a5a6; }

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
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
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
        .btn-accion.info { background: #2e86c1; color: white; }
        .btn-accion.info:hover { background: #1a5276; }

        .justificacion-tooltip {
            cursor: help;
            color: #2980b9;
            font-weight: 600;
        }

        footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.7rem;
            color: #B28B60;
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
            <h1>📋 Revisión de Eventos</h1>
            <p>Aprueba o solicita correcciones a las carpetas entregadas por los empleados</p>
        </div>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <?php 
            $mensajes = [
                'aprobado' => ['text' => '✅ Carpeta aprobada exitosamente.', 'class' => 'toast-success'],
                'correcciones' => ['text' => '🔄 Correcciones solicitadas.', 'class' => 'toast-warning'],
                'rechazado' => ['text' => '⛔ Carpeta rechazada (fuera de tiempo).', 'class' => 'toast-danger'],
                'justificacion_validada' => ['text' => '✅ Justificación validada. El empleado ya puede subir el archivo.', 'class' => 'toast-success'],
            ];
            if (isset($mensajes[$_GET['mensaje']])): 
        ?>
            <div class="toast <?= $mensajes[$_GET['mensaje']]['class'] ?>">
                <?= $mensajes[$_GET['mensaje']]['text'] ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="tabla-container">
        <h2>📂 Carpetas pendientes de revisión</h2>

        <?php if (empty($carpetas)): ?>
            <div class="empty-msg">✅ No hay carpetas pendientes de revisión en tu unidad. ¡Todo está al día!</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Empleado</th>
                        <th>Unidad</th>
                        <th>Fecha entrega</th>
                        <th>Estado</th>
                        <th>Justificación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carpetas as $c): ?>
                        <?php 
                            $estado = $c['estado'];
                            $clase = 'estado-' . $estado;
                            $label = ucfirst(str_replace('_', ' ', $estado));
                            $tieneJustificacion = !empty($c['justificacion_fuera_tiempo']);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($c['actividad_desc'] ?? $c['registro_descripcion'] ?? 'Sin descripción') ?></td>
                            <td><?= htmlspecialchars($c['usuario_nombre']) ?></td>
                            <td><?= htmlspecialchars($c['unidad_nombre'] ?? 'Sin asignar') ?></td>
                            <td><?= date('d/m/Y', strtotime($c['fecha_entrega'])) ?></td>
                            <td><span class="estado-badge <?= $clase ?>"><?= $label ?></span></td>
                            <td>
                                <?php if ($tieneJustificacion): ?>
                                    <span class="justificacion-tooltip" title="<?= htmlspecialchars($c['justificacion_fuera_tiempo']) ?>">
                                        📄 Ver justificación
                                    </span>
                                <?php else: ?>
                                    <span style="color:#999;">Sin justificación</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="acciones-botones">
                                    <!-- Ver detalle -->
                                    <a href="/Dir_bienestar/eventos/ver_carpeta/<?= $c['id'] ?>" 
                                       class="btn-accion primary"
                                       title="Ver detalle completo">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                    <!-- Aprobar -->
                                    <a href="/Dir_bienestar/eventos/aprobar_carpeta/<?= $c['id'] ?>" 
                                       class="btn-accion success" 
                                       onclick="return confirm('¿Aprobar esta carpeta?')"
                                       title="Aprobar">
                                        <span class="material-symbols-outlined">check_circle</span>
                                    </a>
                                    <!-- Solicitar correcciones -->
                                    <a href="/Dir_bienestar/eventos/solicitar_correcciones/<?= $c['id'] ?>" 
                                       class="btn-accion warning"
                                       onclick="return confirm('¿Solicitar correcciones? El empleado deberá subir una nueva versión.')"
                                       title="Solicitar correcciones">
                                        <span class="material-symbols-outlined">edit_note</span>
                                    </a>
                                    <?php if ($estado == 'entregado' && $tieneJustificacion): ?>
                                        <!-- Validar justificación -->
                                        <a href="/Dir_bienestar/eventos/validar_justificacion/<?= $c['id'] ?>" 
                                           class="btn-accion info"
                                           onclick="return confirm('¿Validar esta justificación? El empleado podrá subir el archivo.')"
                                           title="Validar justificación">
                                            <span class="material-symbols-outlined">verified</span>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($estado == 'entregado' && $tieneJustificacion): ?>
                                        <!-- Rechazar (si está fuera de tiempo) -->
                                        <a href="/Dir_bienestar/eventos/rechazar_carpeta/<?= $c['id'] ?>" 
                                           class="btn-accion danger"
                                           onclick="return confirm('¿Rechazar esta carpeta? La justificación no será aceptada.')"
                                           title="Rechazar (no aceptar justificación)">
                                            <span class="material-symbols-outlined">cancel</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <footer>Las carpetas en estado "Entregado" esperan revisión; en "Revisado" esperan nueva versión.</footer>
</div>
</body>
</html>