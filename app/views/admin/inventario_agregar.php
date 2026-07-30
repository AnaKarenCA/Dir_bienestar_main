<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Insumo</title>
    <style>
        body {
            background: #FEF7F0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 24px;
            color: #2C241A;
        }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        h1 { color: #800000; border-bottom: 2px solid #E6D8C8; padding-bottom: 12px; }
        .field-group { margin-bottom: 15px; }
        label { display: block; font-weight: 700; font-size: 0.8rem; color: #800000; margin-bottom: 4px; }
        input { width: 100%; padding: 10px; border: 1.5px solid #DBCAB2; border-radius: 24px; font-size: 0.9rem; }
        .btn { background: #800000; color: white; border: none; padding: 12px 28px; border-radius: 40px; font-weight: 700; cursor: pointer; }
        .btn:hover { background: #5a0000; }
        .btn-secondary { background: #E7DAC8; color: #5E3E22; }
        .btn-secondary:hover { background: #D4C3AB; }
        .acciones { display: flex; gap: 12px; margin-top: 20px; }
        .error { color: #c0392b; font-size: 0.8rem; margin-top: 5px; }
    </style>
</head>
<body>
    <?php include_once APPROOT . '/views/partials/menu.php'; ?>

    <div class="container">
        <h1> Agregar Insumo</h1>
        <form id="formAgregar">
            <div class="field-group">
                <label>Nombre del insumo *</label>
                <input type="text" name="nombre_insumo" required>
            </div>
            <div class="field-group">
    <label>Tipo de insumo *</label>
    <select name="tipo" required>
        <option value="Interno">Interno</option>
        <option value="Externo">Externo</option>
    </select>
</div>
            <div class="field-group">
                <label>Medida (opcional)</label>
                <input type="text" name="medida" placeholder="Ej: 2.20 x .50">
            </div>
           
                
<div class="field-group">
    <label>Unidad *</label>
    <select name="unidad" required>
        <option value="">Seleccione...</option>
        <option value="Metros">Metros</option>
        <option value="Centímetros">Centímetros</option>
        <option value="Pulgadas">Pulgadas</option>
        <option value="Unidades">Unidades</option>
        <option value="Kilogramos">Kilogramos</option>
        <option value="Litros">Litros</option>
        <option value="Piezas">Piezas</option>
        <option value="Juegos">Juegos</option>
        <option value="Paquetes">Paquetes</option>
    </select>
</div>          
            <div class="field-group">
                <label>Stock total *</label>
                <input type="number" name="stock_total" min="0" required>
            </div>
            <div class="acciones">
                <button type="submit" class="btn">Guardar</button>
                <a href="/Dir_bienestar/admin/inventario" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
        <div id="mensaje" style="margin-top:15px;"></div>
    </div>

    <script>
        document.getElementById('formAgregar').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {};
            for (let [key, val] of formData.entries()) data[key] = val;

            try {
                const response = await fetch('/Dir_bienestar/admin/guardar_inventario', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) {
                    document.getElementById('mensaje').innerHTML = '<div style="color:#27ae60;">✅ Insumo guardado correctamente. Redirigiendo...</div>';
                    setTimeout(() => window.location.href = '/Dir_bienestar/admin/inventario', 1500);
                } else {
                    document.getElementById('mensaje').innerHTML = '<div style="color:#c0392b;">❌ ' + (result.error || 'Error al guardar') + '</div>';
                }
            } catch (err) {
                document.getElementById('mensaje').innerHTML = '<div style="color:#c0392b;">❌ Error de conexión</div>';
            }
        });
    </script>
</body>
</html>