<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Actividades | Toluca</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        /* Estilos extra para las filas dinámicas */
        .days-container {
            grid-column: span 2;
            background: #f9f4ed;
            border-radius: 24px;
            padding: 16px 18px;
            border: 1px solid #e2d4c4;
        }
        .days-container label {
            font-weight: 700;
            font-size: 0.8rem;
            color: #a90303;
            display: block;
            margin-bottom: 10px;
        }
        .day-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            background: white;
            padding: 12px 14px;
            border-radius: 20px;
            margin-bottom: 10px;
            border: 1px solid #e2d4c4;
        }
        .day-row .field-group {
            flex: 1;
            min-width: 120px;
            margin: 0;
        }
        .day-row .field-group label {
            font-size: 0.6rem;
            color: #6d4c2a;
            margin-bottom: 2px;
        }
        .day-row .field-group input {
            padding: 6px 10px;
            border-radius: 20px;
            border: 1px solid #d0c0b0;
            font-size: 0.75rem;
            width: 100%;
        }
        .remove-day-btn {
            background: #d32f2f;
            color: white;
            border: none;
            border-radius: 40px;
            padding: 6px 14px;
            font-weight: bold;
            cursor: pointer;
            font-size: 0.7rem;
            align-self: flex-end;
            margin-bottom: 2px;
        }
        .remove-day-btn:hover {
            background: #b71c1c;
        }
        .add-day-btn {
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: bold;
            cursor: pointer;
            font-size: 0.8rem;
            margin-top: 8px;
        }
        .add-day-btn:hover {
            background: #1b5e20;
        }
        @media (max-width: 850px) {
            .days-container {
                grid-column: span 1;
            }
            .day-row {
                flex-direction: column;
                align-items: stretch;
            }
            .day-row .field-group {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<div class="dashboard">
    <div class="logos-col">
        <img src="/img/tol.png" alt="Toluca">
    </div>
    <div class="form-col">
        <div class="form-card">
            <div class="card-header">
                <h1>Registro de Actividades</h1>
                <p>Captura de actividades operativas - Verifica antes de guardar</p>
            </div>

            <form id="activityForm">
                <div class="form-grid">
                    <!-- Responsable -->
                    <div class="field-group">
                        <label>Responsable</label>
                        <input type="text" id="responsable" readonly value="<?= htmlspecialchars($responsable) ?>">
                    </div>

                    <!-- Unidad Administrativa -->
                    <div class="field-group">
                        <label>Unidad Administrativa *</label>
                        <select id="unidad_administrativa_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($unidades as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Actividad -->
                    <div class="field-group">
                        <label>Actividad *</label>
                        <select id="actividad_programada_id" required disabled>
                            <option value="">Primero seleccione unidad</option>
                        </select>
                    </div>

                    <!-- Unidad de medida -->
                    <div class="field-group">
                        <label>Unidad de medida</label>
                        <input type="text" id="unidad_medida_nombre" readonly placeholder="Seleccione actividad">
                        <input type="hidden" id="unidad_medida_id">
                    </div>

                    <!-- Lugar con "Otro" -->
                    <div class="field-group">
                        <label>Lugar *</label>
                        <select id="lugar_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($lugares as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nombre']) ?></option>
                            <?php endforeach; ?>
                            <option value="0">Otro (especificar)</option>
                        </select>
                        <div id="otro_lugar_container" class="hidden">
                            <input type="text" id="otro_lugar" placeholder="Escriba el nuevo lugar">
                        </div>
                    </div>

                    <!-- Delegación -->
                    <div class="field-group">
                        <label>Delegación *</label>
                        <select id="delegacion_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($delegaciones as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Subdelegación (opcional) -->
                    <div class="field-group">
                        <label>Subdelegación (opcional)</label>
                        <select id="subdelegacion_id">
                            <option value="">No aplica</option>
                        </select>
                    </div>

                    <!-- Código Postal -->
                    <div class="field-group" id="cp_group" style="display:none;">
                        <label>Código Postal *</label>
                        <select id="cp_select" required>
                            <option value="">Seleccione CP</option>
                        </select>
                    </div>

                    <!-- Domicilio -->
                    <div class="field-group">
                        <label>Calle *</label>
                        <input type="text" id="calle" required>
                    </div>
                    <div class="field-group">
                        <label>Número exterior *</label>
                        <input type="text" id="numero_exterior" required>
                    </div>
                    <div class="field-group">
                        <label>Número interior</label>
                        <input type="text" id="numero_interior">
                    </div>

                    <!-- Beneficiarios -->
                    <div class="field-group">
                        <label>Beneficiarios / Asistentes *</label>
                        <input type="number" id="beneficiarios_asistentes" min="1" required>
                    </div>

                    <!-- Tipo entregable -->
                    <div class="field-group">
                        <label>Tipo de entregable *</label>
                        <select id="tipo_entregable_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($tiposEntregable as $te): ?>
                                <option value="<?= $te['id'] ?>"><?= htmlspecialchars($te['nombre_entregable']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Descripción -->
                    <div class="field-group full-width">
                        <label>Descripción *</label>
                        <textarea id="descripcion" rows="3" required></textarea>
                    </div>

                    <!-- Días dinámicos -->
                    <div class="days-container full-width" id="daysContainer">
                        <label>📅 Días y horarios *</label>
                        <div id="daysList">
                            <!-- Las filas se agregarán aquí dinámicamente -->
                        </div>
                        <button type="button" class="add-day-btn" id="addDayBtn">+ Agregar día</button>
                    </div>
                </div>

                <div class="actions-bar">
                    <button type="button" class="btn btn-secondary" id="limpiarBtn">Limpiar</button>
                    <button type="button" class="btn btn-primary" id="guardarBtn">Guardar</button>
                </div>
                <div class="warning-note">
                    ⚠️ Verifique los datos. Al guardar se abrirá un modal de confirmación y se registrará en la base de datos.
                </div>
            </form>
        </div>
        <footer>Sistema Oficial de Registro <br>  Hecho por: <br> Carmona Aviles Ana Karen <br> Onofre Garcia Halem <br> Oscar Arturo Díaz Duran</footer>
    </div>
</div>

<!-- Modal de confirmación -->
<div id="confirmModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Confirmar registro</h3>
            <button class="close-modal" id="closeModalBtn">&times;</button>
        </div>
        <div class="modal-body" id="modalDataSummary"></div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelModalBtn">Cancelar</button>
            <button class="btn btn-primary" id="confirmSaveBtn">Confirmar</button>
        </div>
    </div>
</div>

<script>
    const BASE_URL = '/Dir_bienestar';
    const modal = document.getElementById('confirmModal');
    let currentFormData = null;

    // Elementos principales
    const unidadSelect = document.getElementById('unidad_administrativa_id');
    const actividadSelect = document.getElementById('actividad_programada_id');
    const unidadMedidaNombre = document.getElementById('unidad_medida_nombre');
    const unidadMedidaIdHidden = document.getElementById('unidad_medida_id');
    const delegacionSelect = document.getElementById('delegacion_id');
    const subdelegacionSelect = document.getElementById('subdelegacion_id');
    const cpGroup = document.getElementById('cp_group');
    const cpSelect = document.getElementById('cp_select');
    const lugarSelect = document.getElementById('lugar_id');
    const otroLugarContainer = document.getElementById('otro_lugar_container');
    const otroLugarInput = document.getElementById('otro_lugar');
    const calleInput = document.getElementById('calle');
    const numeroExteriorInput = document.getElementById('numero_exterior');
    const descripcionInput = document.getElementById('descripcion');
    const beneficiariosInput = document.getElementById('beneficiarios_asistentes');
    const tipoEntregableSelect = document.getElementById('tipo_entregable_id');
    const daysList = document.getElementById('daysList');
    const addDayBtn = document.getElementById('addDayBtn');

    let allCps = [];
    let dayCount = 0;

    // --- Validar formato mixto ---
    function validarFormatoMixto(texto) {
        if (!texto || texto.length === 0) return true;
        return /[A-Z]/.test(texto) && /[a-z]/.test(texto);
    }

    // --- Mostrar/ocultar "Otro lugar" ---
    lugarSelect.addEventListener('change', function() {
        if (this.value === '0') {
            otroLugarContainer.classList.remove('hidden');
            otroLugarInput.required = true;
        } else {
            otroLugarContainer.classList.add('hidden');
            otroLugarInput.required = false;
            otroLugarInput.value = '';
        }
    });

    // --- Cargar actividades al cambiar unidad ---
    unidadSelect.addEventListener('change', async function() {
        const unidadId = this.value;
        if (!unidadId) {
            actividadSelect.innerHTML = '<option value="">Primero seleccione unidad</option>';
            actividadSelect.disabled = true;
            unidadMedidaNombre.value = '';
            unidadMedidaIdHidden.value = '';
            return;
        }
        actividadSelect.disabled = true;
        actividadSelect.innerHTML = '<option value="">Cargando...</option>';
        try {
            const response = await fetch(`${BASE_URL}/dashboard/actividadesPorUnidad/${unidadId}`);
            if (!response.ok) throw new Error('Error en la petición');
            const actividades = await response.json();
            if (actividades.length === 0) {
                actividadSelect.innerHTML = '<option value="">No hay actividades para esta unidad</option>';
            } else {
                let options = '<option value="">Seleccione actividad</option>';
                actividades.forEach(act => {
                    options += `<option value="${act.id}" data-unidad-medida-id="${act.unidad_medida_id}" data-unidad-medida-nombre="${escapeHtml(act.unidad_medida)}">${escapeHtml(act.descripcion)}</option>`;
                });
                actividadSelect.innerHTML = options;
                actividadSelect.disabled = false;
            }
        } catch (error) {
            console.error(error);
            actividadSelect.innerHTML = '<option value="">Error al cargar</option>';
            mostrarToast('Error al cargar actividades', true);
        }
    });

    actividadSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const unidadMedidaId = selectedOption?.dataset.unidadMedidaId;
        const unidadMedidaNombreText = selectedOption?.dataset.unidadMedidaNombre;
        if (unidadMedidaId) {
            unidadMedidaIdHidden.value = unidadMedidaId;
            unidadMedidaNombre.value = unidadMedidaNombreText;
        } else {
            unidadMedidaIdHidden.value = '';
            unidadMedidaNombre.value = '';
        }
    });

    // --- Delegación: cargar subdelegaciones y CPs (igual que antes) ---
    delegacionSelect.addEventListener('change', async function() {
        const delegacionId = this.value;
        subdelegacionSelect.innerHTML = '<option value="">Cargando...</option>';
        cpSelect.innerHTML = '<option value="">Seleccione CP</option>';
        cpGroup.style.display = 'none';
        cpSelect.required = false;
        allCps = [];

        if (!delegacionId) {
            subdelegacionSelect.innerHTML = '<option value="">No aplica</option>';
            return;
        }

        try {
            const subResponse = await fetch(`${BASE_URL}/dashboard/subdelegaciones/${delegacionId}`);
            const subdelegaciones = await subResponse.json();
            let subOptions = '<option value="">No aplica</option>';
            if (subdelegaciones.length > 0) {
                subdelegaciones.forEach(sub => {
                    subOptions += `<option value="${sub.id}">${escapeHtml(sub.nombre)}</option>`;
                });
            }
            subdelegacionSelect.innerHTML = subOptions;
        } catch (error) {
            console.error('Error cargando subdelegaciones:', error);
            subdelegacionSelect.innerHTML = '<option value="">Error</option>';
        }

        try {
            const cpResponse = await fetch(`${BASE_URL}/dashboard/codigosPostalesPorDelegacion/${delegacionId}`);
            const cps = await cpResponse.json();
            allCps = cps;
            if (cps.length === 0) {
                cpSelect.innerHTML = '<option value="">No hay CPs para esta delegación</option>';
                cpSelect.required = false;
                cpGroup.style.display = 'block';
                return;
            }
            let options = '<option value="">Seleccione CP</option>';
            cps.forEach(cp => {
                let label = cp.cp;
                if (cp.subdelegacion_nombre) {
                    label += ` (${cp.subdelegacion_nombre})`;
                }
                options += `<option value="${cp.id}" data-subdelegacion-id="${cp.subdelegacion_id || ''}" data-subdelegacion-nombre="${escapeHtml(cp.subdelegacion_nombre || '')}">${escapeHtml(label)}</option>`;
            });
            cpSelect.innerHTML = options;
            cpSelect.required = true;
            cpGroup.style.display = 'block';

            const directos = cps.filter(c => !c.subdelegacion_id);
            if (directos.length === 1 && cps.length === 1) {
                cpSelect.value = directos[0].id;
                cpSelect.dispatchEvent(new Event('change'));
            }
        } catch (error) {
            console.error('Error cargando CPs:', error);
            cpSelect.innerHTML = '<option value="">Error al cargar</option>';
            cpSelect.required = false;
            cpGroup.style.display = 'block';
        }
    });

    subdelegacionSelect.addEventListener('change', function() {
        const subdelegacionId = this.value;
        if (!subdelegacionId) {
            if (allCps.length === 0) {
                cpSelect.innerHTML = '<option value="">No hay CPs</option>';
                cpSelect.required = false;
                cpGroup.style.display = 'block';
                return;
            }
            let options = '<option value="">Seleccione CP</option>';
            allCps.forEach(cp => {
                let label = cp.cp;
                if (cp.subdelegacion_nombre) {
                    label += ` (${cp.subdelegacion_nombre})`;
                }
                options += `<option value="${cp.id}" data-subdelegacion-id="${cp.subdelegacion_id || ''}" data-subdelegacion-nombre="${escapeHtml(cp.subdelegacion_nombre || '')}">${escapeHtml(label)}</option>`;
            });
            cpSelect.innerHTML = options;
            cpSelect.required = true;
            cpGroup.style.display = 'block';
            return;
        }

        const filtered = allCps.filter(cp => cp.subdelegacion_id == subdelegacionId);
        if (filtered.length === 0) {
            cpSelect.innerHTML = '<option value="">No hay CPs para esta subdelegación</option>';
            cpSelect.required = false;
            cpGroup.style.display = 'block';
            return;
        }
        let options = '<option value="">Seleccione CP</option>';
        filtered.forEach(cp => {
            let label = cp.cp;
            if (cp.subdelegacion_nombre) {
                label += ` (${cp.subdelegacion_nombre})`;
            }
            options += `<option value="${cp.id}" data-subdelegacion-id="${cp.subdelegacion_id || ''}" data-subdelegacion-nombre="${escapeHtml(cp.subdelegacion_nombre || '')}">${escapeHtml(label)}</option>`;
        });
        cpSelect.innerHTML = options;
        cpSelect.required = filtered.length > 0;
        cpGroup.style.display = 'block';

        if (filtered.length === 1) {
            cpSelect.value = filtered[0].id;
            cpSelect.dispatchEvent(new Event('change'));
        }
    });

    cpSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const subId = selectedOption?.dataset.subdelegacionId;
        if (subId) {
            subdelegacionSelect.value = subId;
        } else {
            subdelegacionSelect.value = '';
        }
    });

    // --- Funciones para manejar días dinámicos ---
    function createDayRow(fecha = '', horaInicio = '', horaFin = '') {
        const rowDiv = document.createElement('div');
        rowDiv.className = 'day-row';
        rowDiv.dataset.index = dayCount++;

        rowDiv.innerHTML = `
            <div class="field-group">
                <label>Fecha</label>
                <input type="date" class="day-fecha" value="${fecha}" required>
            </div>
            <div class="field-group">
                <label>Hora inicio</label>
                <input type="time" class="day-hora-inicio" value="${horaInicio}" required>
            </div>
            <div class="field-group">
                <label>Hora fin</label>
                <input type="time" class="day-hora-fin" value="${horaFin}" required>
            </div>
            <button type="button" class="remove-day-btn" title="Eliminar día">✕ Eliminar</button>
        `;

        // Evento para eliminar
        rowDiv.querySelector('.remove-day-btn').addEventListener('click', function() {
            if (daysList.children.length > 1) {
                rowDiv.remove();
            } else {
                mostrarToast('Debe haber al menos un día', true);
            }
        });

        return rowDiv;
    }

    function addDefaultDay() {
        // Si no hay días, agregar uno con la fecha actual y horas por defecto (ej. 09:00 - 18:00)
        const hoy = new Date().toISOString().split('T')[0];
        const row = createDayRow(hoy, '09:00', '18:00');
        daysList.appendChild(row);
    }

    // Inicializar con un día
    addDefaultDay();

    // Botón agregar día
    addDayBtn.addEventListener('click', function() {
        const ultimaFecha = daysList.lastElementChild?.querySelector('.day-fecha')?.value || '';
        const ultimaHoraInicio = daysList.lastElementChild?.querySelector('.day-hora-inicio')?.value || '09:00';
        const ultimaHoraFin = daysList.lastElementChild?.querySelector('.day-hora-fin')?.value || '18:00';
        const row = createDayRow(ultimaFecha, ultimaHoraInicio, ultimaHoraFin);
        daysList.appendChild(row);
    });

    // --- Obtener datos del formulario ---
    function getFormData() {
        const lugar_id = lugarSelect.value;
        const otro_lugar = (lugar_id === '0') ? otroLugarInput.value.trim() : '';
        const cp = cpSelect.value ? cpSelect.value : null;
        const subdelegacion_id = subdelegacionSelect.value || null;

        const calle = calleInput.value.trim();
        const numeroExterior = numeroExteriorInput.value.trim();
        const numeroInterior = document.getElementById('numero_interior').value ? document.getElementById('numero_interior').value.trim() : null;
        const descripcion = descripcionInput.value.trim();
        const beneficiarios = beneficiariosInput.value;
        const tipoEntregable = tipoEntregableSelect.value;

        // Recolectar días
        const days = [];
        const rows = daysList.querySelectorAll('.day-row');
        let valid = true;
        rows.forEach(row => {
            const fecha = row.querySelector('.day-fecha').value;
            const horaInicio = row.querySelector('.day-hora-inicio').value;
            const horaFin = row.querySelector('.day-hora-fin').value;
            if (fecha && horaInicio && horaFin) {
                days.push({ fecha, hora_inicio: horaInicio, hora_fin: horaFin });
            } else {
                valid = false;
            }
        });

        if (!valid) {
            mostrarToast('Complete todos los campos de fecha y hora para cada día', true);
            return null;
        }

        return {
            responsable: document.getElementById('responsable').value,
            unidad_administrativa_id: unidadSelect.value,
            actividad_programada_id: actividadSelect.value,
            unidad_medida_id: unidadMedidaIdHidden.value,
            lugar_id: lugar_id,
            otro_lugar: otro_lugar,
            delegacion_id: delegacionSelect.value,
            subdelegacion_id: subdelegacion_id,
            cp: cp,
            calle: calle,
            numero_exterior: numeroExterior,
            numero_interior: numeroInterior,
            beneficiarios_asistentes: beneficiarios,
            tipo_entregable_id: tipoEntregable,
            descripcion: descripcion,
            dias: days
        };
    }

    // --- Validación ---
    function validate(data) {
        if (!data) return false;
        if (!data.unidad_administrativa_id) { mostrarToast('Seleccione unidad administrativa', true); return false; }
        if (!data.actividad_programada_id) { mostrarToast('Seleccione actividad', true); return false; }
        if (!data.lugar_id) { mostrarToast('Seleccione lugar', true); return false; }
        if (data.lugar_id === '0' && !data.otro_lugar) { mostrarToast('Especifique el nuevo lugar', true); return false; }
        if (!data.delegacion_id) { mostrarToast('Seleccione delegación', true); return false; }
        if (!data.cp) { mostrarToast('Seleccione un código postal', true); return false; }
        if (!data.calle) { mostrarToast('Ingrese calle', true); return false; }
        if (!validarFormatoMixto(data.calle)) { mostrarToast('La calle debe tener al menos una mayúscula y una minúscula', true); return false; }
        if (!data.numero_exterior) { mostrarToast('Ingrese número exterior', true); return false; }
        if (!validarFormatoMixto(data.numero_exterior)) { mostrarToast('El número exterior debe tener al menos una mayúscula y una minúscula', true); return false; }
        if (!data.beneficiarios_asistentes || parseInt(data.beneficiarios_asistentes) < 1) {
            mostrarToast('Los beneficiarios deben ser al menos 1', true);
            return false;
        }
        if (!data.tipo_entregable_id) { mostrarToast('Seleccione tipo de entregable', true); return false; }
        if (!data.descripcion) { mostrarToast('Ingrese descripción', true); return false; }
        if (!validarFormatoMixto(data.descripcion)) { mostrarToast('La descripción debe tener al menos una mayúscula y una minúscula', true); return false; }
        if (!data.dias || data.dias.length === 0) { mostrarToast('Agregue al menos un día con su horario', true); return false; }
        // Validar que cada día tenga hora fin > hora inicio
        for (let d of data.dias) {
            if (d.hora_fin <= d.hora_inicio) {
                mostrarToast(`En la fecha ${d.fecha}, la hora fin debe ser mayor que la hora inicio`, true);
                return false;
            }
        }
        return true;
    }

    // --- Mostrar modal ---
    function showModal(data) {
        const container = document.getElementById('modalDataSummary');
        let lugarTexto = lugarSelect.options[lugarSelect.selectedIndex]?.text;
        if (data.lugar_id === '0') lugarTexto = `Otro: ${data.otro_lugar}`;
        let cpTexto = cpSelect.options[cpSelect.selectedIndex]?.text || 'No seleccionado';
        let subTexto = subdelegacionSelect.options[subdelegacionSelect.selectedIndex]?.text || 'No aplica';
        let delegTexto = delegacionSelect.options[delegacionSelect.selectedIndex]?.text || '';
        let unidadTexto = unidadSelect.options[unidadSelect.selectedIndex]?.text || '';
        let actividadTexto = actividadSelect.options[actividadSelect.selectedIndex]?.text || '';

        // Construir resumen de días
        let diasHtml = '';
        data.dias.forEach((d, i) => {
            diasHtml += `<div style="font-size:0.75rem; padding:4px 0; border-bottom:1px dashed #e7ddcd;">
                <strong>Día ${i+1}:</strong> ${d.fecha} · ${d.hora_inicio} - ${d.hora_fin}
            </div>`;
        });

        container.innerHTML = `
            <div class="summary-row"><span class="summary-label">Responsable:</span><span>${escapeHtml(data.responsable)}</span></div>
            <div class="summary-row"><span class="summary-label">Unidad:</span><span>${escapeHtml(unidadTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Actividad:</span><span>${escapeHtml(actividadTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Lugar:</span><span>${escapeHtml(lugarTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Delegación:</span><span>${escapeHtml(delegTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Subdelegación:</span><span>${escapeHtml(subTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Código Postal:</span><span>${escapeHtml(cpTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Domicilio:</span><span>${escapeHtml(data.calle)} ${escapeHtml(data.numero_exterior)}, Int. ${escapeHtml(data.numero_interior || '')}</span></div>
            <div class="summary-row"><span class="summary-label">Beneficiarios:</span><span>${data.beneficiarios_asistentes}</span></div>
            <div class="summary-row"><span class="summary-label">Entregable:</span><span>${escapeHtml(tipoEntregableSelect.options[tipoEntregableSelect.selectedIndex]?.text || '')}</span></div>
            <div class="summary-row"><span class="summary-label">Descripción:</span><span>${escapeHtml(data.descripcion)}</span></div>
            <div class="summary-row" style="grid-column: span 2; background:#f9f2ea; border-radius:12px; padding:10px;">
                <span class="summary-label">📅 Días y horarios:</span>
                <div style="margin-top:6px;">${diasHtml}</div>
                <div style="margin-top:6px; font-size:0.7rem; color:#800000;">⚠️ Se generarán ${data.dias.length} registros (uno por cada día)</div>
            </div>
        `;
        modal.classList.add('active');
    }

    // --- Enviar registro ---
    async function enviarRegistro(data) {
        try {
            const response = await fetch(`${BASE_URL}/actividad/guardar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                const msg = result.registros > 1 ?
                    `${result.registros} registros guardados exitosamente` :
                    'Registro guardado exitosamente';
                mostrarToast(msg);
                limpiarFormulario();
                closeModal();
            } else {
                mostrarToast('Error: ' + (result.error || 'No se pudo guardar'), true);
            }
        } catch (error) {
            console.error(error);
            mostrarToast('Error de conexión con el servidor', true);
        }
    }

    function closeModal() { modal.classList.remove('active'); }
    function mostrarToast(msg, error = false) {
        let toast = document.querySelector('.toast-msg');
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'toast-msg';
            document.body.appendChild(toast);
        }
        toast.style.background = error ? '#a90303e6' : '#2a6b47e6';
        toast.innerText = msg;
        toast.style.opacity = '1';
        setTimeout(() => toast.style.opacity = '0', 3000);
    }

    function limpiarFormulario() {
        document.getElementById('activityForm').reset();
        unidadSelect.value = '';
        actividadSelect.innerHTML = '<option value="">Primero seleccione unidad</option>';
        actividadSelect.disabled = true;
        subdelegacionSelect.innerHTML = '<option value="">No aplica</option>';
        cpSelect.innerHTML = '<option value="">Seleccione CP</option>';
        cpGroup.style.display = 'none';
        cpSelect.required = false;
        otroLugarContainer.classList.add('hidden');
        otroLugarInput.value = '';
        unidadMedidaNombre.value = '';
        unidadMedidaIdHidden.value = '';
        allCps = [];
        // Limpiar días: dejar solo uno
        daysList.innerHTML = '';
        dayCount = 0;
        addDefaultDay();
    }

    // --- Eventos de botones ---
    document.getElementById('guardarBtn').addEventListener('click', () => {
        const data = getFormData();
        if (!validate(data)) return;
        currentFormData = data;
        showModal(data);
    });
    document.getElementById('confirmSaveBtn').addEventListener('click', () => { if (currentFormData) enviarRegistro(currentFormData); });
    document.getElementById('cancelModalBtn').addEventListener('click', closeModal);
    document.getElementById('closeModalBtn').addEventListener('click', closeModal);
    document.getElementById('limpiarBtn').addEventListener('click', limpiarFormulario);
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
</script>
</body>
</html>