<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Global | Administración</title>
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
        <h1>Inventario Global</h1>
        <div class="header-actions">
            <a href="/Dir_bienestar/admin/agregar_inventario" class="btn">+ Agregar insumo</a>
            <a href="/Dir_bienestar/admin/inventario_inactivos" class="btn btn-secondary">🗑️ Ver inactivos</a>
            <div class="search-box">
                <input type="text" id="buscador" placeholder=" Buscar por nombre, medida o unidad...">
            </div>
        </div>

        <div id="tablaContainer">
            <?php if (!empty($insumos)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Insumo</th>
                            <th>Medida</th>
                            <th>Unidad</th>
                            <th>Stock</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                        <?php foreach ($insumos as $insumo): ?>
                            <tr data-nombre="<?= strtolower($insumo['nombre_insumo']) ?>" 
                                data-medida="<?= strtolower($insumo['medida'] ?? '') ?>"
                                data-unidad="<?= strtolower($insumo['unidad'] ?? '') ?>">
                                <td><?= htmlspecialchars($insumo['nombre_insumo']) ?></td>
                                <td><?= htmlspecialchars($insumo['medida'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($insumo['unidad'] ?? '—') ?></td>
                                <td><?= $insumo['stock_total'] ?></td>
                                <td>
                                    <span class="badge <?= ($insumo['tipo'] ?? 'Interno') == 'Interno' ? 'badge-interno' : 'badge-externo' ?>">
                                        <?= ($insumo['tipo'] ?? 'Interno') ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/Dir_bienestar/admin/editar_inventario/<?= $insumo['id'] ?>" class="btn-accion">Editar</a>
                                    <a href="#" onclick="eliminar(<?= $insumo['id'] ?>)" class="btn-accion btn-danger">Desactivar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No hay insumos activos registrados.</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Buscador en vivo
        document.getElementById('buscador').addEventListener('input', function() {
            const texto = this.value.toLowerCase().trim();
            const filas = document.querySelectorAll('#tablaBody tr');
            let visible = 0;
            filas.forEach(fila => {
                const nombre = fila.dataset.nombre || '';
                const medida = fila.dataset.medida || '';
                const unidad = fila.dataset.unidad || '';
                const coincide = nombre.includes(texto) || medida.includes(texto) || unidad.includes(texto);
                fila.style.display = coincide ? '' : 'none';
                if (coincide) visible++;
            });
            // Mostrar mensaje si no hay resultados
            const container = document.getElementById('tablaContainer');
            let mensaje = container.querySelector('.no-resultados');
            if (visible === 0) {
                if (!mensaje) {
                    mensaje = document.createElement('div');
                    mensaje.className = 'empty no-resultados';
                    mensaje.textContent = 'No se encontraron insumos que coincidan con la búsqueda.';
                    container.appendChild(mensaje);
                }
            } else {
                if (mensaje) mensaje.remove();
            }
        });

        // Desactivar (eliminación lógica)
        async function eliminar(id) {
            if (!confirm('¿Desactivar este insumo? Podrá restaurarse desde "Ver inactivos".')) return;
            try {
                const res = await fetch('/Dir_bienestar/admin/eliminar_inventario/' + id, { method: 'POST' });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo desactivar'));
                }
            } catch (e) {
                alert('Error de conexión');
            }
        }
    </script>
</body>
</html>