<?php include_once APPROOT . '/views/partials/menu.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Carpeta | DG Bienestar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: #FEF7F0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 24px 28px;
            color: #2C241A;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #E6D8C8;
            padding-bottom: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .header h1 {
            color: #800000;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header .badge {
            background: #800000;
            color: white;
            padding: 4px 16px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .section {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .section-title {
            font-size: 1.2rem;
            color: #800000;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .grid-item {
            padding: 8px 0;
            border-bottom: 1px solid #EDE0D2;
        }
        .grid-item label {
            font-weight: 600;
            color: #5E3E22;
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .grid-item .value {
            font-size: 0.9rem;
            color: #2C241A;
            word-break: break-word;
        }
        .evidencia-link {
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
        }
        .evidencia-link:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }
        th {
            background: #800000;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #EDE0D2;
        }
        tr:hover td {
            background: #FCF5EA;
        }
        .btn {
            padding: 8px 20px;
            border-radius: 40px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            background: #E7DAC8;
            color: #5E3E22;
            transition: 0.15s;
        }
        .btn:hover {
            background: #D4C3AB;
        }
        .btn-primary {
            background: #800000;
            color: white;
        }
        .btn-primary:hover {
            background: #5a0000;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #B28B60;
            font-size: 0.7rem;
            padding: 16px 0;
            border-top: 1px solid #EDE0D2;
        }
        .empty-msg {
            color: #AB8E66;
            font-style: italic;
            padding: 8px 0;
        }
        @media (max-width: 650px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- HEADER -->
    <div class="header">
        <h1>
            <span class="material-symbols-outlined" style="font-size:2rem;">folder</span>
            Detalle de Carpeta
        </h1>
        <span class="badge"><?= ucfirst($carpeta['estado'] ?? 'Pendiente') ?></span>
    </div>

    <!-- ========================================================== -->
    <!-- 1. INFORMACIÓN GENERAL -->
    <!-- ========================================================== -->
    <div class="section">
        <div class="section-title">
            <span class="material-symbols-outlined">info</span> Información general
        </div>
        <div class="grid">
            <div class="grid-item">
                <label>Actividad</label>
                <div class="value"><?= htmlspecialchars($registro['actividad_desc'] ?? '') ?></div>
            </div>
            <div class="grid-item">
                <label>Dirección</label>
                <div class="value"><?= htmlspecialchars($registro['unidad_nombre'] ?? '') ?></div>
            </div>
            <div class="grid-item">
                <label>Responsable</label>
                <div class="value"><?= htmlspecialchars($registro['usuario_nombre'] ?? '') ?></div>
            </div>
            <div class="grid-item">
                <label>Fecha de entrega</label>
                <div class="value"><?= $carpeta['fecha_entrega'] ?? 'No definida' ?></div>
            </div>
            <div class="grid-item">
                <label>Fecha del evento</label>
                <div class="value"><?= $registro['fecha_inicio'] ?? '' ?></div>
            </div>
            <div class="grid-item">
                <label>Lugar</label>
                <div class="value"><?= htmlspecialchars($registro['lugar_nombre'] ?? '') ?></div>
            </div>
            <div class="grid-item">
                <label>Beneficiarios</label>
                <div class="value"><?= $registro['beneficiarios_asistentes'] ?? 0 ?></div>
            </div>
            <div class="grid-item">
                <label>Evidencia</label>
                <div class="value">
                    <?php if (!empty($carpeta['firma'])): ?>
                        <!-- ✅ CORREGIDO: Ruta absoluta con "/" al inicio -->
                        <a href="/<?= htmlspecialchars($carpeta['firma']) ?>" target="_blank" class="evidencia-link">
                            📄 Ver archivo
                        </a>
                    <?php else: ?>
                        <span style="color:#999;">Sin archivo</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================== -->
    // 2. ORDEN DEL DÍA
    <!-- ========================================================== -->
    <?php if (!empty($ordenes)): ?>
    <div class="section">
        <div class="section-title">
            <span class="material-symbols-outlined">schedule</span> Orden del día
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:15%;">Hora inicio</th>
                    <th style="width:40%;">Actividad</th>
                    <th style="width:30%;">Responsable</th>
                    <th style="width:15%;">Duración</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordenes as $o): ?>
                    <tr>
                        <td><?= substr($o['hora_inicio'] ?? '', 0, 5) ?></td>
                        <td><?= htmlspecialchars($o['actividad'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($o['otro_responsable'])): ?>
                                <?= htmlspecialchars($o['otro_responsable']) ?>
                            <?php elseif (!empty($o['responsable_id'])): ?>
                                <?= 'ID: ' . $o['responsable_id'] ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= $o['duracion_calculada'] ?? 15 ?> min</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- ========================================================== -->
    // 3. PRESÍDIUM
    <!-- ========================================================== -->
    <?php if (!empty($presidium)): ?>
    <div class="section">
        <div class="section-title">
            <span class="material-symbols-outlined">group</span> Presídium
        </div>
        <table>
            <thead>
                <tr><th>Nombre</th><th>Cargo</th></tr>
            </thead>
            <tbody>
                <?php foreach ($presidium as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nombre_invitado'] ?? '') ?></td>
                        <td><?= htmlspecialchars($p['cargo_invitado'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (!empty($evento['maestra_ceremonias'])): ?>
            <p style="margin-top:12px; font-weight:600;">
                <span class="material-symbols-outlined" style="font-size:1rem; vertical-align:middle;">mic</span>
                Maestra de ceremonias: <?= htmlspecialchars($evento['maestra_ceremonias']) ?>
            </p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ========================================================== -->
    // 4. INVITADOS ESPECIALES
    <!-- ========================================================== -->
    <?php if (!empty($invitados)): ?>
    <div class="section">
        <div class="section-title">
            <span class="material-symbols-outlined">person_add</span> Invitados especiales
        </div>
        <table>
            <thead>
                <tr><th style="width:50%;">Nombre</th><th style="width:50%;">Cargo</th></tr>
            </thead>
            <tbody>
                <?php foreach ($invitados as $inv): ?>
                    <tr>
                        <td><?= htmlspecialchars($inv['nombre'] ?? '') ?></td>
                        <td><?= htmlspecialchars($inv['cargo'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- ========================================================== -->
    // 5. MÓDULOS JORNADA INTEGRAL
    <!-- ========================================================== -->
    <?php if (!empty($modulos)): ?>
    <div class="section">
        <div class="section-title">
            <span class="material-symbols-outlined">grid_view</span> Módulos Jornada Integral
        </div>
        <table>
            <thead>
                <tr><th style="width:50%;">Institución</th><th style="width:50%;">Servicio</th></tr>
            </thead>
            <tbody>
                <?php foreach ($modulos as $mod): ?>
                    <tr>
                        <td><?= htmlspecialchars($mod['institucion'] ?? '') ?></td>
                        <td><?= htmlspecialchars($mod['servicio'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- ========================================================== -->
    // 6. REQUERIMIENTOS (Internos y Externos)
    <!-- ========================================================== -->
    <?php if (!empty($internos) || !empty($externos)): ?>
    <div class="section">
        <div class="section-title">
            <span class="material-symbols-outlined">list_alt</span> Requerimientos
        </div>

        <?php if (!empty($internos)): ?>
            <h4 style="color:#800000; margin: 8px 0 6px;">Internos</h4>
            <table>
                <thead>
                    <tr><th style="width:15%;">Cantidad</th><th style="width:40%;">Insumo</th><th style="width:25%;">Medida</th><th style="width:20%;">Unidad</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($internos as $item): ?>
                        <tr>
                            <td><?= $item['cantidad'] ?? 1 ?></td>
                            <td><?= htmlspecialchars($item['nombre_insumo'] ?? '') ?></td>
                            <td><?= htmlspecialchars($item['medida'] ?? '') ?></td>
                            <td><?= htmlspecialchars($item['unidad'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($externos)): ?>
            <h4 style="color:#800000; margin: 16px 0 6px;">Externos</h4>
            <table>
                <thead>
                    <tr><th style="width:15%;">Cantidad</th><th style="width:40%;">Insumo</th><th style="width:25%;">Medida</th><th style="width:20%;">Unidad</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($externos as $item): ?>
                        <tr>
                            <td><?= $item['cantidad'] ?? 1 ?></td>
                            <td><?= htmlspecialchars($item['nombre_insumo'] ?? '') ?></td>
                            <td><?= htmlspecialchars($item['medida'] ?? '') ?></td>
                            <td><?= htmlspecialchars($item['unidad'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ========================================================== -->
    // BOTÓN DE VOLVER
    <!-- ========================================================== -->
    <div style="margin-top:20px;">
        <a href="/Dir_bienestar/eventos/index?tipo=1" class="btn">
            <span class="material-symbols-outlined" style="font-size:1rem; vertical-align:middle;">arrow_back</span>
            Volver a eventos
        </a>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        © <?= date('Y') ?> DG Bienestar - Detalle de carpeta
    </div>
</div>
</body>
</html>