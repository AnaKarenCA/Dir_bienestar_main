<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar PowerPoint desde Registro de Actividad</title>
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 24px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: #1e4663;
        }
        select {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #bdc3c7;
            font-size: 1rem;
            margin-bottom: 20px;
        }
        button {
            width: 100%;
            padding: 14px;
            background: #901216;
            color: white;
            border: none;
            border-radius: 40px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }
        button:hover {
            background: #c2082a;
            transform: scale(1.02);
        }
        .note {
            font-size: 0.8rem;
            color: #7f8c8d;
            text-align: center;
            margin-top: 20px;
        }
        .empty {
            text-align: center;
            color: #e67e22;
            padding: 20px;
            background: #fef9e7;
            border-radius: 12px;
        }
    </style>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>
    <div class="container">
        <h1>Generar PowerPoint desde Actividad</h1>
        <form action="/Dir_bienestar/evento_ppt/generar" method="POST">
            <label for="id_registro">Seleccione un registro de actividad con Carpeta:</label>
            <select name="id_registro" id="id_registro" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($registros as $r): ?>
                    <option value="<?= $r['id'] ?>">
                        <?= htmlspecialchars($r['actividad_desc'] ?? 'Actividad') . ' (' . $r['fecha_inicio'] . ')' ?>
                        <?= $r['lugar_nombre'] ? ' - ' . $r['lugar_nombre'] : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Generar PowerPoint</button>
        </form>
        <?php if (empty($registros)): ?>
            <div class="empty">⚠️ No hay registros de actividad con tipo de entregable "Carpeta".</div>
        <?php endif; ?>
        <p class="note">La presentación incluirá los datos del registro, lugar, responsable, beneficiarios y más.</p>
    </div>
</body>
</html>