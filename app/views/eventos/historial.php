<?php include_once APPROOT . '/views/partials/menu.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial | DG Bienestar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        body { background: #FEF7F0; font-family: 'Segoe UI', Roboto, sans-serif; padding: 24px 28px; color: #2C241A; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #E6D8C8; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { color: #800000; font-size: 1.8rem; display: flex; align-items: center; gap: 8px; }
        .section { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
        th { background: #800000; color: white; padding: 10px 12px; text-align: left; }
        td { padding: 10px 12px; border-bottom: 1px solid #EDE0D2; }
        tr:hover td { background: #FCF5EA; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 40px; font-size: 0.7rem; font-weight: 600; color: white; }
        .badge-subida { background: #27ae60; }
        .badge-guardar { background: #3498db; }
        .badge-aprobar { background: #2e86c1; }
        .badge-rechazar { background: #e74c3c; }
        .badge-correcciones { background: #f39c12; }
        .btn { padding: 8px 20px; border-radius: 40px; border: none; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; background: #E7DAC8; color: #5E3E22; transition: 0.15s; }
        .btn:hover { background: #D4C3AB; }
        .footer { margin-top: 30px; text-align: center; color: #B28B60; font-size: 0.7rem; padding: 16px 0; border-top: 1px solid #EDE0D2; }
        .empty-msg { text-align: center; padding: 40px; color: #AB8E66; font-size: 0.9rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>
            <span class="material-symbols-outlined" style="font-size:2rem;">history</span>
            Historial de la carpeta
        </h1>
        <a href="/Dir_bienestar/eventos/index?tipo=1" class="btn">← Volver</a>
    </div>

    <div class="section">
        <?php if (empty($historial)): ?>
            <div class="empty-msg">📭 No hay registros de historial para esta carpeta.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Comentario</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($h['fecha'])) ?></td>
                            <td><?= htmlspecialchars($h['usuario_nombre'] ?? '') ?></td>
                            <td>
                                <span class="badge badge-<?= $h['accion'] ?>">
                                    <?= ucfirst(str_replace('_', ' ', $h['accion'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($h['comentario'] ?? '') ?></td>
                            <td>
                                <?php if ($h['estado_nuevo']): ?>
                                    <?= ucfirst(str_replace('_', ' ', $h['estado_nuevo'])) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="footer">
        © <?= date('Y') ?> DG Bienestar - Historial de carpeta
    </div>
</div>
</body>
</html>