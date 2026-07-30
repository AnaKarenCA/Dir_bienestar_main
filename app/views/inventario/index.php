<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Insumos | DG Bienestar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: #f4f6f8;
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 24px 28px;
            color: #2C241A;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ===== CONTENEDOR PRINCIPAL (estilo vidrio) ===== */
        .inventario-container {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 30px 28px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            margin-top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* ===== HEADER ===== */
        .inventario-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 28px;
            border-bottom: 2px solid rgba(128, 0, 0, 0.15);
            padding-bottom: 16px;
        }

        .inventario-header h1 {
            color: #800000;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .btn-agregar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #800000;
            color: white;
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.25);
            border: none;
        }

        .btn-agregar:hover {
            background: #660000;
            transform: scale(1.03);
            box-shadow: 0 6px 20px rgba(128, 0, 0, 0.35);
            color: white;
            text-decoration: none;
        }

        .btn-agregar .material-symbols-outlined {
            font-size: 1.3rem;
        }

        /* ===== TABLA ===== */
        .inventario-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            background: transparent;
        }

        .inventario-table thead th {
            color: #800000;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 2px solid rgba(128, 0, 0, 0.2);
            background: rgba(128, 0, 0, 0.04);
            text-align: left;
        }

        .inventario-table tbody tr {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(4px);
            transition: all 0.2s ease;
            border-radius: 12px;
        }

        .inventario-table tbody tr:hover {
            background: rgba(128, 0, 0, 0.06);
            transform: scale(1.003);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .inventario-table td {
            padding: 14px 16px;
            vertical-align: middle;
            border: none;
            font-size: 0.95rem;
        }

        .inventario-table td:first-child {
            border-radius: 12px 0 0 12px;
        }
        .inventario-table td:last-child {
            border-radius: 0 12px 12px 0;
        }

        /* ===== BOTONES DE ACCIÓN ===== */
        .acciones {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-accion {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            background: rgba(128, 0, 0, 0.08);
            color: #800000;
            border-color: rgba(128, 0, 0, 0.15);
        }

        .btn-accion:hover {
            background: rgba(128, 0, 0, 0.15);
            color: #660000;
            text-decoration: none;
        }

        .btn-accion.eliminar {
            background: rgba(211, 47, 47, 0.08);
            color: #b71c1c;
            border-color: rgba(211, 47, 47, 0.15);
        }

        .btn-accion.eliminar:hover {
            background: rgba(211, 47, 47, 0.15);
            color: #880e0e;
        }

        .btn-accion .material-symbols-outlined {
            font-size: 1.1rem;
        }

        /* ===== MENSAJE VACÍO ===== */
        .empty {
            text-align: center;
            padding: 40px 20px;
            color: #7A5A3A;
            font-size: 1.1rem;
            background: rgba(0,0,0,0.02);
            border-radius: 16px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            body { padding: 16px; }
            .inventario-container {
                padding: 18px 14px;
                border-radius: 20px;
            }
            .inventario-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            .inventario-header h1 {
                font-size: 1.4rem;
                text-align: center;
            }
            .btn-agregar {
                justify-content: center;
                padding: 12px 20px;
            }
            .inventario-table thead {
                display: none;
            }
            .inventario-table tbody tr {
                display: block;
                margin-bottom: 16px;
                background: rgba(255, 255, 255, 0.8);
                padding: 16px;
                border-radius: 16px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            }
            .inventario-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid rgba(0, 0, 0, 0.04);
                font-size: 0.9rem;
            }
            .inventario-table td:last-child {
                border-bottom: none;
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 6px;
            }
            .inventario-table td:before {
                content: attr(data-label);
                font-weight: 700;
                color: #800000;
                width: 40%;
                flex-shrink: 0;
            }
            .inventario-table td:first-child,
            .inventario-table td:last-child {
                border-radius: 0;
            }
            .acciones {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 480px) {
            body { padding: 12px; }
            .inventario-container { padding: 14px 10px; }
            .btn-agregar { font-size: 0.85rem; padding: 10px 16px; }
            .btn-accion { font-size: 0.7rem; padding: 4px 10px; }
        }
    </style>
</head>
<body>
    <?php include_once APPROOT . '/views/partials/menu.php'; ?>

    <div class="container">
        <div class="inventario-container">
            <div class="inventario-header">
                <h1>Inventario de Insumos</h1>
                <a href="/Dir_bienestar/admin/inventario/agregar" class="btn-agregar">
                    <span class="material-symbols-outlined">add</span> Agregar insumo
                </a>
            </div>

            <?php if (!empty($insumos)): ?>
                <table class="inventario-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Insumo</th>
                            <th>Medida</th>
                            <th>Unidad</th>
                            <th>Stock total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($insumos as $insumo): ?>
                            <tr>
                                <td data-label="ID"><?= $insumo['id'] ?></td>
                                <td data-label="Insumo"><?= htmlspecialchars($insumo['nombre_insumo']) ?></td>
                                <td data-label="Medida"><?= htmlspecialchars($insumo['medida'] ?? '—') ?></td>
                                <td data-label="Unidad"><?= htmlspecialchars($insumo['unidad'] ?? '—') ?></td>
                                <td data-label="Stock total"><?= $insumo['stock_total'] ?></td>
                                <td data-label="Acciones" class="acciones">
                                    <a href="/Dir_bienestar/admin/inventario/editar/<?= $insumo['id'] ?>" class="btn-accion">
                                        <span class="material-symbols-outlined">edit</span> Editar
                                    </a>
                                    <a href="/Dir_bienestar/admin/inventario/eliminar/<?= $insumo['id'] ?>" class="btn-accion eliminar" onclick="return confirm('¿Eliminar este insumo?')">
                                        <span class="material-symbols-outlined">delete</span> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No hay insumos registrados en el inventario.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Script para agregar data-label en móviles si no están en el HTML -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Si los data-label ya están en el HTML, no es necesario, pero se asegura
            const table = document.querySelector('.inventario-table');
            if (table) {
                const headers = table.querySelectorAll('thead th');
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    cells.forEach((cell, index) => {
                        if (headers[index]) {
                            const label = headers[index].textContent.trim();
                            if (!cell.getAttribute('data-label')) {
                                cell.setAttribute('data-label', label);
                            }
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>