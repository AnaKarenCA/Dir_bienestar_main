<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Carpeta de Evento</title>
    <style>
        /* ===== BASE ===== */
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: white;
            color: #1a1a1a;
            font-size: 12px;
            line-height: 1.4;
        }
        .page {
            width: 100%;
            height: 100vh;
            page-break-after: always;
            padding: 30px 40px;
            position: relative;
        }
        /* ===== PORTADA ===== */
        .portada {
            background: url('<?= PUBLIC_PATH . '/' . ($imagen_fondo ?? 'img/Toluca-valor.jpg') ?>') no-repeat center center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.6);
        }
        .portada .overlay {
            background: rgba(0,0,0,0.4);
            position: absolute;
            top:0; left:0; width:100%; height:100%;
            z-index:1;
        }
        .portada .content {
            position: relative;
            z-index:2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .portada .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .portada .header .logo {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1.2;
        }
        .portada .header .sub {
            font-size: 14px;
            font-weight: 300;
        }
        .portada .main-title {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 20px 0;
        }
        .portada .info {
            text-align: center;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.8;
        }
        .portada .info strong {
            font-weight: 700;
        }
        .portada .footer {
            text-align: center;
            font-size: 13px;
            border-top: 2px solid rgba(255,255,255,0.3);
            padding-top: 15px;
        }

        /* ===== INTERIOR ===== */
        .interior {
            background: white;
            color: #333;
            padding: 40px 50px;
        }
        .interior .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #800000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .interior .encabezado .logo-toluca {
            font-size: 18px;
            font-weight: bold;
            color: #800000;
        }
        .interior .encabezado .logo-toluca small {
            font-weight: 300;
            font-size: 12px;
            color: #555;
        }
        .interior .seccion {
            margin-bottom: 20px;
        }
        .interior .seccion h2 {
            color: #800000;
            font-size: 16px;
            font-weight: 700;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .interior .seccion .campo {
            margin: 4px 0;
            font-size: 13px;
        }
        .interior .seccion .campo strong {
            font-weight: 700;
            color: #800000;
            display: inline-block;
            width: 140px;
        }
        .interior table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin: 8px 0;
        }
        .interior table th {
            background: #800000;
            color: white;
            padding: 6px 10px;
            text-align: left;
            font-weight: 700;
        }
        .interior table td, .interior table th {
            border: 1px solid #ddd;
            padding: 5px 8px;
        }
        .interior .firmas {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        .interior .firmas .firma {
            text-align: center;
            width: 45%;
        }
        .interior .firmas .firma .linea {
            border-top: 1px solid #000;
            margin: 6px auto 2px;
            width: 80%;
        }
        .interior .firmas .firma .nombre {
            font-weight: 700;
            font-size: 13px;
        }
        .interior .firmas .firma .cargo {
            font-size: 11px;
            color: #555;
        }
        .interior .fila-doble {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .interior .fila-doble > div {
            flex: 1;
        }
        .interior .ubicacion-mapa {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }
        .interior .ubicacion-mapa .direccion {
            flex: 2;
        }
        .interior .ubicacion-mapa .mapa {
            flex: 1;
            background: #f5f5f5;
            border: 1px dashed #aaa;
            padding: 10px;
            text-align: center;
            font-size: 11px;
            color: #888;
        }
        .interior .presidium-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 20px;
            margin-top: 5px;
        }
        .interior .presidium-grid .item {
            display: flex;
            gap: 6px;
            font-size: 13px;
        }
        .interior .presidium-grid .item .orden {
            font-weight: 700;
            color: #800000;
            width: 30px;
        }
        .interior .presidium-grid .item .nombre-cargo {
            font-weight: 400;
        }
        .interior .presidium-grid .item .nombre-cargo strong {
            font-weight: 700;
        }
        .interior .croquis {
            background: #f9f9f9;
            border: 1px dashed #aaa;
            padding: 15px;
            text-align: center;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
        }
        .interior .croquis img {
            max-width: 100%;
            max-height: 200px;
        }

        /* Ajustes para PDF */
        .page-break {
            page-break-before: always;
        }
        .no-break {
            page-break-inside: avoid;
        }
        @page {
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- ============================================================ -->
    <!-- PORTADA -->
    <!-- ============================================================ -->
    <div class="page portada">
        <div class="overlay"></div>
        <div class="content">
            <div class="header">
                <div class="logo">
                    TOLUCA<br>
                    <span class="sub">CAPITAL DE OPORTUNIDADES Y PROGRESO</span>
                </div>
                <div style="text-align:right; font-size:12px; font-weight:300;">
                    <strong>DIRECCIÓN GENERAL DE BIENESTAR</strong>
                </div>
            </div>
            <div class="main-title">
                <?= htmlspecialchars($evento['nombre_evento'] ?? 'NOMBRE DEL EVENTO') ?>
            </div>
            <div class="info">
                <strong>APROBADO</strong><br>
                <?= htmlspecialchars($evento['aprobado_por'] ?? 'MTRA. ANDREA MA. DEL ROCÍO MERLOS NÁJERA') ?>
                <br><br>
                <strong>RESPONSABLE</strong><br>
                <?= htmlspecialchars($responsable_evento) ?>
                <br><br>
                <strong>DIRECCIÓN QUE REALIZA EL EVENTO</strong><br>
                <?= htmlspecialchars($unidad_nombre) ?>
                <br><br>
                <strong>FECHA DE ENTREGA</strong><br>
                <?= date('d/m/Y', strtotime($fecha_entrega)) ?>
                <br><br>
                <strong>FIRMA</strong><br>
                <span style="font-size:11px;">(Campo suelto)</span>
            </div>
            <div class="footer">
                <span style="font-size:11px; text-transform:uppercase;">Documento de trabajo</span>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- INTERIOR: OBJETIVO Y BENEFICIARIOS -->
    <!-- ============================================================ -->
    <div class="page interior">
        <div class="encabezado">
            <div class="logo-toluca">
                TOLUCA<br>
                <small>CAPITAL DE OPORTUNIDADES Y PROGRESO</small>
            </div>
            <div style="text-align:right; font-weight:bold; color:#800000; font-size:14px;">
                H. Ayuntamiento de Toluca
            </div>
        </div>

        <div class="seccion">
            <h2>OBJETIVO Y BENEFICIARIOS</h2>
            <div class="campo"><strong>Línea de acción PbRM:</strong> <?= htmlspecialchars($evento['descripcion_meta'] ?? '') ?></div>
            <div class="campo" style="margin-top:6px;"><strong>Objetivo del evento:</strong> <?= htmlspecialchars($evento['objetivo'] ?? '') ?></div>
            <div class="campo"><strong>Número de beneficiarios:</strong> <?= $beneficiarios ?></div>
        </div>

        <div class="seccion">
            <h2>JUSTIFICACIÓN</h2>
            <p style="font-size:13px; line-height:1.5;"><?= nl2br(htmlspecialchars($evento['justificacion'] ?? '')) ?></p>
        </div>

        <div class="seccion">
            <h2>GENERALES DEL EVENTO</h2>
            <div class="fila-doble">
                <div>
                    <div class="campo"><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($registro['fecha_inicio'])) ?></div>
                    <div class="campo"><strong>Hora:</strong> <?= substr($registro['hora_inicio'], 0, 5) ?></div>
                    <div class="campo"><strong>Lugar:</strong> <?= htmlspecialchars($registro['lugar_nombre'] ?? '') ?></div>
                </div>
                <div>
                    <div class="campo"><strong>Vestimenta:</strong> <?= htmlspecialchars($evento['vestimenta'] ?? '') ?></div>
                    <div class="campo"><strong>Duración del Evento:</strong> <?= $duracion_str ?></div>
                    <div class="campo"><strong>Coordinación del Evento:</strong> <?= htmlspecialchars($coordinacion_evento) ?></div>
                    <div class="campo"><strong>Responsable del Evento:</strong> <?= htmlspecialchars($responsable_evento) ?></div>
                </div>
            </div>
        </div>

        <div class="seccion">
            <h2>UBICACIÓN DEL EVENTO</h2>
            <div class="ubicacion-mapa">
                <div class="direccion">
                    <div class="campo"><strong>Dirección:</strong> <?= htmlspecialchars($direccion) ?></div>
                    <div class="campo"><strong>Link:</strong> <?= htmlspecialchars($link_mapa) ?></div>
                </div>
                <div class="mapa">
                    <?php if (!empty($imagen_maps)): ?>
                        <img src="<?= PUBLIC_PATH . '/' . $imagen_maps ?>" style="max-width:100%; max-height:80px;">
                    <?php else: ?>
                        <span>Foto Google Maps</span>
                    <?php endif; ?>
                </div>
            </div>
            <div style="margin-top:6px;">
                <?php if (!empty($imagen_lugar)): ?>
                    <img src="<?= PUBLIC_PATH . '/' . $imagen_lugar ?>" style="max-width:100%; max-height:150px;">
                <?php else: ?>
                    <span style="color:#888; font-size:11px;">(Foto del lugar no disponible)</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- INTERIOR: ORDEN DEL DÍA Y PRESÍDIUM -->
    <!-- ============================================================ -->
    <div class="page interior">
        <div class="encabezado">
            <div class="logo-toluca">
                TOLUCA<br>
                <small>CAPITAL DE OPORTUNIDADES Y PROGRESO</small>
            </div>
            <div style="text-align:right; font-weight:bold; color:#800000; font-size:14px;">
                H. Ayuntamiento de Toluca
            </div>
        </div>

        <div class="seccion">
            <h2>ORDEN DEL DÍA</h2>
            <?php if (!empty($ordenes)): ?>
                <table>
                    <thead>
                        <tr><th>Hora</th><th>Actividad</th><th>Responsable</th><th>Duración (min)</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordenes as $o): ?>
                            <tr>
                                <td><?= substr($o['hora_inicio'], 0, 5) ?></td>
                                <td><?= htmlspecialchars($o['actividad'] ?? '') ?></td>
                                <td><?= htmlspecialchars($o['responsable_nombre'] ?? '') ?></td>
                                <td>
                                    <?php
                                        $inicio = new DateTime($o['hora_inicio']);
                                        $fin = new DateTime($o['hora_fin']);
                                        echo ($fin->getTimestamp() - $inicio->getTimestamp()) / 60;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top:6px; font-weight:bold; color:#800000;">
                    Duración total del evento: <?= $duracion_str ?>
                </div>
            <?php else: ?>
                <p style="color:#888;">No se ha registrado orden del día.</p>
            <?php endif; ?>
        </div>

        <div class="seccion">
            <h2>PRESÍDIUM</h2>
            <div class="presidium-grid">
                <?php
                $presidiumList = $presidium;
                if (empty($presidiumList)) {
                    // Fila fija del Presidente
                    echo '<div class="item"><span class="orden">*</span><span class="nombre-cargo"><strong>Lcdo. Ricardo Moreno Bastida</strong><br>Presidente Municipal Constitucional de Toluca</span></div>';
                } else {
                    // Ordenar según el tipo (1 a la derecha, 2 a la izquierda, etc.)
                    $ordenados = [];
                    foreach ($presidiumList as $p) {
                        if ($p['nombre_invitado'] == 'Lcdo. Ricardo Moreno Bastida') continue;
                        $ordenados[] = $p;
                    }
                    // Simular orden: 1,2,3,... (en la vista se maneja con CSS)
                    $i = 1;
                    foreach ($ordenados as $p) {
                        $lado = ($i % 2 == 0) ? 'derecha' : 'izquierda';
                        echo '<div class="item"><span class="orden">' . $i . '</span><span class="nombre-cargo"><strong>' . htmlspecialchars($p['nombre_invitado']) . '</strong><br>' . htmlspecialchars($p['cargo_invitado']) . '</span></div>';
                        $i++;
                    }
                }
                ?>
            </div>
        </div>

        <div class="seccion">
            <h2>CROQUIS DEL EVENTO</h2>
            <div class="croquis">
                <?php if (!empty($imagen_croquis)): ?>
                    <img src="<?= PUBLIC_PATH . '/' . $imagen_croquis ?>" alt="Croquis">
                <?php else: ?>
                    <span>Colocar croquis / distribución</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- INTERIOR: INVITADOS, MÓDULOS Y REQUERIMIENTOS -->
    <!-- ============================================================ -->
    <div class="page interior">
        <div class="encabezado">
            <div class="logo-toluca">
                TOLUCA<br>
                <small>CAPITAL DE OPORTUNIDADES Y PROGRESO</small>
            </div>
            <div style="text-align:right; font-weight:bold; color:#800000; font-size:14px;">
                H. Ayuntamiento de Toluca
            </div>
        </div>

        <div class="seccion">
            <h2>INVITADOS</h2>
            <div style="font-size:12px; margin-bottom:4px;"><strong>INTEGRANTES DE CABILDO Y GABINETE</strong></div>
            <?php $invitados = $evento['invitados_especiales'] ?? []; ?>
            <?php if (!empty($invitados)): ?>
                <table>
                    <thead><tr><th>N°</th><th>Persona invitada</th></tr></thead>
                    <tbody>
                        <?php foreach ($invitados as $idx => $inv): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><strong><?= htmlspecialchars($inv['nombre'] ?? '') ?></strong><br><?= htmlspecialchars($inv['cargo'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color:#888;">No hay invitados especiales registrados.</p>
            <?php endif; ?>
        </div>

        <div class="seccion">
            <h2>MÓDULOS JORNADA INTEGRAL</h2>
            <?php $modulos = $evento['modulos_jornada'] ?? []; ?>
            <?php if (!empty($modulos)): ?>
                <table>
                    <thead><tr><th>N°</th><th>Institución</th><th>Servicio</th></tr></thead>
                    <tbody>
                        <?php foreach ($modulos as $idx => $m): ?>
                            <tr><td><?= $idx + 1 ?></td><td><?= htmlspecialchars($m['institucion'] ?? '') ?></td><td><?= htmlspecialchars($m['servicio'] ?? '') ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color:#888;">No hay módulos registrados.</p>
            <?php endif; ?>
        </div>

        <div class="seccion">
            <h2>REQUERIMIENTOS</h2>
            <div style="font-weight:700; color:#800000; margin-top:6px;">Requerimientos Internos</div>
            <?php $internos = $evento['requerimientos_internos'] ?? []; ?>
            <?php if (!empty($internos)): ?>
                <table>
                    <thead><tr><th>Cant.</th><th>Insumo</th><th>Medida</th><th>Unidad</th></tr></thead>
                    <tbody>
                        <?php foreach ($internos as $item): ?>
                            <tr><td><?= $item['cantidad'] ?? 1 ?></td><td><?= htmlspecialchars($item['nombre_insumo'] ?? '') ?></td><td><?= htmlspecialchars($item['medida'] ?? '') ?></td><td><?= htmlspecialchars($item['unidad'] ?? '') ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color:#888;">Sin requerimientos internos.</p>
            <?php endif; ?>

            <div style="font-weight:700; color:#800000; margin-top:12px;">Requerimientos Externos (Dirección General de Administración)</div>
            <?php $externos = $evento['requerimientos_externos'] ?? []; ?>
            <?php if (!empty($externos)): ?>
                <table>
                    <thead><tr><th>Cant.</th><th>Insumo</th><th>Medida</th><th>Unidad</th></tr></thead>
                    <tbody>
                        <?php foreach ($externos as $item): ?>
                            <tr><td><?= $item['cantidad'] ?? 1 ?></td><td><?= htmlspecialchars($item['nombre_insumo'] ?? '') ?></td><td><?= htmlspecialchars($item['medida'] ?? '') ?></td><td><?= htmlspecialchars($item['unidad'] ?? '') ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color:#888;">Sin requerimientos externos.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- INTERIOR: FIRMAS FINALES -->
    <!-- ============================================================ -->
    <div class="page interior">
        <div class="encabezado">
            <div class="logo-toluca">
                TOLUCA<br>
                <small>CAPITAL DE OPORTUNIDADES Y PROGRESO</small>
            </div>
            <div style="text-align:right; font-weight:bold; color:#800000; font-size:14px;">
                H. Ayuntamiento de Toluca
            </div>
        </div>

        <div class="seccion">
            <h2>REQUERIMIENTOS FINALES</h2>
            <div class="campo"><strong>Evento:</strong> <?= htmlspecialchars($evento['nombre_evento'] ?? '') ?></div>
            <div class="campo"><strong>Día:</strong> <?= date('d \d\e F \d\e Y', strtotime($registro['fecha_inicio'])) ?></div>
            <div class="campo"><strong>Horario:</strong> <?= substr($registro['hora_inicio'], 0, 5) . ' - ' . substr($registro['hora_fin'], 0, 5) ?></div>
            <div class="campo"><strong>Ubicación:</strong> <?= htmlspecialchars($direccion) ?></div>
            <div style="margin-top:10px;">
                <div class="campo"><strong>Delegación Administrativa - Resumen:</strong></div>
                <p style="font-size:12px; background:#f9f9f9; padding:8px; border-radius:4px;"><?= nl2br(htmlspecialchars($evento['delegacion_admin_resumen'] ?? '')) ?></p>
            </div>
        </div>

        <div class="seccion">
            <h2>FIRMAS</h2>
            <div class="firmas">
                <div class="firma">
                    <div class="linea"></div>
                    <div class="nombre"><?= $firma1 ?></div>
                    <div class="cargo">Coordinador de Apoyo Técnico</div>
                </div>
                <div class="firma">
                    <div class="linea"></div>
                    <div class="nombre"><?= $firma2 ?></div>
                    <div class="cargo">Delegado Administrativo</div>
                </div>
            </div>
            <div style="margin-top:10px; text-align:center; font-size:11px; color:#888;">
                Vo. Bo. Validado
            </div>
        </div>
    </div>
</body>
</html>