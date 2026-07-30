<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos | DG Bienestar</title>
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

        /* Tarjetas de tipos */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card-tipo {
            background: white;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border: 2px solid transparent;
            transition: 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: #2C241A;
        }
        .card-tipo:hover {
            border-color: #800000;
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(128,0,0,0.12);
        }
        .card-tipo.active {
            border-color: #800000;
            background: #FFF4E6;
        }
        .card-tipo .icono { font-size: 2.5rem; display: block; }
        .card-tipo .nombre { font-weight: 700; font-size: 1.1rem; margin: 10px 0 4px; }
        .card-tipo .badge {
            background: #800000; color: white;
            padding: 2px 12px; border-radius: 40px;
            font-size: 0.8rem; display: inline-block;
        }

        /* Tabla */
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

        /* Estados - Semáforo */
        .estado-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 40px;
            font-size: 0.65rem;
            font-weight: 700;
            color: white;
            white-space: nowrap;
        }
        .estado-pendiente { background: #6B7280; }
        .estado-elaboracion { background: #3498db; }
        .estado-entregado { background: #f39c12; }
        .estado-revisado { background: #9b59b6; }
        .estado-aprobado { background: #27ae60; }
        .estado-fuera_tiempo { background: #e74c3c; }
        .estado-justificado { background: #2e86c1; }

        /* Evidencia */
        .evidencia-link {
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
        }
        .evidencia-link:hover { text-decoration: underline; }

        /* Botones de acción */
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
        .btn-accion.primary { background: #800000; color: white; }
        .btn-accion.primary:hover { background: #5a0000; }
        .btn-accion.success { background: #27ae60; color: white; }
        .btn-accion.success:hover { background: #1e8449; }
        .btn-accion.warning { background: #f39c12; color: white; }
        .btn-accion.warning:hover { background: #d68910; }
        .btn-accion.danger { background: #e74c3c; color: white; }
        .btn-accion.danger:hover { background: #c0392b; }
        .btn-accion:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.7rem;
            color: #B28B60;
        }

        @media (max-width: 650px) {
            .cards-grid { grid-template-columns: 1fr 1fr; }
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
        .modal-header h3 { color: #800000; font-size: 1.2rem; }
        .modal-header .cerrar {
            background: none; border: none;
            font-size: 1.5rem; color: #999; cursor: pointer;
        }
        .modal-header .cerrar:hover { color: #800000; }
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-weight: 700;
            font-size: 0.8rem;
            color: #800000;
            margin-bottom: 4px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border-radius: 24px;
            border: 1.5px solid #DBCAB2;
            font-size: 0.9rem;
            background: white;
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-group input:focus, .form-group textarea:focus {
            border-color: #800000;
            outline: none;
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
            <h1>📋 Mis Eventos</h1>
            <p>Selecciona un tipo de entregable y gestiona el ciclo de vida de tus evidencias</p>
        </div>
    </div>

    <!-- Tarjetas de tipos -->
    <div class="cards-grid">
        <?php foreach ($tipos as $tipo): ?>
            <?php 
                $count = $conteos[$tipo['id']] ?? 0;
                $active = ($tipoSeleccionado == $tipo['id']) ? 'active' : '';
                $iconMap = [
                    'Carpeta' => 'folder',
                    'Oficio' => 'docs',
                    'Ficha Técnica' => 'content_paste',
                    'No aplica' => 'attach_file_off'
                ];
                $iconName = $iconMap[$tipo['nombre_entregable']] ?? 'folder';
            ?>
            <a href="/Dir_bienestar/eventos/index?tipo=<?= $tipo['id'] ?>" class="card-tipo <?= $active ?>">
                <span class="material-symbols-outlined icono"><?= $iconName ?></span>
                <div class="nombre"><?= htmlspecialchars($tipo['nombre_entregable']) ?></div>
                <div class="badge"><?= $count ?> registros</div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Tabla -->
    <div class="tabla-container">
        <h2>
            <?php if ($tipoSeleccionado): ?>
                Registros de tipo: <strong><?= htmlspecialchars($tipos[array_search($tipoSeleccionado, array_column($tipos, 'id'))]['nombre_entregable'] ?? '') ?></strong>
            <?php else: ?>
                Selecciona un tipo de entregable para ver los registros
            <?php endif; ?>
        </h2>

        <?php if ($tipoSeleccionado && empty($registros)): ?>
            <div class="empty-msg">📭 No hay registros con este tipo de entregable <?= (!$esAdmin && $unidadId) ? 'para tu unidad administrativa' : '' ?>.</div>
        <?php elseif ($tipoSeleccionado && !empty($registros)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Beneficiarios</th>
                        <th>Estado</th>
                        <th>Evidencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $reg): ?>
                        <?php 
                            $estado = $reg['carpeta_estado'] ?? 'pendiente';
                            $carpetaId = $reg['carpeta_id'] ?? null;
                            $fechaFin = $reg['fecha_fin'] ?? $reg['fecha_inicio'];
                            $fechaEntrega = $reg['fecha_entrega'] ?? null;

                            // --------------------------------------------------------------
                            // NUEVA REGLA: detectar si la evidencia se subió fuera de tiempo
                            // --------------------------------------------------------------
                            $subidoFueraTiempo = false;
                            if (!empty($fechaEntrega) && !empty($fechaFin)) {
                                $entregaObj = new DateTime($fechaEntrega);
                                $finObj = new DateTime($fechaFin);
                                if ($entregaObj > $finObj) {
                                    $subidoFueraTiempo = true;
                                }
                            }

                            // Asignar badge según la nueva regla
                            if ($subidoFueraTiempo) {
                                // SIEMPRE rojo, sin importar el estado
                                $badgeClass = 'estado-fuera_tiempo';
                                $badgeLabel = 'Fuera de tiempo';
                            } else {
                                // Color según estado actual
                                $badgeClass = 'estado-' . $estado;
                                $badgeLabel = ucfirst(str_replace('_', ' ', $estado));
                            }

                            // Evidencia
                            $tieneEvidencia = !empty($reg['firma']);
                            $evidenciaNombre = $tieneEvidencia ? '📄 ' . basename($reg['firma']) : 'Sin archivo';
                            $evidenciaLink = $tieneEvidencia ? htmlspecialchars($reg['firma']) : '#';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($reg['actividad_desc'] ?? 'Sin actividad') ?></td>
                            <td><?= $reg['fecha_inicio'] ?></td>
                            <td><?= htmlspecialchars($reg['lugar_nombre'] ?? '') ?></td>
                            <td><?= $reg['beneficiarios_asistentes'] ?></td>
                            <td>
                                <span class="estado-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                <?php if ($estado == 'fuera_tiempo' && !empty($reg['justificacion_fuera_tiempo'])): ?>
                                    <span title="Justificación: <?= htmlspecialchars($reg['justificacion_fuera_tiempo']) ?>" style="cursor:help; font-size:0.7rem;">ℹ️</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($tieneEvidencia): ?>
                                    <a href="<?= $evidenciaLink ?>" class="evidencia-link" target="_blank"><?= $evidenciaNombre ?></a>
                                    <span style="font-size:0.6rem; color:#888; display:block;">Subido: <?= date('d/m/Y', strtotime($reg['fecha_entrega'] ?? 'now')) ?></span>
                                <?php else: ?>
                                    <span style="color:#999;"><?= $evidenciaNombre ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="acciones-botones">
                                    <?php if ($reg['tipo_entregable_id'] == 1): ?>
                                        <!-- Botón Editar SIEMPRE visible para carpeta -->
                                        <a href="/Dir_bienestar/eventos/editar_carpeta?id_registro=<?= $reg['id'] ?>"
                                           class="btn-accion" title="Editar carpeta">
                                            <span class="material-symbols-outlined">edit</span>
                                        </a>
                                        
                                        <?php if ($estado == 'pendiente' || $estado == 'elaboracion'): ?>
                                            <a href="/Dir_bienestar/evento_ppt/generar?id_registro=<?= $reg['id'] ?>" class="btn-accion primary" title="Generar plantilla">
                                                <span class="material-symbols-outlined">description</span>
                                            </a>
                                            <button class="btn-accion success" 
                                                    onclick="abrirModalSubida(<?= $reg['id'] ?>, '<?= $estado ?>', '<?= $fechaFin ?>')" 
                                                    title="Subir evidencia">
                                                <span class="material-symbols-outlined">upload_file</span>
                                            </button>
                                        <?php elseif ($estado == 'entregado'): ?>
                                            <a href="#" class="btn-accion" title="Ver entrega">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </a>
                                            <a href="#" class="btn-accion" title="Historial">
                                                <span class="material-symbols-outlined">history</span>
                                            </a>
                                        <?php elseif ($estado == 'revisado'): ?>
                                            <a href="#" class="btn-accion warning" title="Ver observaciones">
                                                <span class="material-symbols-outlined">comment</span>
                                            </a>
                                            <button class="btn-accion success" onclick="abrirModalSubida(<?= $reg['id'] ?>, '<?= $estado ?>', '<?= $fechaFin ?>')" title="Subir nueva versión">
                                                <span class="material-symbols-outlined">upload_file</span>
                                            </button>
                                        <?php elseif ($estado == 'aprobado'): ?>
                                            <a href="#" class="btn-accion" title="Ver documento">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </a>
                                            <a href="#" class="btn-accion primary" title="Descargar">
                                                <span class="material-symbols-outlined">download</span>
                                            </a>
                                        <?php elseif ($estado == 'fuera_tiempo'): ?>
                                            <button class="btn-accion danger" onclick="abrirModalSubida(<?= $reg['id'] ?>, '<?= $estado ?>', '<?= $fechaFin ?>')" title="Subir evidencia (fuera de tiempo)">
                                                <span class="material-symbols-outlined">upload_file</span>
                                            </button>
                                        <?php elseif ($estado == 'justificado'): ?>
                                            <button class="btn-accion success" onclick="abrirModalSubida(<?= $reg['id'] ?>, '<?= $estado ?>', '<?= $fechaFin ?>')" title="Subir evidencia (justificación aprobada)">
                                                <span class="material-symbols-outlined">upload_file</span>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-msg">👆 Selecciona un tipo de entregable para comenzar.</div>
        <?php endif; ?>
    </div>

    <footer>Los eventos mostrados corresponden a tu unidad administrativa. Los administradores ven todos los registros.</footer>
</div>

<!-- ======================================== -->
<!-- MODAL DE SUBIDA DE EVIDENCIA / JUSTIFICACIÓN -->
<!-- ======================================== -->
<div class="modal-overlay" id="modalSubida">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="modalSubidaTitle">📤 Subir evidencia</h3>
            <button class="cerrar" onclick="cerrarModalSubida()">&times;</button>
        </div>
        <form id="formSubida" enctype="multipart/form-data">
            <input type="hidden" name="registro_id" id="subidaRegistroId">
            <input type="hidden" name="estado_actual" id="subidaEstadoActual">
            <input type="hidden" name="fecha_fin" id="subidaFechaFin">
            
            <!-- Campo archivo (siempre visible y obligatorio) -->
            <div class="form-group" id="archivoGroup">
                <label>Archivo *</label>
                <input type="file" name="archivo" accept=".pdf,.doc,.docx,.pptx" required>
            </div>
            
            <!-- Campo justificación (visible solo si fuera de tiempo) -->
            <div class="form-group" id="justificacionGroup" style="display:none;">
                <label>Justificación (fuera de tiempo) *</label>
                <textarea name="justificacion" rows="3" placeholder="Explique por qué la entrega se realiza después del plazo..."></textarea>
                <small style="color:#888; font-size:0.7rem;">La justificación será revisada por tu jefe.</small>
            </div>
            
            <div id="mensajeSubida" class="mensaje"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModalSubida()">Cancelar</button>
                <button type="submit" class="btn btn-primary">📤 Enviar</button>
            </div>
        </form>
    </div>
</div>

<script>
// ============================================================
// MODAL DE SUBIDA (SIEMPRE REQUIERE ARCHIVO)
// ============================================================
function abrirModalSubida(registroId, estadoActual, fechaFin) {
    document.getElementById('subidaRegistroId').value = registroId;
    document.getElementById('subidaEstadoActual').value = estadoActual;
    document.getElementById('subidaFechaFin').value = fechaFin;
    document.getElementById('formSubida').reset();
    document.getElementById('mensajeSubida').className = 'mensaje';
    document.getElementById('mensajeSubida').style.display = 'none';
    
    const archivoGroup = document.getElementById('archivoGroup');
    const justificacionGroup = document.getElementById('justificacionGroup');
    const modalTitle = document.getElementById('modalSubidaTitle');
    const archivoInput = archivoGroup.querySelector('input');
    const justificacionInput = justificacionGroup.querySelector('textarea');
    
    // Siempre mostrar archivo y hacerlo obligatorio
    archivoGroup.style.display = 'block';
    archivoInput.required = true;
    
    const hoy = new Date();
    const fin = new Date(fechaFin);
    const diffTime = Math.abs(hoy - fin);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const esFueraTiempo = (diffDays > 3 && hoy > fin);

    // Mostrar justificación solo si está fuera de tiempo
    if (estadoActual === 'fuera_tiempo' || (estadoActual !== 'justificado' && esFueraTiempo)) {
        justificacionGroup.style.display = 'block';
        justificacionInput.required = true;
        modalTitle.textContent = '📤 Subir evidencia (fuera de tiempo)';
    } else {
        justificacionGroup.style.display = 'none';
        justificacionInput.required = false;
        justificacionInput.value = '';
        if (estadoActual === 'justificado') {
            modalTitle.textContent = '📤 Subir evidencia (justificación aprobada)';
        } else {
            modalTitle.textContent = '📤 Subir evidencia';
        }
    }

    document.getElementById('modalSubida').classList.add('activo');
}

function cerrarModalSubida() {
    document.getElementById('modalSubida').classList.remove('activo');
}

// Cerrar modal al hacer clic en overlay
document.getElementById('modalSubida').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalSubida();
});

// ============================================================
// ENVÍO DEL FORMULARIO (AJAX) - SIEMPRE REQUIERE ARCHIVO
// ============================================================
document.getElementById('formSubida').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const mensaje = document.getElementById('mensajeSubida');
    mensaje.className = 'mensaje';
    mensaje.style.display = 'none';

    // Validar archivo (siempre obligatorio)
    const archivo = formData.get('archivo');
    if (!archivo || archivo.size === 0) {
        mensaje.className = 'mensaje error';
        mensaje.textContent = '❌ Debes seleccionar un archivo.';
        mensaje.style.display = 'block';
        return;
    }

    // Validar justificación solo si el campo está visible
    const justificacionGroup = document.getElementById('justificacionGroup');
    if (justificacionGroup.style.display !== 'none') {
        const justificacion = formData.get('justificacion')?.trim();
        if (!justificacion) {
            mensaje.className = 'mensaje error';
            mensaje.textContent = '❌ Debes escribir una justificación para la entrega fuera de tiempo.';
            mensaje.style.display = 'block';
            return;
        }
    }

    try {
        const response = await fetch('/Dir_bienestar/eventos/subir_evidencia', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            mensaje.className = 'mensaje exito';
            mensaje.textContent = result.mensaje || '✅ Enviado correctamente. Recargando...';
            mensaje.style.display = 'block';
            setTimeout(() => location.reload(), 1500);
        } else {
            mensaje.className = 'mensaje error';
            mensaje.textContent = '❌ ' + (result.error || 'Error al enviar');
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