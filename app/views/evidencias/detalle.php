<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Actividad | DG Bienestar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        :root {
            --principal: #800000;
            --principal-hover: #660000;
            --verde: #137818;
            --amarillo: #a86c0a;
            --gris: #f5f5f5;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        body { background:#f4f6f8; color:#333; padding:24px 28px; }
        .container { max-width:1400px; margin:0 auto; }
        .header { display:flex; align-items:center; gap:24px; flex-wrap:wrap; margin-bottom:28px; border-bottom:2px solid #E6D8C8; padding-bottom:16px; }
        .title-area h1 { font-size:1.8rem; color:var(--principal); font-weight:800; }
        .title-area p { color:#7A5A3A; font-weight:500; }

        .info-box { background:white; border-radius:28px; padding:24px; margin-bottom:25px; box-shadow:0 6px 14px rgba(0,0,0,0.04); }
        .info-box h2 { color:var(--principal); margin-bottom:15px; }
        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .info-grid p { margin:4px 0; }

        .cards-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:25px; }
        .card-ev {
            background:white; border-radius:20px; padding:24px; text-align:center;
            border-top:6px solid var(--principal);
            box-shadow:0 4px 12px rgba(0,0,0,0.06);
            transition:0.2s;
        }
        .card-ev:hover { transform:translateY(-4px); }
        .card-ev h3 { color:var(--principal); font-size:1.1rem; margin-bottom:10px; }
        .card-ev .estado { display:inline-block; padding:4px 16px; border-radius:40px; font-size:0.75rem; font-weight:700; color:white; }
        .card-ev .estado.registrada { background:var(--verde); }
        .card-ev .estado.pendiente { background:var(--amarillo); }
        .card-ev .btn-ev {
            display:inline-block; margin-top:12px; background:var(--principal); color:white;
            padding:8px 20px; border-radius:40px; text-decoration:none; font-weight:600; font-size:0.8rem;
            transition:0.15s; border:none; cursor:pointer;
        }
        .card-ev .btn-ev:hover { background:var(--principal-hover); }
        .card-ev .btn-ev.success { background:var(--verde); }
        .card-ev .btn-ev.success:hover { background:#1b5e20; }
        .card-ev .btn-ev:disabled { background:#bdbdbd; cursor:not-allowed; }

        .progreso { background:white; border-radius:20px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.04); margin-top:20px; }
        .progreso label { font-weight:700; color:var(--principal); display:block; margin-bottom:6px; }
        .progreso progress { width:100%; height:24px; border-radius:12px; }
        .progreso span { display:block; margin-top:6px; font-size:0.85rem; color:#555; }

        .btn-enviar { background:var(--verde); color:white; border:none; padding:12px 30px; border-radius:40px; font-weight:700; font-size:1rem; cursor:pointer; transition:0.15s; margin-top:16px; }
        .btn-enviar:hover { background:#1b5e20; }
        .btn-enviar:disabled { background:#bdbdbd; cursor:not-allowed; }

        .volver { display:inline-block; margin-bottom:20px; color:var(--principal); text-decoration:none; font-weight:600; }
        .volver:hover { text-decoration:underline; }

        @media(max-width:900px){ .cards-grid { grid-template-columns:1fr; } .info-grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<div class="container">
    <div class="header">
        <div class="title-area">
            <h1>Detalle de Actividad</h1>
            <p>Registra o edita las evidencias de cada momento</p>
        </div>
    </div>

    <a href="/Dir_bienestar/evidencias/index" class="volver">← Volver a mis actividades</a>

    <div class="info-box">
        <h2><?= htmlspecialchars($actividad['actividad_desc'] ?? 'Sin descripción') ?></h2>
        <div class="info-grid">
            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($actividad['fecha_inicio'])) ?></p>
            <p><strong>Horario:</strong> <?= date('H:i', strtotime($actividad['hora_inicio'])) ?> - <?= date('H:i', strtotime($actividad['hora_fin'])) ?></p>
            <p><strong>Lugar:</strong> <?= htmlspecialchars($actividad['lugar_nombre'] ?? 'No especificado') ?></p>
            <p><strong>Responsable:</strong> <?= htmlspecialchars($actividad['usuario_nombre'] ?? '') ?></p>
        </div>
        <?php if (!empty($actividad['descripcion'])): ?>
            <p style="margin-top:12px;"><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($actividad['descripcion'])) ?></p>
        <?php endif; ?>
    </div>

    <div class="cards-grid">
        <?php
        $tipos = [
    'llegada' => [
        'icon' => 'location_on',
        'label' => 'Llegada'
    ],
    'durante' => [
        'icon' => 'photo_camera',
        'label' => 'Durante la actividad'
    ],
    'finalizacion' => [
        'icon' => 'flag',
        'label' => 'Finalización'
    ]
];
        foreach ($tipos as $tipo => $info):
            $ev = $evidenciasPorTipo[$tipo] ?? null;
            $registrada = !is_null($ev);
            $estadoClase = $registrada ? 'registrada' : 'pendiente';
            $estadoTexto = $registrada ? 'Registrada' : 'Pendiente';
            $botonTexto = $registrada ? 'Editar' : 'Registrar';
            $botonClase = $registrada ? 'success' : '';
        ?>
        <div class="card-ev">
            <h3>
    <span class="material-symbols-outlined">
        <?= $info['icon'] ?>
    </span>
    <?= $info['label'] ?>
</h3>
            <span class="estado <?= $estadoClase ?>"><?= $estadoTexto ?></span>
            <br>
            <a href="/Dir_bienestar/evidencias/registrar/<?= $actividad['id'] ?>?tipo=<?= $tipo ?>" class="btn-ev <?= $botonClase ?>">
                <?= $botonTexto ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="progreso">
        <label>Progreso</label>
        <progress value="<?= $totalEvidencias ?>" max="3"></progress>
        <span><?= $totalEvidencias ?> de 3 evidencias registradas</span>

        <?php if ($totalEvidencias == 3): ?>
            <button class="btn-enviar" onclick="alert('Todas las evidencias están registradas. Puedes enviarlas para revisión.')">
                Enviar evidencias
            </button>
        <?php else: ?>
            <button class="btn-enviar" disabled>Completa las 3 evidencias para enviar</button>
        <?php endif; ?>
    </div>
</div>
</body>
</html>