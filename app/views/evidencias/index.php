<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Actividades | DG Bienestar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        :root {
            --principal: #800000;
            --principal-hover: #660000;
            --gris: #f5f5f5;
            --texto: #333;
            --verde: #05710a;
            --amarillo: #b46f00;
            --rojo: #c62828;
            --blanco: #ffffff;
            --borde: #d9d9d9;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        body { background:#f4f6f8; color:var(--texto); padding:24px 28px; }
        .container { max-width:1400px; margin:0 auto; }
        .header { display:flex; align-items:center; gap:24px; flex-wrap:wrap; margin-bottom:28px; border-bottom:2px solid #E6D8C8; padding-bottom:16px; }
        .logo-area img { height:70px; width:auto; }
        .title-area h1 { font-size:1.8rem; color:var(--principal); font-weight:800; }
        .title-area p { color:#7A5A3A; font-weight:500; }

        .tabla-container { background:white; border-radius:28px; padding:20px; box-shadow:0 6px 14px rgba(0,0,0,0.04); overflow-x:auto; }
        .tabla-container h2 { color:var(--principal); margin-bottom:16px; font-size:1.2rem; }

        table { width:100%; border-collapse:collapse; font-size:0.85rem; min-width:700px; }
        th { background:var(--principal); color:white; padding:12px 10px; text-align:left; font-weight:600; white-space:nowrap; }
        td { border-bottom:1px solid #EDE0D2; padding:10px; vertical-align:middle; }
        tr:hover td { background:#FCF5EA; }

        .estado-badge { display:inline-block; padding:4px 14px; border-radius:40px; font-size:0.7rem; font-weight:700; color:white; white-space:nowrap; }
        .estado-pendiente { background:var(--amarillo); }
        .estado-proceso { background:#f39c12; }
        .estado-completada { background:var(--verde); }

        .btn-accion {
            background:var(--principal);
            color:white;
            border:none;
            padding:8px 16px;
            border-radius:40px;
            font-size:0.75rem;
            font-weight:600;
            cursor:pointer;
            transition:0.15s;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }
        .btn-accion:hover { background:var(--principal-hover); }
        .btn-accion.success { background:var(--verde); }
        .btn-accion.success:hover { background:#1b5e20; }
        .btn-accion .material-symbols-outlined { font-size:1.1rem; }

        .empty-msg { text-align:center; padding:40px; color:#AB8E66; font-size:0.9rem; }

footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.7rem;
            color: #80062a;
        }    </style>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<div class="container">
    <div class="header">
        <div class="logo-area">
            <img src="/img/logo_d_bienestar.png" alt="DG Bienestar" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 70%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%23800000%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 fill=%22white%22 text-anchor=%22middle%22%3EDGB%3C/text%3E%3C/svg%3E'">
        </div>
        <div class="title-area">
            <h1>Mis Actividades</h1>
            <p>Selecciona una actividad para registrar o ver sus evidencias</p>
        </div>
    </div>

    <div class="tabla-container">
        <h2>Actividades asignadas</h2>

        <?php if (empty($actividades)): ?>
            <div class="empty-msg">No tienes actividades asignadas.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($actividades as $act): ?>
                        <tr>
                            <td><?= htmlspecialchars($act['actividad_desc'] ?? 'Sin descripción') ?></td>
                            <td><?= date('d/m/Y', strtotime($act['fecha_inicio'])) ?></td>
                            <td><?= date('H:i', strtotime($act['hora_inicio'])) . ' - ' . date('H:i', strtotime($act['hora_fin'])) ?></td>
                            <td><?= htmlspecialchars($act['lugar_nombre'] ?? 'No especificado') ?></td>
                            <td>
                                <span class="estado-badge estado-<?= $act['estado_clase'] ?>">
                                    <?= $act['estado_texto'] ?>
                                </span>
                                <span style="font-size:0.7rem;color:#888;display:block;">
                                    <?= $act['evidencias_count'] ?>/3 evidencias
                                </span>
                            </td>
                            <td>
                                <?php if ((int)$act['evidencias_count'] == 3): ?>
                                    <a href="/Dir_bienestar/evidencias/ver/<?= $act['id'] ?>" class="btn-accion success">
                                        <span class="material-symbols-outlined">visibility</span> Ver evidencias
                                    </a>
                                <?php else: ?>
                                    <a href="/Dir_bienestar/evidencias/detalle/<?= $act['id'] ?>" class="btn-accion">
                                        <span class="material-symbols-outlined">edit</span> Registrar evidencias
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <footer>Hecho por: <br> Carmona Aviles Ana Karen <br> Onofre Garcia Halem <br> Oscar Arturo Díaz Duran</footer></div>
</body>
</html>