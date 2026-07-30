<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Carpeta | DG Bienestar</title>
    <!-- CSS externo (el tuyo ya contiene los estilos del presídium) -->
    <link rel="stylesheet" href="/css/editar_carpeta.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        /* ============================================================
           ESTILOS ADICIONALES PARA EL PRESÍDIUM (POR SI ACASO)
           ============================================================ */
        .presidium-controls {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .counter {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .presidium-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .presidium-canvas-panel {
            flex: 1;
            min-width: 250px;
            background: #fff;
            border-radius: 16px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .presidium-list-panel {
            flex: 1;
            min-width: 300px;
            background: #fff;
            border-radius: 16px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .presidium-visual {
            position: relative;
            width: 100%;
            height: 240px;
            border: 1px dashed rgba(128,0,0,0.2);
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
        }
        .presidium-spot {
            position: absolute;
            transform: translate(-50%, -50%);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #800000;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            z-index: 10;
            cursor: default;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .presidium-spot.center-spot {
            width: 34px;
            height: 34px;
            background: #000;
            font-size: 14px;
        }
        .presidium-shape-black {
            pointer-events: none;
        }
        .presidium-row-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            padding: 8px;
            background: #fdfaf5;
            border-radius: 12px;
            border: 1px solid #EDE0D2;
        }
        .presidium-row-item .spot-tag {
            background: #800000;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<div class="container">
    <!-- HEADER -->
    <div class="header">
        <h1><span class="material-symbols-outlined">folder</span> Editar Carpeta</h1>
        <div class="status-badge status-<?= $carpeta['estado'] ?? 'pendiente' ?>">
            Estado: <?= ucfirst(str_replace('_', ' ', $carpeta['estado'] ?? 'pendiente')) ?>
        </div>
    </div>

    <form id="carpetaForm" enctype="multipart/form-data">
        <input type="hidden" name="carpeta_id" value="<?= $carpeta['id'] ?? '' ?>">
        <input type="hidden" name="registro_actividad_id" value="<?= $registro['id'] ?? '' ?>">
        <input type="hidden" name="evento_id" value="<?= $evento['id'] ?? '' ?>">
        <input type="hidden" name="estado" value="<?= $carpeta['estado'] ?? 'pendiente' ?>">

        <!-- ===== D1. PORTADA ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">description</span>Portada</div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Nombre de la Dirección que realiza el evento *</label>
                    <input type="text" name="direccion_nombre" value="<?= htmlspecialchars($registro['unidad_nombre'] ?? '') ?>" readonly>
                </div>
                <div class="form-group full-width">
                    <label>Nombre del Evento *</label>
                    <input type="text" name="nombre_evento" value="<?= htmlspecialchars($evento['nombre_evento'] ?? '') ?>" placeholder="colocar el nombre del evento" required>
                </div>
                <div class="form-group">
                    <label>Aprobado por</label>
                    <input type="text" name="aprobado_por" value="Mtra. Andrea Ma. Del Rocío Merlos Nájera" readonly>
                </div>
                <div class="form-group">
                    <label>Responsable del Evento</label>
                    <input type="text" name="responsable_evento" value="<?= htmlspecialchars($responsable_solo_nombre ?? '') ?>" placeholder="Ej: Mtro. Omar Ruiz Castillo">
                </div>
                <div class="form-group">
                    <label>Fecha de entrega</label>
                    <input type="date" name="fecha_entrega" value="<?= $carpeta['fecha_entrega'] ?? date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>Fecha del evento</label>
                    <input type="date" name="fecha_evento" id="main-date" value="<?= $evento['fecha_evento'] ?? $registro['fecha_inicio'] ?? '' ?>" readonly>
                </div>
                <div class="form-group full-width">
                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; align-items: center;">
                        <label style="margin:0;">Imagen de fondo (Diseño para el evento)</label>
                        <label class="checkbox-inline" style="margin:0;">
                            <input type="checkbox" id="bg-in-progress" onchange="toggleBgInput(this.checked)"> Marcar como En Proceso (No editable)
                        </label>
                    </div>
                    <div class="file-input-wrapper" id="bg-wrapper">
                        <span style="display:block; margin-bottom:6px;">Seleccionar diseño del evento...</span>
                        <span class="file-label" onclick="document.getElementById('bg-file').click()">Seleccionar archivo</span>
                        <input type="file" id="bg-file" name="imagen_fondo" accept="image/*" style="display:none;">
                        <?php if (!empty($evento['imagen_fondo'])): ?>
                            <div style="margin-top:6px;"><img src="<?= htmlspecialchars($evento['imagen_fondo']) ?>" style="max-height:60px; border-radius:4px;"></div>
                            <input type="hidden" name="imagen_fondo_actual" value="<?= htmlspecialchars($evento['imagen_fondo']) ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== D2. OBJETIVO Y BENEFICIARIOS ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">target</span>Objetivo y Beneficiarios</div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Línea de acción PbRM</label>
                    <input type="text" name="descripcion_meta" value="<?= htmlspecialchars($evento['descripcion_meta'] ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Objetivo del PbRM</label>
                    <input type="text" name="objetivo_pbrm" value="<?= htmlspecialchars($objetivo_pbrm ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Objetivo del Evento</label>
                    <input type="text" name="objetivo_evento" value="<?= htmlspecialchars($evento['objetivo_evento'] ?? '') ?>" placeholder="Propósito específico">
                </div>
                <div class="form-group">
                    <label>Número de beneficiarios</label>
                    <input type="number" name="beneficiarios" value="<?= $registro['beneficiarios_asistentes'] ?? 0 ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Fecha del evento (resumen)</label>
                    <input type="date" id="resumen-date" value="<?= $registro['fecha_inicio'] ?? '' ?>" readonly>
                </div>
            </div>
        </div>

        <!-- ===== D3. JUSTIFICACIÓN ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">edit_note</span>Justificación</div>
            <div class="form-group full-width">
                <label>Justificación e impacto del evento</label>
                <textarea name="justificacion" rows="4"><?= htmlspecialchars($evento['justificacion'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- ===== D4. GENERALES DEL EVENTO ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">info</span>Generales del Evento</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" id="gen-date" value="<?= $registro['fecha_inicio'] ?? '' ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Hora inicio</label>
                    <input type="time" id="start-time" value="<?= substr($registro['hora_inicio'] ?? '', 0, 5) ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Lugar</label>
                    <input type="text" value="<?= htmlspecialchars($registro['lugar_nombre'] ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Vestimenta</label>
                    <select name="vestimenta">
                        <option value="Formal" <?= ($evento['vestimenta'] ?? '') == 'Formal' ? 'selected' : '' ?>>Formal</option>
                        <option value="Casual" <?= ($evento['vestimenta'] ?? '') == 'Casual' ? 'selected' : '' ?>>Casual</option>
                        <option value="Formal-Casual" <?= ($evento['vestimenta'] ?? '') == 'Formal-Casual' ? 'selected' : '' ?>>Formal-Casual</option>
                        <option value="Deportiva" <?= ($evento['vestimenta'] ?? '') == 'Deportiva' ? 'selected' : '' ?>>Deportiva</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Duración del Evento límite configurada (min)</label>
                    <input type="number" id="limit-duration" value="540" readonly>
                </div>
                <div class="form-group">
                    <label>Coordinación del Evento</label>
                    <input type="text" name="coordinacion_evento" value="<?= htmlspecialchars($evento['coordinacion_evento'] ?? '') ?>" placeholder="Nombre del coordinador">
                </div>
                <div class="form-group full-width">
                    <label>Responsable del Evento</label>
                    <input type="text" value="<?= htmlspecialchars($responsable_solo_nombre ?? '') ?>" readonly>
                </div>
            </div>
        </div>

        <!-- ===== D5. UBICACIÓN DEL EVENTO ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">location_on</span>Ubicación del Evento</div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Dirección</label>
                    <input type="text" name="direccion_entrega" value="<?= htmlspecialchars($carpeta['direccion_entrega'] ?? '') ?>" placeholder="Calle, número, colonia">
                </div>
                <div class="form-group full-width">
                    <label>Link de Google Maps</label>
                    <input type="url" name="link_mapa" value="<?= htmlspecialchars($evento['link_mapa'] ?? $carpeta['link_mapa'] ?? '') ?>" placeholder="https://maps.google.com/...">
                </div>
                <div class="form-group">
                    <label>Foto del lugar</label>
                    <input type="file" name="imagen_lugar" accept="image/*">
                    <?php if (!empty($evento['imagen_lugar'])): ?>
                        <div style="margin-top:4px;"><img src="<?= htmlspecialchars($evento['imagen_lugar']) ?>" style="max-height:60px;"></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Foto Google Maps</label>
                    <input type="file" name="imagen_maps" accept="image/*">
                    <?php if (!empty($evento['imagen_maps'])): ?>
                        <div style="margin-top:4px;"><img src="<?= htmlspecialchars($evento['imagen_maps']) ?>" style="max-height:60px;"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ===== D6. ORDEN DEL DÍA ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">schedule</span>Orden del Día - Programa Protocolario</div>
            <div id="ordenContainer">
                <table id="ordenTable">
                    <thead>
                        <tr>
                            <th style="width:15%;">Hora Inicio</th>
                            <th style="width:35%;">Actividad</th>
                            <th style="width:25%;">Responsable</th>
                            <th style="width:15%;">Duración (min)</th>
                            <th style="width:10%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ordenBody">
                        <?php if (!empty($ordenes)): ?>
                            <?php foreach ($ordenes as $idx => $o): ?>
                                <tr>
                                    <td><input type="text" class="row-start" name="orden[<?= $idx ?>][hora_inicio]" readonly style="background:#eee; width:100%;" value="<?= substr($o['hora_inicio'] ?? '', 0, 5) ?>"></td>
                                    <td><input type="text" name="orden[<?= $idx ?>][actividad]" value="<?= htmlspecialchars($o['actividad'] ?? '') ?>" style="width:100%;"></td>
                                    <td>
                                        <select name="orden[<?= $idx ?>][responsable_id]" style="width:100%;" onchange="mostrarOtro(this)">
                                            <option value="">Seleccione...</option>
                                            <?php foreach ($usuarios as $usr): ?>
                                                <option value="<?= $usr['id'] ?>" <?= ($o['responsable_id'] == $usr['id']) ? 'selected' : '' ?>><?= htmlspecialchars($usr['nombre']) ?></option>
                                            <?php endforeach; ?>
                                            <option value="otro" <?= ($o['responsable_id'] == 'otro') ? 'selected' : '' ?>>+ Otro</option>
                                        </select>
                                        <input type="text" name="orden[<?= $idx ?>][otro_responsable]" placeholder="Nombre" style="display:<?= ($o['responsable_id'] == 'otro') ? 'block' : 'none' ?>; margin-top:4px; width:100%;" value="<?= htmlspecialchars($o['otro_responsable'] ?? '') ?>">
                                    </td>
                                    <td><input type="number" class="row-dur" name="orden[<?= $idx ?>][duracion]" value="<?= $o['duracion_minutos'] ?? 15 ?>" step="1" min="1" style="width:100%;" onchange="calculateAgenda()"></td>
                                    <td><button type="button" class="btn btn-danger" onclick="eliminarFilaOrden(this)">✕</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td><input type="text" class="row-start" name="orden[0][hora_inicio]" readonly style="background:#eee; width:100%;" value="<?= substr($registro['hora_inicio'] ?? '', 0, 5) ?>"></td>
                                <td><input type="text" name="orden[0][actividad]" placeholder="Inicio" style="width:100%;"></td>
                                <td>
                                    <select name="orden[0][responsable_id]" style="width:100%;" onchange="mostrarOtro(this)">
                                        <option value="">Seleccione...</option>
                                        <?php foreach ($usuarios as $usr): ?>
                                            <option value="<?= $usr['id'] ?>"><?= htmlspecialchars($usr['nombre']) ?></option>
                                        <?php endforeach; ?>
                                        <option value="otro">+ Otro</option>
                                    </select>
                                    <input type="text" name="orden[0][otro_responsable]" placeholder="Nombre" style="display:none; margin-top:4px; width:100%;">
                                </td>
                                <td><input type="number" class="row-dur" name="orden[0][duracion]" value="30" step="1" min="1" style="width:100%;" onchange="calculateAgenda()"></td>
                                <td><button type="button" class="btn btn-danger" onclick="eliminarFilaOrden(this)">✕</button></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" onclick="agregarFilaOrden()">+ Agregar actividad</button>
                <div class="duration-summary" id="duration-alert-box">
                    Fin del evento - Duración total calculada del programa: <strong id="total-calculated-label">0</strong> min<br>
                    Duración total límite configurada (del bloque generales): <strong id="total-limit-label">540</strong> min
                </div>
            </div>
        </div>

        <!-- ===== D7. PRESÍDIUM ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">group</span>Presídium</div>
            <div class="presidium-controls">
                <div class="form-group" style="flex:1; min-width:180px;">
                    <label>Tipo de Presídium</label>
                    <select id="p-type" onchange="renderPresidium()" style="width:100%;">
                        <option value="lineal" <?= ($tipoPresidiumSeleccionado ?? '') == 'lineal' ? 'selected' : '' ?>>Lineal</option>
                        <option value="herradura" <?= ($tipoPresidiumSeleccionado ?? '') == 'herradura' ? 'selected' : '' ?>>Herradura</option>
                        <option value="media_luna" <?= ($tipoPresidiumSeleccionado ?? '') == 'media_luna' ? 'selected' : '' ?>>Media Luna</option>
                        <option value="redondo" <?= ($tipoPresidiumSeleccionado ?? '') == 'redondo' ? 'selected' : '' ?>>Redondo</option>
                        <option value="rusa" <?= ($tipoPresidiumSeleccionado ?? '') == 'rusa' ? 'selected' : '' ?>>Rusa</option>
                        <option value="cuadrada" <?= ($tipoPresidiumSeleccionado ?? '') == 'cuadrada' ? 'selected' : '' ?>>Cuadrada</option>
                    </select>
                </div>
                <div class="counter">
                    <button type="button" class="btn btn-secondary" onclick="adjustSpots(-1)">-</button>
                    <span id="spots-count"><?= count($presidium) > 1 ? count($presidium) : 5 ?></span>
                    <button type="button" class="btn btn-secondary" onclick="adjustSpots(1)">+</button>
                </div>
            </div>
            <div class="presidium-container">
                <div class="presidium-canvas-panel">
                    <h4>Distribución Visual</h4>
                    <div class="presidium-visual" id="p-canvas"></div>
                </div>
                <div class="presidium-list-panel">
                    <h4>Asignación de Autoridades (Jerarquía Protocolaria)</h4>
                    <div id="p-inputs">
                        <!-- El JS reemplazará este contenido, pero PHP puede dejar los inputs ocultos -->
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-top:15px;">
                <label>Maestra de ceremonias</label>
                <input type="text" name="maestra_ceremonias" value="<?= htmlspecialchars($evento['maestra_ceremonias'] ?? '') ?>" placeholder="Nombre de la maestra de ceremonias">
            </div>
            <input type="hidden" name="tipo_presidium_seleccionado" id="tipoPresidiumHidden" value="<?= $tipoPresidiumSeleccionado ?? 'lineal' ?>">
            <input type="hidden" name="presidium_data" id="presidiumData">
        </div>

        <!-- ===== D8. CROQUIS DEL EVENTO ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">map</span>Croquis del Evento</div>
            <div class="form-group full-width">
                <label>Imagen del croquis</label>
                <input type="file" name="imagen_croquis" accept="image/*">
                <?php if (!empty($evento['imagen_croquis'])): ?>
                    <div style="margin-top:4px;"><img src="<?= htmlspecialchars($evento['imagen_croquis']) ?>" style="max-height:100px;"></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ===== D9. INVITADOS ESPECIALES ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">person_add</span>Invitados Especiales (Cabildo, Gabinete, externos)</div>
            <table id="t-specials">
                <thead>
                    <tr><th style="width:10%;">N°</th><th style="width:45%;">Persona invitada</th><th style="width:35%;">Cargo (Negritas)</th><th style="width:10%;">Acciones</th></tr>
                </thead>
                <tbody id="invitadosBody">
                    <?php
                    $invitadosList = is_array($evento['invitados_especiales'] ?? null) ? $evento['invitados_especiales'] : [];
                    if (!empty($invitadosList)):
                        foreach ($invitadosList as $idx => $inv):
                    ?>
                        <tr>
                            <td><?= $idx+1 ?></td>
                            <td><input type="text" name="invitados[<?= $idx ?>][nombre]" value="<?= htmlspecialchars($inv['nombre'] ?? '') ?>" style="width:100%;"></td>
                            <td><input type="text" name="invitados[<?= $idx ?>][cargo]" value="<?= htmlspecialchars($inv['cargo'] ?? '') ?>" style="width:100%; font-weight:bold;"></td>
                            <td><button type="button" class="btn btn-danger" onclick="eliminarFilaInvitados(this)">✕</button></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td>1</td>
                            <td><input type="text" name="invitados[0][nombre]" placeholder="Nombre" style="width:100%;"></td>
                            <td><input type="text" name="invitados[0][cargo]" placeholder="Cargo" style="width:100%; font-weight:bold;"></td>
                            <td><button type="button" class="btn btn-danger" onclick="eliminarFilaInvitados(this)">✕</button></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" onclick="agregarFilaInvitados()">+ Agregar invitado</button>
        </div>

        <!-- ===== D10. MÓDULOS JORNADA INTEGRAL ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">grid_view</span>Módulos Jornada Integral</div>
            <table id="t-modules">
                <thead>
                    <tr><th style="width:10%;">N°</th><th style="width:45%;">Institución</th><th style="width:35%;">Servicio</th><th style="width:10%;">Acciones</th></tr>
                </thead>
                <tbody id="modulosBody">
                    <?php
                    $modulosList = is_array($evento['modulos_jornada'] ?? null) ? $evento['modulos_jornada'] : [];
                    if (!empty($modulosList)):
                        foreach ($modulosList as $idx => $mod):
                    ?>
                        <tr>
                            <td><?= $idx+1 ?></td>
                            <td><input type="text" name="modulos[<?= $idx ?>][institucion]" value="<?= htmlspecialchars($mod['institucion'] ?? '') ?>" style="width:100%;"></td>
                            <td><input type="text" name="modulos[<?= $idx ?>][servicio]" value="<?= htmlspecialchars($mod['servicio'] ?? '') ?>" style="width:100%;"></td>
                            <td><button type="button" class="btn btn-danger" onclick="eliminarFilaModulos(this)">✕</button></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td>1</td>
                            <td><input type="text" name="modulos[0][institucion]" placeholder="Institución" style="width:100%;"></td>
                            <td><input type="text" name="modulos[0][servicio]" placeholder="Servicio" style="width:100%;"></td>
                            <td><button type="button" class="btn btn-danger" onclick="eliminarFilaModulos(this)">✕</button></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" onclick="agregarFilaModulos()">+ Agregar módulo</button>
        </div>

        <!-- ===== D11. REQUERIMIENTOS ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">list_alt</span>Requerimientos Operativos</div>

            <h4 style="font-size:14px; margin-bottom:10px; color:#800000;">Requerimientos Internos (Delegación Administrativa)</h4>
            <table id="t-int">
                <thead>
                    <tr><th style="width:15%;">Cantidad</th><th style="width:40%;">Insumo</th><th style="width:25%;">Medida</th><th style="width:20%;">Unidad</th><th style="width:10%;">Acciones</th></tr>
                </thead>
                <tbody id="reqInternosBody">
                    <?php
                    $internos = is_array($evento['requerimientos_internos'] ?? null) ? $evento['requerimientos_internos'] : [];
                    if (!empty($internos)):
                        foreach ($internos as $idx => $item):
                    ?>
                        <tr>
                            <td><input type="number" name="req_internos[<?= $idx ?>][cantidad]" value="<?= $item['cantidad'] ?? 1 ?>" min="1" class="rcant" style="width:100%;"></td>
                            <td>
                                <select name="req_internos[<?= $idx ?>][insumo_id]" class="rins" style="width:100%;" onchange="actualizarMedidaUnidad(this)">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($insumosInternos as $ins): ?>
                                        <option value="<?= $ins['id'] ?>" <?= ($item['insumo_id'] ?? '') == $ins['id'] ? 'selected' : '' ?>
                                            data-medida="<?= htmlspecialchars($ins['medida'] ?? '') ?>"
                                            data-unidad="<?= htmlspecialchars($ins['unidad'] ?? '') ?>"
                                            data-stock="<?= $ins['stock_total'] ?>">
                                            <?= htmlspecialchars($ins['nombre_insumo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="req_internos[<?= $idx ?>][medida]" value="<?= htmlspecialchars($item['medida'] ?? '') ?>" placeholder="Medida" class="rmedida" style="width:100%;" readonly></td>
                            <td><input type="text" name="req_internos[<?= $idx ?>][unidad]" value="<?= htmlspecialchars($item['unidad'] ?? '') ?>" placeholder="Unidad" class="runidad" style="width:100%;" readonly></td>
                            <td><button type="button" class="btn btn-danger" onclick="eliminarFilaReq(this)">✕</button></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td><input type="number" name="req_internos[0][cantidad]" value="1" min="1" class="rcant" style="width:100%;"></td>
                            <td>
                                <select name="req_internos[0][insumo_id]" class="rins" style="width:100%;" onchange="actualizarMedidaUnidad(this)">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($insumosInternos as $ins): ?>
                                        <option value="<?= $ins['id'] ?>" data-medida="<?= htmlspecialchars($ins['medida'] ?? '') ?>" data-unidad="<?= htmlspecialchars($ins['unidad'] ?? '') ?>" data-stock="<?= $ins['stock_total'] ?>"><?= htmlspecialchars($ins['nombre_insumo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="req_internos[0][medida]" placeholder="Medida" class="rmedida" style="width:100%;" readonly></td>
                            <td><input type="text" name="req_internos[0][unidad]" placeholder="Unidad" class="runidad" style="width:100%;" readonly></td>
                            <td><button type="button" class="btn btn-danger" onclick="eliminarFilaReq(this)">✕</button></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" onclick="agregarFilaReq('t-int', 'internos')">+ Agregar requerimiento interno</button>

            <div style="margin: 20px 0 10px;">
                <h4 style="font-size:14px; margin-bottom:10px; color:#800000;">Requerimientos Externos (Dirección General de Administración)</h4>
            </div>
            <table id="t-ext">
                <thead>
                    <tr><th style="width:15%;">Cantidad</th><th style="width:40%;">Insumo</th><th style="width:25%;">Medida</th><th style="width:20%;">Unidad</th><th style="width:10%;">Acciones</th></tr>
                </thead>
                <tbody id="reqExternosBody">
                    <?php
                    $externos = is_array($evento['requerimientos_externos'] ?? null) ? $evento['requerimientos_externos'] : [];
                    if (!empty($externos)):
                        foreach ($externos as $idx => $item):
                    ?>
                        <tr>
                            <td><input type="number" name="req_externos[<?= $idx ?>][cantidad]" value="<?= $item['cantidad'] ?? 1 ?>" min="1" class="rcant" style="width:100%;"></td>
                            <td>
                                <select name="req_externos[<?= $idx ?>][insumo_id]" class="rins" style="width:100%;" onchange="actualizarMedidaUnidad(this)">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($insumosExternos as $ins): ?>
                                        <option value="<?= $ins['id'] ?>" <?= ($item['insumo_id'] ?? '') == $ins['id'] ? 'selected' : '' ?>
                                            data-medida="<?= htmlspecialchars($ins['medida'] ?? '') ?>"
                                            data-unidad="<?= htmlspecialchars($ins['unidad'] ?? '') ?>"
                                            data-stock="<?= $ins['stock_total'] ?>">
                                            <?= htmlspecialchars($ins['nombre_insumo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="req_externos[<?= $idx ?>][medida]" value="<?= htmlspecialchars($item['medida'] ?? '') ?>" placeholder="Medida" class="rmedida" style="width:100%;" readonly></td>
                            <td><input type="text" name="req_externos[<?= $idx ?>][unidad]" value="<?= htmlspecialchars($item['unidad'] ?? '') ?>" placeholder="Unidad" class="runidad" style="width:100%;" readonly></td>
                            <td><button type="button" class="btn btn-danger" onclick="eliminarFilaReq(this)">✕</button></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td><input type="number" name="req_externos[0][cantidad]" value="1" min="1" class="rcant" style="width:100%;"></td>
                            <td>
                                <select name="req_externos[0][insumo_id]" class="rins" style="width:100%;" onchange="actualizarMedidaUnidad(this)">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($insumosExternos as $ins): ?>
                                        <option value="<?= $ins['id'] ?>" data-medida="<?= htmlspecialchars($ins['medida'] ?? '') ?>" data-unidad="<?= htmlspecialchars($ins['unidad'] ?? '') ?>" data-stock="<?= $ins['stock_total'] ?>"><?= htmlspecialchars($ins['nombre_insumo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="req_externos[0][medida]" placeholder="Medida" class="rmedida" style="width:100%;" readonly></td>
                            <td><input type="text" name="req_externos[0][unidad]" placeholder="Unidad" class="runidad" style="width:100%;" readonly></td>
                            <td><button type="button" class="btn btn-danger" onclick="eliminarFilaReq(this)">✕</button></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" onclick="agregarFilaReq('t-ext', 'externos')">+ Agregar requerimiento externo</button>

            <div style="margin-top:20px;">
                <button type="button" class="btn btn-action-lock" onclick="lockAndConsolidate()">Solicitar y Bloquear Recursos</button>
            </div>
        </div>

        <!-- ===== D12. REQUERIMIENTOS FINALES Y FIRMAS ===== -->
        <div class="section">
            <div class="section-title"><span class="material-symbols-outlined">check_circle</span>Requerimientos Finales y Firmas</div>
            <div id="consolidated-container">
                <?php
                $internos = is_array($evento['requerimientos_internos'] ?? null) ? $evento['requerimientos_internos'] : [];
                $externos = is_array($evento['requerimientos_externos'] ?? null) ? $evento['requerimientos_externos'] : [];
                if (!empty($internos) || !empty($externos)):
                ?>
                    <h4 style="font-size:14px; margin-bottom:10px; color:#800000;">Consolidado de Requerimientos Solicitados</h4>
                    <table>
                        <thead><tr><th>Origen</th><th>Cantidad</th><th>Insumo</th><th>Medida</th><th>Unidad</th></tr></thead>
                        <tbody>
                            <?php foreach ($internos as $item): ?>
                                <tr>
                                    <td><span style="color:#28a745; font-weight:bold;">Interno</span></td>
                                    <td><?= $item['cantidad'] ?? 1 ?></td>
                                    <td><strong><?= htmlspecialchars($item['nombre_insumo'] ?? '') ?></strong></td>
                                    <td><?= htmlspecialchars($item['medida'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($item['unidad'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php foreach ($externos as $item): ?>
                                <tr>
                                    <td><span style="color:#007bff; font-weight:bold;">Externo</span></td>
                                    <td><?= $item['cantidad'] ?? 1 ?></td>
                                    <td><strong><?= htmlspecialchars($item['nombre_insumo'] ?? '') ?></strong></td>
                                    <td><?= htmlspecialchars($item['medida'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($item['unidad'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty-msg">Ningún recurso pre-apartado aún. Presione el botón "Solicitar y Bloquear Recursos".</p>
                <?php endif; ?>
            </div>
            <div class="form-grid" style="margin-top:20px;">
                <div class="form-group">
                    <label>Evento</label>
                    <input type="text" name="evento_nombre_final" value="<?= htmlspecialchars($evento['nombre_evento'] ?? '') ?>" placeholder="Nombre del evento">
                </div>
                <div class="form-group">
                    <label>Día</label>
                    <?php
                    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                    $timestamp = strtotime($registro['fecha_inicio'] ?? 'now');
                    $dia = date('d', $timestamp);
                    $mes = $meses[(int)date('m', $timestamp) - 1];
                    $anio = date('Y', $timestamp);
                    ?>
                    <input type="text" value="<?= $dia . ' de ' . $mes . ' de ' . $anio ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Horario</label>
                    <input type="text" value="<?= substr($registro['hora_inicio'] ?? '', 0, 5) . ' - ' . substr($registro['hora_fin'] ?? '', 0, 5) ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Ubicación</label>
                    <input type="text" value="<?= htmlspecialchars($carpeta['direccion_entrega'] ?? '') ?>" readonly>
                </div>
                <div class="form-group full-width" style="margin-top:20px;">
                    <label>Firma 1: Coordinador de Apoyo Técnico</label>
                    <input type="text" value="Mtro. Omar Ruiz Castillo - Coordinador de Apoyo Técnico" readonly>
                </div>
                <div class="form-group full-width">
                    <label>Firma 2: Delegado Administrativo</label>
                    <input type="text" value="Lcdo. Marco Antonio Guadarrama López - Delegado Administrativo" readonly>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer-actions">
            <a href="/Dir_bienestar/eventos/index" class="btn btn-secondary">← Volver a Eventos</a>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" id="generarPPTBtn">Generar PowerPoint</button>
                <button type="button" class="btn btn-success" id="guardarBtn">Guardar Carpeta</button>
            </div>
        </div>
    </form>
</div>

<!-- ============================================================
     PASAR VARIABLES DE PHP A JAVASCRIPT
     ============================================================ -->
<script>
    var currentSpots = <?= count($presidium) > 1 ? count($presidium) : 5 ?>;
    var tipoPresidiumSeleccionado = '<?= $tipoPresidiumSeleccionado ?? "lineal" ?>';
    var usuarios = <?= json_encode($usuarios) ?>;
    var insumosInternos = <?= json_encode($insumosInternos) ?>;
    var insumosExternos = <?= json_encode($insumosExternos) ?>;
    var registroHoraInicio = '<?= substr($registro['hora_inicio'] ?? '', 0, 5) ?>';
    // Datos reales del presidium que vienen de PHP (para inicializar savedNames y savedCargos)
    var presidiumData = <?= json_encode($presidium) ?>;
</script>

<!-- ============================================================
     SCRIPT CON LAS FUNCIONES (CORREGIDO PARA MANTENER DATOS)
     ============================================================ -->
<script>
// Variables auxiliares (no globales)
let savedNames = {};
let savedCargos = {};
let currentType = tipoPresidiumSeleccionado;

// Inicializar savedNames/savedCargos con los datos que vienen de PHP
if (presidiumData && presidiumData.length > 0) {
    // El orden de los spots en el canvas es: izquierda (descendente), *, derecha (ascendente)
    // Pero para simplificar, asignaremos en el orden en que aparecen en la lista de inputs
    // El JS de renderPresidium usa el índice de order para acceder a savedNames[idx]
    // Nosotros llenaremos esos objetos según el orden en que PHP imprimiría los inputs
    let idx = 0;
    for (let i = 0; i < presidiumData.length; i++) {
        let p = presidiumData[i];
        if (p.nombre_invitado === 'Lcdo. Ricardo Moreno Bastida') continue; // se ignora el presidente
        savedNames[idx] = p.nombre_invitado || '';
        savedCargos[idx] = p.cargo_invitado || '';
        idx++;
    }
    // Ajustar currentSpots al número real de miembros (incluyendo al presidente)
    currentSpots = presidiumData.length;
}

// ================================================================
// SINCERONIZACIÓN DE FECHAS
// ================================================================
function syncDates(val) {
    const resumenDate = document.getElementById('resumen-date');
    const genDate = document.getElementById('gen-date');
    if (resumenDate) resumenDate.value = val;
    if (genDate) genDate.value = val;
}

// ================================================================
// IMAGEN DE FONDO - CHECK "EN PROCESO"
// ================================================================
function toggleBgInput(checked) {
    const wrapper = document.getElementById('bg-wrapper');
    const fileInput = document.getElementById('bg-file');
    if (!wrapper || !fileInput) return;
    if (checked) {
        wrapper.classList.add('disabled');
        fileInput.disabled = true;
        const span = wrapper.querySelector('span');
        if (span) span.innerText = 'En Proceso — No Editable';
    } else {
        wrapper.classList.remove('disabled');
        fileInput.disabled = false;
        const span = wrapper.querySelector('span');
        if (span) span.innerText = 'Seleccionar diseño del evento...';
    }
}

// ================================================================
// ORDEN DEL DÍA - CÁLCULO DE HORAS Y DURACIÓN
// ================================================================
function calculateAgenda() {
    const startInput = document.getElementById('start-time');
    const start = startInput ? startInput.value : registroHoraInicio || '09:00';
    const limitInput = document.getElementById('limit-duration');
    const limit = limitInput ? parseInt(limitInput.value) || 540 : 540;
    const rows = document.querySelectorAll('#ordenBody tr');
    let [h, m] = start.split(':').map(Number);
    let total = 0;

    rows.forEach(r => {
        const startInputRow = r.querySelector('.row-start');
        const durInput = r.querySelector('.row-dur');
        if (startInputRow && durInput) {
            startInputRow.value = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
            let dur = parseInt(durInput.value) || 0;
            total += dur;
            m += dur;
            h += Math.floor(m / 60);
            m = m % 60;
            h = h % 24;
        }
    });

    const totalLabel = document.getElementById('total-calculated-label');
    const limitLabel = document.getElementById('total-limit-label');
    if (totalLabel) totalLabel.innerText = total;
    if (limitLabel) limitLabel.innerText = limit;

    const alertBox = document.getElementById('duration-alert-box');
    if (alertBox) {
        if (total > limit) {
            alertBox.classList.add('alert-active');
            alert('⚠️ Fin del evento - Duración total calculada del programa: ' + total + ' min\nDuración total límite configurada (del bloque generales): ' + limit + ' min.');
        } else {
            alertBox.classList.remove('alert-active');
        }
    }
}

function agregarFilaOrden() {
    const tbody = document.getElementById('ordenBody');
    if (!tbody) return;
    const idx = tbody.children.length;
    const tr = document.createElement('tr');

    let optionsHtml = '<option value="">Seleccione...</option>';
    usuarios.forEach(u => {
        optionsHtml += `<option value="${u.id}">${u.nombre}</option>`;
    });
    optionsHtml += '<option value="otro">+ Otro</option>';

    tr.innerHTML = `
        <td><input type="text" class="row-start" name="orden[${idx}][hora_inicio]" readonly style="background:#eee; width:100%;"></td>
        <td><input type="text" name="orden[${idx}][actividad]" style="width:100%;"></td>
        <td>
            <select name="orden[${idx}][responsable_id]" style="width:100%;" onchange="mostrarOtro(this)">
                ${optionsHtml}
            </select>
            <input type="text" name="orden[${idx}][otro_responsable]" placeholder="Nombre" style="display:none; margin-top:4px; width:100%;">
        </td>
        <td><input type="number" class="row-dur" name="orden[${idx}][duracion]" value="15" step="1" min="1" style="width:100%;" onchange="calculateAgenda()"></td>
        <td><button type="button" class="btn btn-danger" onclick="eliminarFilaOrden(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
    calculateAgenda();
}

function eliminarFilaOrden(btn) {
    const row = btn.closest('tr');
    if (row && row.parentElement.children.length > 1) {
        row.remove();
        calculateAgenda();
    }
}

function mostrarOtro(select) {
    const input = select.closest('td').querySelector('input[type="text"]');
    if (!input) return;
    if (select.value === 'otro') {
        input.style.display = 'block';
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

// ================================================================
// PRESÍDIUM - RENDERIZADO VISUAL Y LISTA (MANTIENE DATOS REALES)
// ================================================================
function savePresidiumState() {
    const inputs = document.querySelectorAll('#p-inputs .presidium-row-item');
    inputs.forEach((row, idx) => {
        const nameInput = row.querySelector('input[id^="name-"]');
        const cargoInput = row.querySelector('input[id^="cargo-"]');
        if (nameInput) savedNames[idx] = nameInput.value;
        if (cargoInput) savedCargos[idx] = cargoInput.value;
    });
}

function adjustSpots(amt) {
    currentSpots += amt;
    if (currentSpots < 1) currentSpots = 1;
    if (currentSpots > 15) currentSpots = 15;
    const spotsCount = document.getElementById('spots-count');
    if (spotsCount) spotsCount.innerText = currentSpots;
    renderPresidium();
}

function renderPresidium() {
    savePresidiumState();
    const typeSelect = document.getElementById('p-type');
    if (!typeSelect) return;
    const type = typeSelect.value;
    currentType = type;
    const hidden = document.getElementById('tipoPresidiumHidden');
    if (hidden) hidden.value = type;

    const canvas = document.getElementById('p-canvas');
    const inputs = document.getElementById('p-inputs');
    if (!canvas || !inputs) return;
    canvas.innerHTML = '';
    inputs.innerHTML = '';

    canvas.style.width = '100%';
    canvas.style.height = '240px';
    canvas.style.position = 'relative';
    canvas.style.border = '1px dashed rgba(128,0,0,0.2)';
    canvas.style.background = '#fff';

    const total = currentSpots;
    let order = ['*'];
    let left = [], right = [];
    for (let i = 1; i < total; i++) {
        if (i % 2 !== 0) left.push(i);
        else right.push(i);
    }
    left.reverse();
    order = left.concat(['*']).concat(right);

    if (type !== 'lineal') {
        const shape = document.createElement('div');
        shape.className = 'presidium-shape-black';
        shape.style.position = 'absolute';
        shape.style.backgroundColor = '#111';
        shape.style.opacity = '0.85';
        shape.style.zIndex = '5';
        shape.style.transform = 'translate(-50%, -50%)';
        shape.style.left = '50%';
        shape.style.top = '50%';

        switch (type) {
            case 'redondo':
                shape.style.width = '140px'; shape.style.height = '140px'; shape.style.borderRadius = '50%'; shape.style.backgroundColor = '#111';
                break;
            case 'herradura':
                shape.style.width = '150px'; shape.style.height = '100px'; shape.style.border = '15px solid #111'; shape.style.borderBottom = 'none'; shape.style.borderRadius = '80px 80px 0 0'; shape.style.backgroundColor = 'transparent';
                break;
            case 'media_luna':
                shape.style.width = '150px'; shape.style.height = '100px'; shape.style.border = '15px solid #111'; shape.style.borderBottom = 'none'; shape.style.borderRadius = '80px 80px 0 0'; shape.style.backgroundColor = 'transparent'; shape.style.top = '55%';
                break;
            case 'rusa':
                shape.style.width = '160px'; shape.style.height = '90px'; shape.style.border = '15px solid #111'; shape.style.borderBottom = 'none'; shape.style.borderRadius = '0'; shape.style.backgroundColor = 'transparent';
                break;
            case 'cuadrada':
                shape.style.width = '160px'; shape.style.height = '120px'; shape.style.border = '15px solid #111'; shape.style.borderRadius = '0'; shape.style.backgroundColor = 'transparent';
                break;
        }
        canvas.appendChild(shape);
    }

    order.forEach((spot, index) => {
        let leftPos = 50, topPos = 50;
        const totalSpots = order.length;
        if (type === 'lineal') {
            leftPos = 10 + (index / (totalSpots - 1)) * 80;
            topPos = 50;
        } else {
            switch (type) {
                case 'redondo':
                    const angleR = (index / totalSpots) * 2 * Math.PI - Math.PI / 2;
                    leftPos = 50 + 32 * Math.cos(angleR); topPos = 50 + 32 * Math.sin(angleR);
                    break;
                case 'herradura':
                    const angleH = (index / (totalSpots - 1)) * Math.PI;
                    leftPos = 50 + 35 * Math.cos(angleH + Math.PI); topPos = 45 + 32 * Math.sin(angleH + Math.PI);
                    break;
                case 'media_luna':
                    const angleM = (index / (totalSpots - 1)) * Math.PI;
                    leftPos = 50 + 35 * Math.cos(angleM); topPos = 55 - 32 * Math.sin(angleM);
                    break;
                case 'rusa':
                    const seg = Math.max(1, totalSpots - 1);
                    leftPos = 20 + (index * (60 / seg)); topPos = (index === 0 || index === totalSpots - 1) ? 65 : 40;
                    break;
                case 'cuadrada':
                    const cols = 4;
                    const row = Math.floor(index / cols); const col = index % cols;
                    leftPos = 25 + col * 16; topPos = 30 + row * 18;
                    break;
            }
        }

        const sDiv = document.createElement('div');
        sDiv.className = `presidium-spot ${spot === '*' ? 'center-spot' : ''}`;
        sDiv.style.left = leftPos + '%'; sDiv.style.top = topPos + '%'; sDiv.innerText = spot;
        canvas.appendChild(sDiv);
    });

    // Generar inputs respetando savedNames y savedCargos
    order.forEach((spot, index) => {
        const row = document.createElement('div');
        row.className = 'presidium-row-item';
        let oldN = savedNames[index] || '';
        let oldC = savedCargos[index] || '';

        if (spot === '*') {
            row.innerHTML = `
                <div class="spot-tag">*</div>
                <input type="text" value="Lcdo. Ricardo Moreno Bastida" readonly style="flex:1; background:#eee; font-weight:bold;">
                <input type="text" value="Presidente Municipal Constitucional de Toluca" readonly style="width:35%; background:#eee;">
            `;
        } else {
            row.innerHTML = `
                <div class="spot-tag">${spot}</div>
                <input type="text" id="name-${index}" value="${oldN}" style="flex:1;" placeholder="Nombre">
                <input type="text" id="cargo-${index}" value="${oldC}" style="width:35%;" placeholder="Cargo">
            `;
        }
        inputs.appendChild(row);
    });
}

// ================================================================
// INVITADOS ESPECIALES
// ================================================================
function agregarFilaInvitados() {
    const tbody = document.getElementById('invitadosBody');
    if (!tbody) return;
    const idx = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>${idx + 1}</td>
        <td><input type="text" name="invitados[${idx}][nombre]" style="width:100%;"></td>
        <td><input type="text" name="invitados[${idx}][cargo]" style="width:100%; font-weight:bold;"></td>
        <td><button type="button" class="btn btn-danger" onclick="eliminarFilaInvitados(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
    reordenarInvitados();
}

function eliminarFilaInvitados(btn) {
    const row = btn.closest('tr');
    if (row && row.parentElement.children.length > 1) {
        row.remove();
        reordenarInvitados();
    }
}

function reordenarInvitados() {
    const rows = document.querySelectorAll('#invitadosBody tr');
    rows.forEach((r, idx) => { r.cells[0].innerText = idx + 1; });
}

// ================================================================
// MÓDULOS JORNADA INTEGRAL
// ================================================================
function agregarFilaModulos() {
    const tbody = document.getElementById('modulosBody');
    if (!tbody) return;
    const idx = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>${idx + 1}</td>
        <td><input type="text" name="modulos[${idx}][institucion]" style="width:100%;"></td>
        <td><input type="text" name="modulos[${idx}][servicio]" style="width:100%;"></td>
        <td><button type="button" class="btn btn-danger" onclick="eliminarFilaModulos(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
    reordenarModulos();
}

function eliminarFilaModulos(btn) {
    const row = btn.closest('tr');
    if (row && row.parentElement.children.length > 1) {
        row.remove();
        reordenarModulos();
    }
}

function reordenarModulos() {
    const rows = document.querySelectorAll('#modulosBody tr');
    rows.forEach((r, idx) => { r.cells[0].innerText = idx + 1; });
}

// ================================================================
// REQUERIMIENTOS - AGREGAR Y ELIMINAR
// ================================================================
function agregarFilaReq(tableId, tipo) {
    const tbody = document.querySelector(`#${tableId} tbody`);
    if (!tbody) return;
    const idx = tbody.children.length;
    const tr = document.createElement('tr');
    const prefijo = tipo === 'internos' ? 'req_internos' : 'req_externos';
    const options = tipo === 'internos' ? insumosInternos : insumosExternos;

    let optHtml = '<option value="">Seleccione...</option>';
    options.forEach(ins => {
        optHtml += `<option value="${ins.id}" data-medida="${ins.medida || ''}" data-unidad="${ins.unidad || ''}" data-stock="${ins.stock_total}">${ins.nombre_insumo}</option>`;
    });

    tr.innerHTML = `
        <td><input type="number" name="${prefijo}[${idx}][cantidad]" value="1" min="1" class="rcant" style="width:100%;"></td>
        <td><select name="${prefijo}[${idx}][insumo_id]" class="rins" style="width:100%;" onchange="actualizarMedidaUnidad(this)">${optHtml}</select></td>
        <td><input type="text" name="${prefijo}[${idx}][medida]" placeholder="Medida" class="rmedida" style="width:100%;" readonly></td>
        <td><input type="text" name="${prefijo}[${idx}][unidad]" placeholder="Unidad" class="runidad" style="width:100%;" readonly></td>
        <td><button type="button" class="btn btn-danger" onclick="eliminarFilaReq(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
}

function eliminarFilaReq(btn) {
    const row = btn.closest('tr');
    if (row && row.parentElement.children.length > 1) row.remove();
}

function actualizarMedidaUnidad(select) {
    const row = select.closest('tr');
    const medidaInput = row.querySelector('.rmedida');
    const unidadInput = row.querySelector('.runidad');
    const option = select.options[select.selectedIndex];
    if (option && option.value) {
        if (medidaInput) medidaInput.value = option.dataset.medida || '';
        if (unidadInput) unidadInput.value = option.dataset.unidad || '';
    } else {
        if (medidaInput) medidaInput.value = '';
        if (unidadInput) unidadInput.value = '';
    }
}

// ================================================================
// SOLICITAR Y BLOQUEAR RECURSOS
// ================================================================
function lockAndConsolidate() {
    const container = document.getElementById('consolidated-container');
    let errores = [];

    document.querySelectorAll('#t-int tbody tr').forEach(r => {
        const cantInput = r.querySelector('.rcant');
        const select = r.querySelector('.rins');
        if (select && select.value) {
            const stock = parseInt(select.options[select.selectedIndex].dataset.stock) || 0;
            const cantidad = parseInt(cantInput.value) || 0;
            if (cantidad > stock) errores.push(`- ${select.options[select.selectedIndex].text}`);
        }
    });
    document.querySelectorAll('#t-ext tbody tr').forEach(r => {
        const cantInput = r.querySelector('.rcant');
        const select = r.querySelector('.rins');
        if (select && select.value) {
            const stock = parseInt(select.options[select.selectedIndex].dataset.stock) || 0;
            const cantidad = parseInt(cantInput.value) || 0;
            if (cantidad > stock) errores.push(`- ${select.options[select.selectedIndex].text}`);
        }
    });

    if (errores.length > 0) {
        alert('❌ No hay suficiente stock para los siguientes insumos:\n' + errores.join('\n') + '\nPor favor ajusta las cantidades.');
        return;
    }

    const internos = [];
    document.querySelectorAll('#t-int tbody tr').forEach(r => {
        const cant = r.querySelector('.rcant')?.value || 1;
        const select = r.querySelector('.rins');
        const insumo = select ? select.options[select.selectedIndex] : null;
        const medida = r.querySelector('.rmedida')?.value || '';
        const unidad = r.querySelector('.runidad')?.value || '';
        if (insumo && insumo.value) {
            internos.push({ cantidad: cant, insumo_id: insumo.value, nombre_insumo: insumo.text, medida, unidad });
        }
    });
    const externos = [];
    document.querySelectorAll('#t-ext tbody tr').forEach(r => {
        const cant = r.querySelector('.rcant')?.value || 1;
        const select = r.querySelector('.rins');
        const insumo = select ? select.options[select.selectedIndex] : null;
        const medida = r.querySelector('.rmedida')?.value || '';
        const unidad = r.querySelector('.runidad')?.value || '';
        if (insumo && insumo.value) {
            externos.push({ cantidad: cant, insumo_id: insumo.value, nombre_insumo: insumo.text, medida, unidad });
        }
    });

    if (internos.length === 0 && externos.length === 0) {
        alert('No hay requerimientos para solicitar.');
        return;
    }

    let html = '<h4 style="font-size:14px; margin-bottom:10px; color:#800000;">Consolidado de Requerimientos Solicitados</h4>';
    html += '<table><thead><tr><th>Origen</th><th>Cantidad</th><th>Insumo</th><th>Medida</th><th>Unidad</th></tr></thead><tbody>';
    internos.forEach(item => {
        html += `<tr><td><span style="color:#28a745; font-weight:bold;">Interno</span></td><td>${item.cantidad}</td><td><strong>${item.nombre_insumo}</strong></td><td>${item.medida}</td><td>${item.unidad}</td></tr>`;
    });
    externos.forEach(item => {
        html += `<tr><td><span style="color:#007bff; font-weight:bold;">Externo</span></td><td>${item.cantidad}</td><td><strong>${item.nombre_insumo}</strong></td><td>${item.medida}</td><td>${item.unidad}</td></tr>`;
    });
    html += '</tbody></table>';
    if (container) container.innerHTML = html;
    alert('✅ Insumos bloqueados exitosamente.');
}

// ================================================================
// ENVÍO DEL FORMULARIO (FormData, el controlador ya espera $_POST)
// ================================================================
function enviarFormulario() {
    const form = document.getElementById('carpetaForm');
    if (!form) return;
    const formData = new FormData(form);

    const presidiumList = [];
    document.querySelectorAll('#p-inputs .presidium-row-item').forEach((row, idx) => {
        const spotTag = row.querySelector('.spot-tag');
        const nombreInput = row.querySelector('input[id^="name-"]');
        const cargoInput = row.querySelector('input[id^="cargo-"]');
        if (spotTag && nombreInput && cargoInput) {
            presidiumList.push({
                orden: spotTag.innerText,
                nombre: nombreInput.value,
                cargo: cargoInput.value
            });
        }
    });
    formData.append('presidium_data', JSON.stringify(presidiumList));

    fetch('/Dir_bienestar/eventos/guardar_carpeta', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('✅ Carpeta guardada correctamente.');
            if (result.carpeta_id) {
                window.location.href = '/Dir_bienestar/eventos/editar_carpeta?id_registro=' + formData.get('registro_actividad_id');
            }
        } else {
            alert('❌ Error: ' + (result.error || 'No se pudo guardar'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error de conexión. Revisa la consola para más detalles.');
    });
}

// ================================================================
// GENERAR POWERPOINT (AJAX + REDIRECCIÓN)
// ================================================================
function generarPowerPoint() {
    const form = document.getElementById('carpetaForm');
    const formData = new FormData(form);
    const registroId = formData.get('registro_actividad_id');
    if (!registroId) {
        alert('No se encontró el registro de actividad.');
        return;
    }

    fetch('/Dir_bienestar/evento_ppt/generar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_registro=' + encodeURIComponent(registroId)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success && result.url) {
            window.open(result.url, '_blank');
            setTimeout(() => { window.location.href = '/Dir_bienestar/eventos/index'; }, 1000);
        } else {
            alert('❌ Error al generar PowerPoint: ' + (result.error || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error de conexión al generar PowerPoint.');
    });
}

// ================================================================
// INICIALIZACIÓN
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    const mainDate = document.getElementById('main-date');
    if (mainDate) syncDates(mainDate.value);

    calculateAgenda();
    renderPresidium();

    const bgCheck = document.getElementById('bg-in-progress');
    if (bgCheck && bgCheck.checked) toggleBgInput(true);

    const guardarBtn = document.getElementById('guardarBtn');
    if (guardarBtn) guardarBtn.addEventListener('click', enviarFormulario);

    const generarBtn = document.getElementById('generarPPTBtn');
    if (generarBtn) generarBtn.addEventListener('click', generarPowerPoint);
});
</script>
</body>
</html>