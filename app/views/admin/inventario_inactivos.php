<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Insumos Inactivos</title>
     <style>
        body {
            background: #FEF7F0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 24px;
            color: #2C241A;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #800000;
            border-bottom: 2px solid #E6D8C8;
            padding-bottom: 12px;
        }
        .header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin: 20px 0;
        }
        .header-actions .btn {
            background: #800000;
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .header-actions .btn:hover {
            background: #5a0000;
        }
        .header-actions .btn-secondary {
            background: #E7DAC8;
            color: #5E3E22;
        }
        .header-actions .btn-secondary:hover {
            background: #D4C3AB;
        }
        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }
        .search-box input {
            padding: 8px 16px;
            border: 1.5px solid #DBCAB2;
            border-radius: 40px;
            font-size: 0.85rem;
            min-width: 220px;
        }
        .search-box input:focus {
            border-color: #800000;
            outline: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        th {
            background: #800000;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #EDE0D2;
        }
        tr:hover td {
            background: #FCF5EA;
        }
        .btn-accion {
            background: #E7DAC8;
            border: none;
            padding: 4px 12px;
            border-radius: 40px;
            font-weight: 600;
            color: #5E3E22;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.75rem;
            margin: 2px 0;
        }
        .btn-accion:hover {
            background: #D4C3AB;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .badge-interno {
            background: #d4edda;
            color: #155724;
        }
        .badge-externo {
            background: #fff3cd;
            color: #856404;
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: #AB8E66;
        }
        .hidden-row {
            display: none;
        }
        @media (max-width: 850px) {
            .search-box {
                margin-left: 0;
                width: 100%;
            }
            .search-box input {
                width: 100%;
            }
            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <?php include_once APPROOT . '/views/partials/menu.php'; ?>

    <div class="container">
        <h1>🗑️ Insumos Inactivos</h1>
        <div class="header-actions">
            <a href="/Dir_bienestar/admin/inventario" class="btn">← Volver a activos</a>
        </div>

        <?php if (!empty($insumos)): ?>
            <table>
                <thead>
                    <tr><th>ID</th><th>Insumo</th><th>Unidad</th><th>Stock</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($insumos as $insumo): ?>
                        <tr>
                            <td><?= $insumo['id'] ?></td>
                            <td><?= htmlspecialchars($insumo['nombre_insumo']) ?></td>
                            <td><?= htmlspecialchars($insumo['unidad'] ?? '—') ?></td>
                            <td><?= $insumo['stock_total'] ?></td>
                            <td>
                                <a href="#" onclick="restaurar(<?= $insumo['id'] ?>)" class="btn">Restaurar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty">No hay insumos inactivos.</div>
        <?php endif; ?>
    </div>

    <script>
        async function restaurar(id) {
            if (!confirm('¿Restaurar este insumo?')) return;
            try {
                const res = await fetch('/Dir_bienestar/admin/restaurar_inventario/' + id, { method: 'POST' });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo restaurar'));
                }
            } catch (e) {
                alert('Error de conexión');
            }
        }
    </script>
</body>
</html>