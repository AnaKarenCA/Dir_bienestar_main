<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios | Panel Administrativo</title>
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

        /* HEADER */
        .header-admin {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }
        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 40px;
            flex-wrap: wrap;
        }
        .identidad {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .identidad img { height: 65px; width: auto; }
        .titulo-sistema h1 {
            font-size: 30px;
            color: var(--vino);
            margin-bottom: 6px;
        }
        .titulo-sistema p { color: #777; font-size: 15px; }
        .usuario-admin { text-align: right; }
        .usuario-admin h3 { color: var(--vino); margin-bottom: 4px; }
        .usuario-admin small { color: #777; display: block; }

        .menu-superior {
            background: var(--vino);
            padding: 0;
        }
        .menu-superior nav { display: flex; align-items: center; justify-content: center; }
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

        /* CONTENIDO */
        .contenido {
            max-width: 1400px;
            margin: 35px auto;
            padding: 0 30px;
        }
        .contenido h2 {
            color: var(--vino);
            margin-bottom: 25px;
            font-size: 28px;
        }

        /* BARRA DE FILTROS */
        .filtros-bar {
            background: white;
            border-radius: var(--radio);
            padding: 18px 22px;
            box-shadow: var(--sombra);
            display: flex;
            flex-wrap: wrap;
            gap: 12px 20px;
            align-items: center;
            margin-bottom: 25px;
        }
        .filtros-bar .campo {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 150px;
        }
        .filtros-bar .campo label {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--vino);
            letter-spacing: 0.3px;
        }
        .filtros-bar .campo input,
        .filtros-bar .campo select {
            padding: 8px 12px;
            border-radius: 40px;
            border: 1px solid #DBCAB2;
            background: #FFFDF9;
            font-size: 0.8rem;
            outline: none;
            width: 100%;
        }
        .filtros-bar .campo input:focus,
        .filtros-bar .campo select:focus {
            border-color: var(--vino);
        }
        .filtros-bar .acciones {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            margin-left: auto;
        }
        .btn {
            padding: 8px 20px;
            border-radius: 40px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.15s;
            font-size: 0.8rem;
        }
        .btn-vino { background: var(--vino); color: white; }
        .btn-vino:hover { background: var(--vino3); }
        .btn-outline { background: transparent; border: 2px solid var(--vino); color: var(--vino); }
        .btn-outline:hover { background: var(--vino); color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #1e8449; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }

        /* TABLA */
        .panel {
            background: white;
            border-radius: var(--radio);
            box-shadow: var(--sombra);
            overflow: hidden;
        }
        .panel-header {
            background: var(--vino);
            color: white;
            padding: 16px 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .panel-header .material-symbols-outlined { font-size: 26px; }
        .panel-header h3 { font-size: 20px; font-weight: normal; }

        .tabla-wrapper {
            overflow-x: auto;
            padding: 0;
        }
        .tabla-admin {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .tabla-admin thead {
            background: #f8f4f0;
            border-bottom: 2px solid #e0d6cc;
        }
        .tabla-admin thead th {
            padding: 14px 12px;
            font-weight: 700;
            text-align: left;
            color: var(--vino);
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .tabla-admin thead th:hover { color: var(--vino2); }
        .tabla-admin thead th .orden-icono { font-size: 0.7rem; margin-left: 4px; }
        .tabla-admin tbody td {
            padding: 12px;
            border-bottom: 1px solid #ececec;
            vertical-align: middle;
        }
        .tabla-admin tbody tr:nth-child(even) { background: #fafafa; }
        .tabla-admin tbody tr:hover { background: #f7eeee; }
        .sin-registros { text-align: center; padding: 40px !important; color: #888; font-style: italic; }

        .estado-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
        }
        .estado-Activo { background: #27ae60; }
        .estado-Inactivo { background: #f39c12; }
        .estado-Bloqueado { background: #e74c3c; }

        .acciones-botones {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        .acciones-botones .btn-icono {
            background: none;
            border: none;
            padding: 4px 6px;
            cursor: pointer;
            border-radius: 8px;
            transition: 0.1s;
            color: #555;
            font-size: 1.2rem;
            display: inline-flex;
            align-items: center;
        }
        .acciones-botones .btn-icono:hover {
            background: #e8e0d8;
            color: var(--vino);
        }
        .acciones-botones .btn-icono.verde { color: #27ae60; }
        .acciones-botones .btn-icono.rojo { color: #e74c3c; }
        .acciones-botones .btn-icono.amarillo { color: #f39c12; }

        /* PAGINACIÓN */
        .paginacion {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 22px;
            background: #f8f4f0;
            border-top: 1px solid #e0d6cc;
            flex-wrap: wrap;
            gap: 12px;
        }
        .paginacion .info {
            font-size: 0.85rem;
            color: #555;
        }
        .paginacion .botones {
            display: flex;
            gap: 6px;
        }
        .paginacion .botones button {
            padding: 4px 14px;
            border-radius: 40px;
            border: 1px solid #d0c4b8;
            background: white;
            cursor: pointer;
            font-weight: 600;
            transition: 0.1s;
        }
        .paginacion .botones button:hover {
            background: var(--vino);
            color: white;
            border-color: var(--vino);
        }
        .paginacion .botones button.activo {
            background: var(--vino);
            color: white;
            border-color: var(--vino);
        }
        .paginacion .botones button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* MODALES */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }
        .modal-overlay.activo { display: flex; }
        .modal-contenedor {
            background: white;
            border-radius: 28px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: modalFade 0.2s ease;
        }
        @keyframes modalFade {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-contenedor h2 {
            color: var(--vino);
            border-bottom: 2px solid #E6D8C8;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .modal-contenedor .campo {
            margin-bottom: 16px;
        }
        .modal-contenedor .campo label {
            display: block;
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--vino);
            margin-bottom: 4px;
        }
        .modal-contenedor .campo input,
        .modal-contenedor .campo select,
        .modal-contenedor .campo textarea {
            width: 100%;
            padding: 10px 14px;
            border-radius: 40px;
            border: 1.5px solid #DBCAB2;
            font-size: 0.9rem;
            background: white;
        }
        .modal-contenedor .campo input:focus,
        .modal-contenedor .campo select:focus {
            border-color: var(--vino);
            outline: none;
        }
        .modal-contenedor .campo .error {
            color: #e74c3c;
            font-size: 0.75rem;
            margin-top: 4px;
        }
        .modal-acciones {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #eee;
        }
        .modal-acciones .btn {
            padding: 10px 28px;
        }
        .cerrar-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #999;
            cursor: pointer;
            float: right;
            margin-top: -8px;
        }
        .cerrar-modal:hover { color: var(--vino); }

        .campo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 700px) {
            .campo-grid { grid-template-columns: 1fr; }
            .filtros-bar { flex-direction: column; align-items: stretch; }
            .filtros-bar .acciones { margin-left: 0; justify-content: flex-end; }
            .encabezado { flex-direction: column; align-items: flex-start; gap: 12px; }
            .usuario-admin { text-align: left; }
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
    <h2>👥 Gestión de Usuarios</h2>

    <!-- BARRA DE FILTROS -->
    <div class="filtros-bar">
        <div class="campo">
            <label>Buscar</label>
            <input type="text" id="busqueda" placeholder="Nombre o correo...">
        </div>
        <div class="campo">
            <label>Rol</label>
            <select id="filtroRol">
                <option value="">Todos</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['tipo_rol']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label>Unidad</label>
            <select id="filtroUnidad">
                <option value="">Todas</option>
                <?php foreach ($unidades as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label>Estatus</label>
            <select id="filtroEstatus">
                <option value="">Todos</option>
                <?php foreach ($estatuses as $est): ?>
                    <option value="<?= $est ?>"><?= $est ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="acciones">
            <button class="btn btn-vino" id="btnNuevoUsuario">+ Nuevo Usuario</button>
        </div>
    </div>

    <!-- TABLA -->
    <div class="panel">
        <div class="panel-header">
            <span class="material-symbols-outlined">groups</span>
            <h3>Lista de Usuarios</h3>
        </div>
        <div class="tabla-wrapper">
            <table class="tabla-admin" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th data-orden="u.nombre">Nombre <span class="orden-icono">↕</span></th>
                        <th data-orden="u.correo">Correo <span class="orden-icono">↕</span></th>
                        <th data-orden="u.puesto">Puesto <span class="orden-icono">↕</span></th>
                        <th data-orden="r.tipo_rol">Rol <span class="orden-icono">↕</span></th>
                        <th data-orden="ua.nombre">Unidad <span class="orden-icono">↕</span></th>
                        <th data-orden="u.estatus">Estado <span class="orden-icono">↕</span></th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaBody">
                    <tr><td colspan="7" class="sin-registros">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="paginacion" id="paginacion">
            <div class="info" id="infoPaginacion">Mostrando 0 registros</div>
            <div class="botones" id="botonesPaginacion"></div>
        </div>
    </div>
</div>

<!-- ==================== MODALES (sin cambios) ==================== -->
<!-- Modal Nuevo Usuario -->
<div class="modal-overlay" id="modalNuevo">
    <div class="modal-contenedor">
        <button class="cerrar-modal" onclick="cerrarModal('modalNuevo')">&times;</button>
        <h2>➕ Nuevo Usuario</h2>
        <form id="formNuevoUsuario">
            <div class="campo">
                <label>Nombre *</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="campo">
                <label>Correo *</label>
                <input type="email" name="correo" required>
            </div>
            <div class="campo-grid">
                <div class="campo">
                    <label>Contraseña *</label>
                    <input type="password" name="clave" required minlength="6">
                </div>
                <div class="campo">
                    <label>Confirmar contraseña *</label>
                    <input type="password" name="confirmar_clave" required minlength="6">
                </div>
            </div>
            <div class="campo">
                <label>Puesto</label>
                <input type="text" name="puesto">
            </div>
            <div class="campo-grid">
                <div class="campo">
                    <label>Rol *</label>
                    <select name="rol_id" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['tipo_rol']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Unidad Administrativa</label>
                    <select name="unidad_administrativa_id">
                        <option value="">Sin asignar</option>
                        <?php foreach ($unidades as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="campo">
                <label>Estado</label>
                <select name="estatus">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="Bloqueado">Bloqueado</option>
                </select>
            </div>
            <div id="errorNuevo" class="error" style="display:none;"></div>
            <div class="modal-acciones">
                <button type="button" class="btn btn-outline" onclick="cerrarModal('modalNuevo')">Cancelar</button>
                <button type="submit" class="btn btn-vino">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal-overlay" id="modalEditar">
    <div class="modal-contenedor">
        <button class="cerrar-modal" onclick="cerrarModal('modalEditar')">&times;</button>
        <h2>✏️ Editar Usuario</h2>
        <form id="formEditarUsuario">
            <input type="hidden" name="id">
            <div class="campo">
                <label>Nombre *</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="campo">
                <label>Correo *</label>
                <input type="email" name="correo" required>
            </div>
            <div class="campo">
                <label>Puesto</label>
                <input type="text" name="puesto">
            </div>
            <div class="campo-grid">
                <div class="campo">
                    <label>Rol *</label>
                    <select name="rol_id" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['tipo_rol']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Unidad Administrativa</label>
                    <select name="unidad_administrativa_id">
                        <option value="">Sin asignar</option>
                        <?php foreach ($unidades as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="campo">
                <label>Estado</label>
                <select name="estatus">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="Bloqueado">Bloqueado</option>
                </select>
            </div>
            <div id="errorEditar" class="error" style="display:none;"></div>
            <div class="modal-acciones">
                <button type="button" class="btn btn-outline" onclick="cerrarModal('modalEditar')">Cancelar</button>
                <button type="submit" class="btn btn-vino">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ver Usuario -->
<div class="modal-overlay" id="modalVer">
    <div class="modal-contenedor">
        <button class="cerrar-modal" onclick="cerrarModal('modalVer')">&times;</button>
        <h2>👤 Detalles del Usuario</h2>
        <div id="detallesUsuario">
            <p><strong>Nombre:</strong> <span id="vNombre"></span></p>
            <p><strong>Correo:</strong> <span id="vCorreo"></span></p>
            <p><strong>Rol:</strong> <span id="vRol"></span></p>
            <p><strong>Unidad:</strong> <span id="vUnidad"></span></p>
            <p><strong>Puesto:</strong> <span id="vPuesto"></span></p>
            <p><strong>Estado:</strong> <span id="vEstado"></span></p>
            <p><strong>ID:</strong> <span id="vId"></span></p>
        </div>
        <div class="modal-acciones">
            <button class="btn btn-outline" onclick="cerrarModal('modalVer')">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal Cambiar Contraseña -->
<div class="modal-overlay" id="modalPassword">
    <div class="modal-contenedor">
        <button class="cerrar-modal" onclick="cerrarModal('modalPassword')">&times;</button>
        <h2>🔐 Cambiar Contraseña</h2>
        <form id="formPassword">
            <input type="hidden" name="id">
            <div class="campo">
                <label>Nueva contraseña *</label>
                <input type="password" name="nueva" required minlength="6">
            </div>
            <div class="campo">
                <label>Confirmar contraseña *</label>
                <input type="password" name="confirmar" required minlength="6">
            </div>
            <div id="errorPassword" class="error" style="display:none;"></div>
            <div class="modal-acciones">
                <button type="button" class="btn btn-outline" onclick="cerrarModal('modalPassword')">Cancelar</button>
                <button type="submit" class="btn btn-vino">Cambiar</button>
            </div>
        </form>
    </div>
</div>

<script>
// ============================================================
// ESTADO GLOBAL
// ============================================================
let estado = {
    busqueda: '',
    rol_id: '',
    unidad_id: '',
    estatus: '',
    orden: 'u.nombre',
    direccion: 'ASC',
    limite: 20,
    offset: 0,
    total: 0
};

// ============================================================
// FUNCIONES DE CARGA DE DATOS (CORREGIDAS)
// ============================================================
async function cargarUsuarios() {
    const params = new URLSearchParams({
        busqueda: estado.busqueda,
        rol_id: estado.rol_id,
        unidad_id: estado.unidad_id,
        estatus: estado.estatus,
        orden: estado.orden,
        direccion: estado.direccion,
        limite: estado.limite,
        offset: estado.offset
    });

    try {
        const response = await fetch('/Dir_bienestar/admin/usuarios_data?' + params);
        if (!response.ok) {
            throw new Error('Error HTTP: ' + response.status);
        }
        const data = await response.json();

        // Verificar si el servidor devolvió un error
        if (data.error) {
            console.error('Error del servidor:', data.error);
            document.getElementById('tablaBody').innerHTML = `<tr><td colspan="7" class="sin-registros">Error: ${data.error}</td></tr>`;
            document.getElementById('infoPaginacion').textContent = 'Error al cargar datos';
            document.getElementById('botonesPaginacion').innerHTML = '';
            return;
        }

        // Asegurar que los datos sean arrays/números válidos
        const usuarios = Array.isArray(data.data) ? data.data : [];
        const total = typeof data.total === 'number' ? data.total : 0;

        renderTabla(usuarios);
        renderPaginacion(total);
        estado.total = total;
    } catch (error) {
        console.error('Error cargando usuarios:', error);
        document.getElementById('tablaBody').innerHTML = '<tr><td colspan="7" class="sin-registros">Error al cargar datos</td></tr>';
        document.getElementById('infoPaginacion').textContent = 'Error de conexión';
        document.getElementById('botonesPaginacion').innerHTML = '';
    }
}

function renderTabla(usuarios) {
    const tbody = document.getElementById('tablaBody');
    if (!usuarios || usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="sin-registros">No se encontraron usuarios</td></tr>';
        return;
    }
    let html = '';
    usuarios.forEach(u => {
        const estadoClase = 'estado-' + u.estatus;
        html += `<tr>
            <td><strong>${escapeHtml(u.nombre)}</strong></td>
            <td>${escapeHtml(u.correo)}</td>
            <td>${escapeHtml(u.puesto || '-')}</td>
            <td>${escapeHtml(u.tipo_rol)}</td>
            <td>${escapeHtml(u.unidad_nombre || '-')}</td>
            <td><span class="estado-badge ${estadoClase}">${u.estatus}</span></td>
            <td>
                <div class="acciones-botones">
                    <button class="btn-icono" title="Ver" onclick="verUsuario(${u.id})">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                    <button class="btn-icono" title="Editar" onclick="editarUsuario(${u.id})">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <button class="btn-icono" title="Cambiar contraseña" onclick="cambiarPassword(${u.id})">
                        <span class="material-symbols-outlined">lock</span>
                    </button>
                    ${u.estatus === 'Bloqueado' ?
                        `<button class="btn-icono verde" title="Desbloquear" onclick="desbloquearUsuario(${u.id})">
                            <span class="material-symbols-outlined">lock_open</span>
                        </button>` :
                        `<button class="btn-icono rojo" title="Bloquear" onclick="bloquearUsuario(${u.id})">
                            <span class="material-symbols-outlined">lock</span>
                        </button>`
                    }
                </div>
            </td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

function renderPaginacion(total) {
    // Asegurar que total sea un número válido
    if (typeof total !== 'number' || isNaN(total) || total < 0) {
        document.getElementById('infoPaginacion').textContent = 'Datos no disponibles';
        document.getElementById('botonesPaginacion').innerHTML = '';
        return;
    }

    const info = document.getElementById('infoPaginacion');
    const contenedor = document.getElementById('botonesPaginacion');

    if (total === 0) {
        info.textContent = 'No hay registros';
        contenedor.innerHTML = '';
        return;
    }

    const totalPaginas = Math.ceil(total / estado.limite);
    const paginaActual = Math.floor(estado.offset / estado.limite) + 1;
    const inicio = estado.offset + 1;
    const fin = Math.min(estado.offset + estado.limite, total);
    info.textContent = `Mostrando ${inicio} - ${fin} de ${total} registros`;

    if (totalPaginas <= 1) {
        contenedor.innerHTML = '';
        return;
    }

    let html = '';
    html += `<button ${paginaActual <= 1 ? 'disabled' : ''} onclick="cambiarPagina(${paginaActual - 1})">◀</button>`;
    for (let i = 1; i <= totalPaginas; i++) {
        if (i === paginaActual) {
            html += `<button class="activo">${i}</button>`;
        } else if (i === 1 || i === totalPaginas || Math.abs(i - paginaActual) <= 2) {
            html += `<button onclick="cambiarPagina(${i})">${i}</button>`;
        } else if (i === 2 || i === totalPaginas - 1) {
            html += `<span style="padding:0 6px;">…</span>`;
        }
    }
    html += `<button ${paginaActual >= totalPaginas ? 'disabled' : ''} onclick="cambiarPagina(${paginaActual + 1})">▶</button>`;
    contenedor.innerHTML = html;
}

function cambiarPagina(pagina) {
    estado.offset = (pagina - 1) * estado.limite;
    cargarUsuarios();
}

// ============================================================
// FILTROS Y ORDENAMIENTO (igual que antes)
// ============================================================
document.getElementById('busqueda').addEventListener('input', function() {
    estado.busqueda = this.value;
    estado.offset = 0;
    cargarUsuarios();
});
document.getElementById('filtroRol').addEventListener('change', function() {
    estado.rol_id = this.value;
    estado.offset = 0;
    cargarUsuarios();
});
document.getElementById('filtroUnidad').addEventListener('change', function() {
    estado.unidad_id = this.value;
    estado.offset = 0;
    cargarUsuarios();
});
document.getElementById('filtroEstatus').addEventListener('change', function() {
    estado.estatus = this.value;
    estado.offset = 0;
    cargarUsuarios();
});

document.querySelectorAll('#tablaUsuarios thead th[data-orden]').forEach(th => {
    th.addEventListener('click', function() {
        const campo = this.dataset.orden;
        if (estado.orden === campo) {
            estado.direccion = estado.direccion === 'ASC' ? 'DESC' : 'ASC';
        } else {
            estado.orden = campo;
            estado.direccion = 'ASC';
        }
        estado.offset = 0;
        cargarUsuarios();
    });
});

// ============================================================
// MODALES (igual que antes)
// ============================================================
function abrirModal(id) {
    document.getElementById(id).classList.add('activo');
}
function cerrarModal(id) {
    document.getElementById(id).classList.remove('activo');
}
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('activo');
        }
    });
});

// ============================================================
// ACCIONES DE USUARIOS (igual que antes)
// ============================================================
async function verUsuario(id) {
    try {
        const res = await fetch(`/Dir_bienestar/admin/obtener_usuario/${id}`);
        const data = await res.json();
        if (data.success) {
            const u = data.usuario;
            document.getElementById('vId').textContent = u.id;
            document.getElementById('vNombre').textContent = u.nombre;
            document.getElementById('vCorreo').textContent = u.correo;
            document.getElementById('vRol').textContent = u.tipo_rol;
            document.getElementById('vUnidad').textContent = u.unidad || '-';
            document.getElementById('vPuesto').textContent = u.puesto || '-';
            document.getElementById('vEstado').textContent = u.estatus;
            abrirModal('modalVer');
        } else {
            alert('Error al obtener usuario');
        }
    } catch (e) {
        alert('Error de conexión');
    }
}

async function editarUsuario(id) {
    try {
        const res = await fetch(`/Dir_bienestar/admin/obtener_usuario/${id}`);
        const data = await res.json();
        if (data.success) {
            const u = data.usuario;
            const form = document.getElementById('formEditarUsuario');
            form.querySelector('[name="id"]').value = u.id;
            form.querySelector('[name="nombre"]').value = u.nombre;
            form.querySelector('[name="correo"]').value = u.correo;
            form.querySelector('[name="puesto"]').value = u.puesto || '';
            form.querySelector('[name="rol_id"]').value = u.rol_id;
            form.querySelector('[name="unidad_administrativa_id"]').value = u.unidad_administrativa_id || '';
            form.querySelector('[name="estatus"]').value = u.estatus;
            document.getElementById('errorEditar').style.display = 'none';
            abrirModal('modalEditar');
        } else {
            alert('Error al obtener usuario');
        }
    } catch (e) {
        alert('Error de conexión');
    }
}

async function cambiarPassword(id) {
    const form = document.getElementById('formPassword');
    form.querySelector('[name="id"]').value = id;
    form.reset();
    document.getElementById('errorPassword').style.display = 'none';
    abrirModal('modalPassword');
}

async function bloquearUsuario(id) {
    if (!confirm('¿Bloquear este usuario? No podrá iniciar sesión.')) return;
    try {
        const res = await fetch(`/Dir_bienestar/admin/bloquear_usuario/${id}`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            cargarUsuarios();
        } else {
            alert('Error al bloquear usuario');
        }
    } catch (e) {
        alert('Error de conexión');
    }
}

async function desbloquearUsuario(id) {
    if (!confirm('¿Desbloquear este usuario?')) return;
    try {
        const res = await fetch(`/Dir_bienestar/admin/desbloquear_usuario/${id}`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            cargarUsuarios();
        } else {
            alert('Error al desbloquear usuario');
        }
    } catch (e) {
        alert('Error de conexión');
    }
}

// ============================================================
// ENVÍO DE FORMULARIOS (AJAX) - igual que antes
// ============================================================
document.getElementById('formNuevoUsuario').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    const errorDiv = document.getElementById('errorNuevo');
    errorDiv.style.display = 'none';
    try {
        const res = await fetch('/Dir_bienestar/admin/crear_usuario', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            cerrarModal('modalNuevo');
            cargarUsuarios();
        } else {
            errorDiv.textContent = result.error || 'Error al crear usuario';
            errorDiv.style.display = 'block';
        }
    } catch (e) {
        errorDiv.textContent = 'Error de conexión';
        errorDiv.style.display = 'block';
    }
});

document.getElementById('formEditarUsuario').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    const id = data.id;
    delete data.id;
    const errorDiv = document.getElementById('errorEditar');
    errorDiv.style.display = 'none';
    try {
        const res = await fetch(`/Dir_bienestar/admin/actualizar_usuario/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            cerrarModal('modalEditar');
            cargarUsuarios();
        } else {
            errorDiv.textContent = result.error || 'Error al actualizar';
            errorDiv.style.display = 'block';
        }
    } catch (e) {
        errorDiv.textContent = 'Error de conexión';
        errorDiv.style.display = 'block';
    }
});

document.getElementById('formPassword').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    const id = data.id;
    delete data.id;
    const errorDiv = document.getElementById('errorPassword');
    errorDiv.style.display = 'none';
    try {
        const res = await fetch(`/Dir_bienestar/admin/cambiar_contrasena_usuario/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            cerrarModal('modalPassword');
            alert('Contraseña cambiada exitosamente');
        } else {
            errorDiv.textContent = result.error || 'Error al cambiar contraseña';
            errorDiv.style.display = 'block';
        }
    } catch (e) {
        errorDiv.textContent = 'Error de conexión';
        errorDiv.style.display = 'block';
    }
});

// ============================================================
// BOTÓN NUEVO USUARIO
// ============================================================
document.getElementById('btnNuevoUsuario').addEventListener('click', function() {
    document.getElementById('formNuevoUsuario').reset();
    document.getElementById('errorNuevo').style.display = 'none';
    abrirModal('modalNuevo');
});

// ============================================================
// UTILITY
// ============================================================
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// ============================================================
// INICIALIZAR
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    cargarUsuarios();
});
</script>
</body>
</html>