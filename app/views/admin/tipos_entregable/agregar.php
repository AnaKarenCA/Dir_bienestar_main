<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tipo de Entregable</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        /* (Mismos estilos de la vista index, o puedes reutilizar) */
        :root { --vino: #800000; --vino3: #611232; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { background: #f3f3f3; color: #333; }
        .header-admin { background: white; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        .encabezado { display: flex; justify-content: space-between; align-items: center; padding: 18px 40px; flex-wrap: wrap; }
        .identidad { display: flex; align-items: center; gap: 20px; }
        .identidad img { height: 65px; width: auto; }
        .titulo-sistema h1 { font-size: 30px; color: var(--vino); margin-bottom: 6px; }
        .titulo-sistema p { color: #777; font-size: 15px; }
        .usuario-admin { text-align: right; }
        .usuario-admin h3 { color: var(--vino); margin-bottom: 4px; }
        .usuario-admin small { color: #777; display: block; }
        .menu-superior { background: var(--vino); padding: 0; }
        .menu-superior nav { display: flex; align-items: center; justify-content: center; flex-wrap: wrap; }
        .menu-superior nav ul { display: flex; list-style: none; flex-wrap: wrap; }
        .menu-superior nav ul li a { display: block; padding: 15px 24px; color: white; text-decoration: none; font-size: 15px; transition: .3s; }
        .menu-superior nav ul li a:hover { background: var(--vino3); }

        .contenido { max-width: 700px; margin: 35px auto; padding: 0 30px; }
        .contenido h2 { color: var(--vino); margin-bottom: 25px; font-size: 28px; display: flex; align-items: center; gap: 12px; }

        .form-card { background: white; border-radius: 14px; padding: 30px; box-shadow: 0 5px 16px rgba(0,0,0,.08); }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: 700; font-size: 0.85rem; color: var(--vino); margin-bottom: 4px; }
        .form-group input { width: 100%; padding: 10px 14px; border-radius: 40px; border: 1.5px solid #DBCAB2; font-size: 0.95rem; background: white; }
        .form-group input:focus { border-color: var(--vino); outline: none; }

        .acciones { display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap; }
        .btn { padding: 10px 28px; border-radius: 40px; border: none; font-weight: 700; cursor: pointer; transition: 0.15s; font-size: 0.85rem; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--vino); color: white; }
        .btn-primary:hover { background: var(--vino3); }
        .btn-secondary { background: #E7DAC8; color: #5E3E22; }
        .btn-secondary:hover { background: #D4C3AB; }

        .mensaje { padding: 10px 16px; border-radius: 24px; margin-top: 12px; font-size: 0.85rem; display: none; }
        .mensaje.exito { background: #d4edda; color: #155724; display: block; }
        .mensaje.error { background: #f8d7da; color: #721c24; display: block; }

        .footer-admin { margin-top: 45px; padding: 20px; text-align: center; font-size: 13px; color: #777; border-top: 1px solid #ddd; }
        .footer-admin strong { color: #800000; }
    </style>
</head>
<body>
<header class="header-admin">
    <div class="encabezado">
        <div class="identidad">
            <img src="/img/logo_d_bienestar.png" alt="Toluca">
            <div class="titulo-sistema">
                <h1>Sistema Integral de Actividades</h1>
                <p>Panel de Administración</p>
            </div>
        </div>
        <div class="usuario-admin">
            <h3><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></h3>
            <small>Administrador del Sistema</small>
            <small><?= date('d/m/Y H:i') ?></small>
        </div>
    </div>
    <div class="menu-superior">
        <?php require APPROOT.'/views/partials/menu.php'; ?>
    </div>
</header>

<div class="contenido">
    <h2><span class="material-symbols-outlined">add</span> Agregar Tipo de Entregable</h2>
    <div class="form-card">
        <form id="formAgregar">
            <div class="form-group">
                <label>Nombre del tipo de entregable *</label>
                <input type="text" name="nombre_entregable" placeholder="Ej: Carpeta, Oficio, Ficha Técnica" required>
            </div>
            <div id="mensaje" class="mensaje"></div>
            <div class="acciones">
                <button type="submit" class="btn btn-primary">💾 Guardar</button>
                <a href="/Dir_bienestar/admin/tipos_entregable" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<div class="footer-admin">
    <strong>Sistema Integral de Actividades</strong><br>
    Dirección General de Bienestar · Panel de Administración
</div>

<script>
document.getElementById('formAgregar').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    const mensaje = document.getElementById('mensaje');
    mensaje.className = 'mensaje';
    mensaje.style.display = 'none';

    try {
        const res = await fetch('/Dir_bienestar/admin/guardar_tipo_entregable', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            mensaje.className = 'mensaje exito';
            mensaje.textContent = '✅ Tipo de entregable agregado correctamente. Redirigiendo...';
            mensaje.style.display = 'block';
            setTimeout(() => window.location.href = '/Dir_bienestar/admin/tipos_entregable', 1200);
        } else {
            mensaje.className = 'mensaje error';
            mensaje.textContent = '❌ ' + (result.error || 'Error al guardar');
            mensaje.style.display = 'block';
        }
    } catch (e) {
        mensaje.className = 'mensaje error';
        mensaje.textContent = '❌ Error de conexión';
        mensaje.style.display = 'block';
    }
});
</script>
</body>
</html>