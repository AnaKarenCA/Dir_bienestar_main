<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Entregable | Panel Administrativo</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        :root {
            --vino: #800000;
            --vino2: #9D2449;
            --vino3: #611232;
            --gris: #f5f5f5;
            --gris2: #ececec;
            --gris3: #d9d9d9;
            --texto: #333;
            --blanco: #fff;
            --radio: 14px;
            --sombra: 0 5px 16px rgba(0,0,0,.08);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { background: #f3f3f3; color: var(--texto); }
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
        .menu-superior nav ul li a {
            display: block;
            padding: 15px 24px;
            color: white;
            text-decoration: none;
            font-size: 15px;
            transition: .3s;
        }
        .menu-superior nav ul li a:hover { background: var(--vino3); }

        .contenido { max-width: 1200px; margin: 35px auto; padding: 0 30px; }
        .contenido h2 { color: var(--vino); margin-bottom: 25px; font-size: 28px; display: flex; align-items: center; gap: 12px; }

        .panel { background: white; border-radius: var(--radio); box-shadow: var(--sombra); overflow: hidden; }
        .panel-header { background: var(--vino); color: white; padding: 16px 22px; display: flex; align-items: center; gap: 10px; }
        .panel-header .material-symbols-outlined { font-size: 26px; }
        .panel-header h3 { font-size: 20px; font-weight: normal; }

        .tabla-wrapper { overflow-x: auto; padding: 0; }
        .tabla-admin { width: 100%; border-collapse: collapse; font-size: 14px; }
        .tabla-admin thead { background: #f8f4f0; border-bottom: 2px solid #e0d6cc; }
        .tabla-admin thead th { padding: 14px 12px; font-weight: 700; text-align: left; color: var(--vino); }
        .tabla-admin tbody td { padding: 12px; border-bottom: 1px solid #ececec; vertical-align: middle; }
        .tabla-admin tbody tr:nth-child(even) { background: #fafafa; }
        .tabla-admin tbody tr:hover { background: #f7eeee; }
        .sin-registros { text-align: center; padding: 40px !important; color: #888; font-style: italic; }
        .badge-usos { display: inline-block; padding: 2px 10px; border-radius: 40px; font-size: 0.7rem; font-weight: 700; background: #e8e0d8; color: #555; }

        .acciones-botones { display: flex; gap: 4px; flex-wrap: wrap; }
        .btn-icono { background: none; border: none; padding: 4px 6px; cursor: pointer; border-radius: 8px; transition: 0.1s; color: #555; font-size: 1.2rem; display: inline-flex; align-items: center; }
        .btn-icono:hover { background: #e8e0d8; color: var(--vino); }

        .btn { padding: 8px 20px; border-radius: 40px; border: none; font-weight: 700; cursor: pointer; transition: 0.15s; font-size: 0.8rem; text-decoration: none; display: inline-block; }
        .btn-vino { background: var(--vino); color: white; }
        .btn-vino:hover { background: var(--vino3); }
        .btn-outline { background: transparent; border: 2px solid var(--vino); color: var(--vino); }
        .btn-outline:hover { background: var(--vino); color: white; }

        .footer-admin { margin-top: 45px; padding: 20px; text-align: center; font-size: 13px; color: #777; border-top: 1px solid #ddd; }
        .footer-admin strong { color: #800000; }

        @media (max-width: 700px) {
            .encabezado { flex-direction: column; align-items: flex-start; gap: 12px; }
            .usuario-admin { text-align: left; }
            .contenido { padding: 0 15px; }
        }
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
    <h2>
        <span class="material-symbols-outlined">category</span>
        Tipos de Entregable
    </h2>

    <div style="margin-bottom:20px; display:flex; gap:12px; flex-wrap:wrap;">
        <a href="/Dir_bienestar/admin/agregar_tipo_entregable" class="btn btn-vino">+ Nuevo tipo</a>
        <a href="/Dir_bienestar/admin/dashboard" class="btn btn-outline">← Volver al dashboard</a>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="material-symbols-outlined">list</span>
            <h3>Lista de tipos de entregable</h3>
        </div>
        <div class="tabla-wrapper">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Registros asociados</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tipos)): ?>
                        <tr><td colspan="4" class="sin-registros">No hay tipos de entregable registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tipos as $t): ?>
                            <tr>
                                <td><?= $t['id'] ?></td>
                                <td><strong><?= htmlspecialchars($t['nombre_entregable']) ?></strong></td>
                                <td><span class="badge-usos"><?= $t['usos'] ?? 0 ?> registros</span></td>
                                <td>
                                    <div class="acciones-botones">
                                        <a href="/Dir_bienestar/admin/editar_tipo_entregable/<?= $t['id'] ?>" class="btn-icono" title="Editar">
                                            <span class="material-symbols-outlined">edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="footer-admin">
    <strong>Sistema Integral de Actividades</strong><br>
    Dirección General de Bienestar · Panel de Administración
</div>
</body>
</html>