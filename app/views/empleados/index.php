<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-success alert-dismissible" id="mensajeExito">
        <span class="mensaje-texto"><?= htmlspecialchars($_SESSION['mensaje']) ?></span>
        <button type="button" class="btn-cerrar" id="cerrarMensaje">&times;</button>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<div class="empleados-container">
    <div class="empleados-header">
        <h2>Empleados de mi unidad</h2>
        <a href="/Dir_bienestar/empleados/agregar" class="btn-agregar">
            <span class="material-symbols-outlined">person_add</span> Agregar empleado
        </a>
    </div>

    <table class="table table-hover empleados-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Puesto</th>
                <th>Rol</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($empleados)): ?>
                <tr>
                    <td colspan="6" class="text-center">No hay empleados registrados en esta unidad.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($empleados as $emp): ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['nombre']) ?></td>
                        <td><?= htmlspecialchars($emp['correo']) ?></td>
                        <td><?= htmlspecialchars($emp['puesto'] ?? '') ?></td>
                        <td><?= htmlspecialchars($emp['tipo_rol']) ?></td>
                        <td>
                            <span class="badge-estatus <?= $emp['estatus'] === 'Activo' ? 'activo' : 'inactivo' ?>">
                                <?= $emp['estatus'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="/Dir_bienestar/empleados/editar/<?= $emp['id'] ?>" class="btn-accion editar">
                                <span class="material-symbols-outlined">edit</span> Editar
                            </a>
                            <a href="/Dir_bienestar/empleados/toggle/<?= $emp['id'] ?>" 
                               class="btn-accion <?= $emp['estatus'] === 'Activo' ? 'bloquear' : 'desbloquear' ?>"
                               onclick="return confirm('¿Cambiar estatus de este empleado?')">
                                <span class="material-symbols-outlined">
                                    <?= $emp['estatus'] === 'Activo' ? 'block' : 'check_circle' ?>
                                </span>
                                <?= $emp['estatus'] === 'Activo' ? 'Bloquear' : 'Desbloquear' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
/* ===== CONTENEDOR PRINCIPAL (estilo vidrio) ===== */
.empleados-container {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 28px;
    padding: 30px 28px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    margin: 20px auto;
    max-width: 1200px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* ===== HEADER ===== */
.empleados-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 28px;
    border-bottom: 2px solid rgba(128, 0, 0, 0.15);
    padding-bottom: 16px;
}

.empleados-header h2 {
    color: #800000;
    font-weight: 700;
    font-size: 1.6rem;
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

/* ===== MENSAJE DE ÉXITO (auto‑ocultable) ===== */
.alert-success {
    background: rgba(46, 125, 50, 0.12);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(46, 125, 50, 0.25);
    border-radius: 16px;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    color: #1b5e20;
    font-weight: 500;
    animation: slideDown 0.4s ease;
}

.alert-success .mensaje-texto {
    flex: 1;
}

.alert-success .btn-cerrar {
    background: none;
    border: none;
    font-size: 1.8rem;
    line-height: 1;
    padding: 0 8px;
    cursor: pointer;
    color: #1b5e20;
    opacity: 0.6;
    transition: 0.2s;
}

.alert-success .btn-cerrar:hover {
    opacity: 1;
    transform: scale(1.1);
}

/* Animaciones de entrada y salida */
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-16px); }
    to { opacity: 1; transform: translateY(0); }
}
.alert-success.ocultar {
    animation: slideUp 0.4s ease forwards;
}
@keyframes slideUp {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-16px); }
}

/* ===== TABLA ===== */
.empleados-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 6px;
    background: transparent;
}

.empleados-table thead th {
    color: #800000;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 16px;
    border-bottom: 2px solid rgba(128, 0, 0, 0.2);
    background: rgba(128, 0, 0, 0.04);
}

.empleados-table tbody tr {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(4px);
    transition: all 0.2s ease;
    border-radius: 12px;
}

.empleados-table tbody tr:hover {
    background: rgba(128, 0, 0, 0.06);
    transform: scale(1.003);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}

.empleados-table td {
    padding: 14px 16px;
    vertical-align: middle;
    border: none;
    font-size: 0.95rem;
}

.empleados-table td:first-child {
    border-radius: 12px 0 0 12px;
}
.empleados-table td:last-child {
    border-radius: 0 12px 12px 0;
}

/* ===== BADGE ESTATUS ===== */
.badge-estatus {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    background: rgba(128, 128, 128, 0.15);
    color: #555;
}

.badge-estatus.activo {
    background: rgba(46, 125, 50, 0.15);
    color: #2e7d32;
}

.badge-estatus.inactivo {
    background: rgba(158, 158, 158, 0.2);
    color: #616161;
}

/* ===== BOTONES DE ACCIÓN ===== */
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
    margin-right: 4px;
    border: 1px solid transparent;
}

.btn-accion .material-symbols-outlined {
    font-size: 1.1rem;
}

.btn-accion.editar {
    background: rgba(128, 0, 0, 0.08);
    color: #800000;
    border-color: rgba(128, 0, 0, 0.15);
}

.btn-accion.editar:hover {
    background: rgba(128, 0, 0, 0.15);
    color: #660000;
    text-decoration: none;
}

.btn-accion.bloquear {
    background: rgba(211, 47, 47, 0.08);
    color: #b71c1c;
    border-color: rgba(211, 47, 47, 0.15);
}

.btn-accion.bloquear:hover {
    background: rgba(211, 47, 47, 0.15);
    color: #880e0e;
    text-decoration: none;
}

.btn-accion.desbloquear {
    background: rgba(46, 125, 50, 0.08);
    color: #2e7d32;
    border-color: rgba(46, 125, 50, 0.15);
}

.btn-accion.desbloquear:hover {
    background: rgba(46, 125, 50, 0.15);
    color: #1b5e20;
    text-decoration: none;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .empleados-container {
        padding: 18px 14px;
        border-radius: 20px;
    }
    .empleados-header {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    .empleados-header h2 {
        font-size: 1.3rem;
        text-align: center;
    }
    .btn-agregar {
        justify-content: center;
        padding: 12px 20px;
    }
    .empleados-table thead {
        display: none;
    }
    .empleados-table tbody tr {
        display: block;
        margin-bottom: 16px;
        background: rgba(255, 255, 255, 0.8);
        padding: 16px;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    .empleados-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        font-size: 0.9rem;
    }
    .empleados-table td:last-child {
        border-bottom: none;
        justify-content: flex-start;
        flex-wrap: wrap;
        gap: 6px;
    }
    .empleados-table td:before {
        content: attr(data-label);
        font-weight: 700;
        color: #800000;
        width: 40%;
        flex-shrink: 0;
    }
    .empleados-table td:first-child,
    .empleados-table td:last-child {
        border-radius: 0;
    }
}

@media (max-width: 480px) {
    .empleados-container {
        padding: 14px 10px;
    }
    .btn-agregar {
        font-size: 0.85rem;
        padding: 10px 16px;
    }
    .btn-accion {
        font-size: 0.7rem;
        padding: 4px 10px;
    }
}
</style>

<script>
// ===== AUTO‑CERRAR MENSAJE DE ÉXITO =====
document.addEventListener('DOMContentLoaded', function() {
    const mensaje = document.getElementById('mensajeExito');
    if (mensaje) {
        // Cerrar con el botón (X)
        const cerrarBtn = document.getElementById('cerrarMensaje');
        if (cerrarBtn) {
            cerrarBtn.addEventListener('click', function() {
                mensaje.classList.add('ocultar');
                setTimeout(() => mensaje.style.display = 'none', 400);
            });
        }

        // Auto‑cerrar después de 4 segundos
        setTimeout(() => {
            mensaje.classList.add('ocultar');
            setTimeout(() => mensaje.style.display = 'none', 400);
        }, 4000);
    }

    // ===== DATOS‑LABEL PARA MÓVIL =====
    const table = document.querySelector('.empleados-table');
    if (table) {
        const headers = table.querySelectorAll('thead th');
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
                if (headers[index]) {
                    cell.setAttribute('data-label', headers[index].textContent.trim());
                }
            });
        });
    }
});
</script>