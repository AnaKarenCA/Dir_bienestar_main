<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Evidencias | DG Bienestar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        :root {
            --principal: #800000;
            --verde: #2e7d32;
            --amarillo: #f9a825;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        body { background:#f4f6f8; color:#333; padding:24px 28px; }
        .container { max-width:1400px; margin:0 auto; }
        .header { display:flex; align-items:center; gap:24px; flex-wrap:wrap; margin-bottom:28px; border-bottom:2px solid #E6D8C8; padding-bottom:16px; }
        .title-area h1 { font-size:1.8rem; color:var(--principal); font-weight:800; }
        .title-area p { color:#7A5A3A; font-weight:500; }

        .volver { display:inline-block; margin-bottom:20px; color:var(--principal); text-decoration:none; font-weight:600; }
        .volver:hover { text-decoration:underline; }

        .evidencias-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(320px,1fr)); gap:24px; }
        .ev-card { background:white; border-radius:20px; padding:20px; box-shadow:0 6px 14px rgba(0,0,0,0.06); border-top:6px solid var(--principal); }
        .ev-card h3 { color:var(--principal); margin-bottom:10px; }
        .ev-card .foto { width:100%; height:200px; border-radius:12px; overflow:hidden; background:#eee; margin-bottom:12px; }
        .ev-card .foto img { width:100%; height:100%; object-fit:cover; }
        .ev-card .foto .sin-foto { display:flex; align-items:center; justify-content:center; height:100%; color:#999; }
        .ev-card .info { font-size:0.85rem; line-height:1.6; }
        .ev-card .info strong { color:var(--principal); }
        .ev-card .estado { display:inline-block; padding:2px 12px; border-radius:40px; font-size:0.7rem; font-weight:700; color:white; }
        .estado.registrada { background:var(--verde); }
        .estado.pendiente { background:var(--amarillo); }

        .mapa-mini { width:100%; height:200px; border-radius:12px; border:1px solid #ddd; margin-top:10px; }

        .acciones { margin-top:16px; display:flex; gap:8px; flex-wrap:wrap; }
        .btn-accion { background:#e0e0e0; color:#333; border:none; padding:6px 14px; border-radius:40px; font-size:0.75rem; cursor:pointer; transition:0.15s; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
        .btn-accion:hover { background:#d0d0d0; }
        .btn-accion.danger { background:#e74c3c; color:white; }
        .btn-accion.danger:hover { background:#c0392b; }

        .empty-msg { text-align:center; padding:40px; color:#AB8E66; font-size:0.9rem; }
    </style>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<div class="container">
    <div class="header">
        <div class="title-area">
            <h1>Evidencias</h1>
            <p><?= htmlspecialchars($actividad['actividad_desc'] ?? 'Actividad') ?></p>
        </div>
    </div>

    <a href="/Dir_bienestar/evidencias/index" class="volver">← Volver a mis actividades</a>

    <?php if (empty($evidencias)): ?>
        <div class="empty-msg"> No hay evidencias registradas para esta actividad.</div>
    <?php else: ?>
        <div class="evidencias-grid">
            <?php
            $tiposNombres = [
                'llegada' => 'Llegada',
                'durante' => 'Durante',
                'finalizacion' => 'Finalización'
            ];
            foreach ($evidencias as $ev):
                $tipoLabel = $tiposNombres[$ev['tipo']] ?? $ev['tipo'];
                $tieneFoto = !empty($ev['fotografia']);
                $tieneUbicacion = !empty($ev['latitud']) && !empty($ev['longitud']);
            ?>
            <div class="ev-card">
                <h3><?= $tipoLabel ?></h3>

                <div class="foto">
                    <?php if ($tieneFoto): ?>
                        <img src="/<?= htmlspecialchars($ev['fotografia']) ?>" alt="Evidencia">
                    <?php else: ?>
                        <div class="sin-foto">📷 Sin fotografía</div>
                    <?php endif; ?>
                </div>

                <div class="info">
                    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($ev['fecha'])) ?></p>
                    <p><strong>Hora:</strong> <?= date('H:i', strtotime($ev['hora'])) ?></p>
                    <p>
                        <strong>Ubicación:</strong>
                        <?php if ($tieneUbicacion): ?>
                            <?= number_format($ev['latitud'], 6) ?>, <?= number_format($ev['longitud'], 6) ?>
                            <?php if (!empty($ev['precision_geolocalizacion'])): ?>
                                (± <?= round($ev['precision_geolocalizacion']) ?> m)
                            <?php endif; ?>
                        <?php else: ?>
                            No registrada
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($ev['comentarios'])): ?>
                        <p><strong>Comentarios:</strong> <?= nl2br(htmlspecialchars($ev['comentarios'])) ?></p>
                    <?php endif; ?>
                    <p>
                        <strong>Estado:</strong>
                        <span class="estado registrada">Registrada</span>
                    </p>
                </div>

                <?php if ($tieneUbicacion): ?>
                    <div id="mapa_<?= $ev['id'] ?>" class="mapa-mini"></div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var map = L.map('mapa_<?= $ev['id'] ?>').setView([<?= $ev['latitud'] ?>, <?= $ev['longitud'] ?>], 14);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap'
                            }).addTo(map);
                            L.marker([<?= $ev['latitud'] ?>, <?= $ev['longitud'] ?>]).addTo(map);
                        });
                    </script>
                <?php endif; ?>

                <div class="acciones">
                    <a href="/Dir_bienestar/evidencias/registrar/<?= $actividad['id'] ?>?tipo=<?= $ev['tipo'] ?>" class="btn-accion">
                        <span class="material-symbols-outlined" style="font-size:1rem;">edit</span> Editar
                    </a>
                    <button class="btn-accion danger" onclick="eliminarEvidencia(<?= $ev['id'] ?>, <?= $actividad['id'] ?>)">
                        <span class="material-symbols-outlined" style="font-size:1rem;">delete</span> Eliminar
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// ============================================================
// ELIMINAR EVIDENCIA (AJAX)
// ============================================================
function eliminarEvidencia(id, registroId) {
    if (!confirm('¿Eliminar esta evidencia? No se podrá recuperar.')) return;

    fetch('/Dir_bienestar/evidencias/eliminar/' + id, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.mensaje);
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(() => alert('Error de conexión.'));
}
</script>
</body>
</html>