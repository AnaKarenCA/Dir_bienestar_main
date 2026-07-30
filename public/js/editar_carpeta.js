/**
 * editar_carpeta.js
 * JavaScript para el formulario de edición de carpeta de evento
 * DG Bienestar - Sistema de Actividades
 */

// ================================================================
// VARIABLES GLOBALES (se inyectan desde PHP)
// ================================================================
let currentSpots = window.currentSpots || 5;
let savedNames = {};
let savedCargos = {};
let currentType = window.tipoPresidiumSeleccionado || 'lineal';
const usuarios = window.usuarios || [];
const insumosInternos = window.insumosInternos || [];
const insumosExternos = window.insumosExternos || [];
const registroHoraInicio = window.registroHoraInicio || '09:00';

// ================================================================
// SINCERONIZACIÓN DE FECHAS
// ================================================================
function syncDates(val) {
    const resumenDate = document.getElementById('resumen-date');
    const genDate = document.getElementById('gen-date');
    if (resumenDate) resumenDate.value = val;
    if (genDate) genDate.value = val;
}

// ================================================================
// IMAGEN DE FONDO - CHECK "EN PROCESO"
// ================================================================
function toggleBgInput(checked) {
    const wrapper = document.getElementById('bg-wrapper');
    const fileInput = document.getElementById('bg-file');
    if (!wrapper || !fileInput) return;
    if (checked) {
        wrapper.classList.add('disabled');
        fileInput.disabled = true;
        const span = wrapper.querySelector('span');
        if (span) span.innerText = 'En Proceso — No Editable';
    } else {
        wrapper.classList.remove('disabled');
        fileInput.disabled = false;
        const span = wrapper.querySelector('span');
        if (span) span.innerText = 'Seleccionar diseño del evento...';
    }
}

// ================================================================
// ORDEN DEL DÍA - CÁLCULO DE HORAS Y DURACIÓN
// ================================================================
function calculateAgenda() {
    const startInput = document.getElementById('start-time');
    const start = startInput ? startInput.value : registroHoraInicio || '09:00';
    const limitInput = document.getElementById('limit-duration');
    const limit = limitInput ? parseInt(limitInput.value) || 540 : 540;
    const rows = document.querySelectorAll('#ordenBody tr');
    let [h, m] = start.split(':').map(Number);
    let total = 0;

    rows.forEach(r => {
        const startInputRow = r.querySelector('.row-start');
        const durInput = r.querySelector('.row-dur');
        if (startInputRow && durInput) {
            startInputRow.value = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
            let dur = parseInt(durInput.value) || 0;
            total += dur;
            m += dur;
            h += Math.floor(m / 60);
            m = m % 60;
            h = h % 24;
        }
    });

    const totalLabel = document.getElementById('total-calculated-label');
    const limitLabel = document.getElementById('total-limit-label');
    if (totalLabel) totalLabel.innerText = total;
    if (limitLabel) limitLabel.innerText = limit;

    const alertBox = document.getElementById('duration-alert-box');
    if (alertBox) {
        if (total > limit) {
            alertBox.classList.add('alert-active');
            alert('⚠️ Fin del evento - Duración total calculada del programa: ' + total + ' min\nDuración total límite configurada (del bloque generales): ' + limit + ' min.');
        } else {
            alertBox.classList.remove('alert-active');
        }
    }
}

function agregarFilaOrden() {
    const tbody = document.getElementById('ordenBody');
    if (!tbody) return;
    const idx = tbody.children.length;
    const tr = document.createElement('tr');

    let optionsHtml = '<option value="">Seleccione...</option>';
    usuarios.forEach(u => {
        optionsHtml += `<option value="${u.id}">${u.nombre}</option>`;
    });
    optionsHtml += '<option value="otro">+ Otro</option>';

    tr.innerHTML = `
        <td><input type="text" class="row-start" name="orden[${idx}][hora_inicio]" readonly style="background:#eee; width:100%;"></td>
        <td><input type="text" name="orden[${idx}][actividad]" style="width:100%;"></td>
        <td>
            <select name="orden[${idx}][responsable_id]" style="width:100%;" onchange="mostrarOtro(this)">
                ${optionsHtml}
            </select>
            <input type="text" name="orden[${idx}][otro_responsable]" placeholder="Nombre" style="display:none; margin-top:4px; width:100%;">
        </td>
        <td><input type="number" class="row-dur" name="orden[${idx}][duracion]" value="15" step="1" min="1" style="width:100%;" onchange="calculateAgenda()"></td>
        <td><button type="button" class="btn btn-danger" onclick="eliminarFilaOrden(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
    calculateAgenda();
}

function eliminarFilaOrden(btn) {
    const row = btn.closest('tr');
    if (row && row.parentElement.children.length > 1) {
        row.remove();
        calculateAgenda();
    }
}

function mostrarOtro(select) {
    const input = select.closest('td').querySelector('input[type="text"]');
    if (!input) return;
    if (select.value === 'otro') {
        input.style.display = 'block';
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

// ================================================================
// PRESÍDIUM - RENDERIZADO VISUAL Y LISTA (CORREGIDO)
// ================================================================
function savePresidiumState() {
    const inputs = document.querySelectorAll('#p-inputs .presidium-row-item');
    inputs.forEach((row, idx) => {
        const nameInput = row.querySelector('input[id^="name-"]');
        const cargoInput = row.querySelector('input[id^="cargo-"]');
        if (nameInput) savedNames[idx] = nameInput.value;
        if (cargoInput) savedCargos[idx] = cargoInput.value;
    });
}

function adjustSpots(amt) {
    currentSpots += amt;
    if (currentSpots < 1) currentSpots = 1;
    if (currentSpots > 15) currentSpots = 15;
    const spotsCount = document.getElementById('spots-count');
    if (spotsCount) spotsCount.innerText = currentSpots;
    renderPresidium();
}

function renderPresidium() {
    savePresidiumState();
    const typeSelect = document.getElementById('p-type');
    if (!typeSelect) return;
    const type = typeSelect.value;
    currentType = type;
    const hidden = document.getElementById('tipoPresidiumHidden');
    if (hidden) hidden.value = type;

    const canvas = document.getElementById('p-canvas');
    const inputs = document.getElementById('p-inputs');
    if (!canvas || !inputs) return;
    canvas.innerHTML = '';
    inputs.innerHTML = '';

    // Asegurar dimensiones
    canvas.style.width = '100%';
    canvas.style.height = '240px';
    canvas.style.position = 'relative';
    canvas.style.border = '1px dashed rgba(128,0,0,0.2)';
    canvas.style.background = '#fff';

    const total = currentSpots;
    // Generar orden: * en el centro, impares a la izquierda (descendente), pares a la derecha (ascendente)
    let order = ['*'];
    let left = [], right = [];
    for (let i = 1; i < total; i++) {
        if (i % 2 !== 0) left.push(i);
        else right.push(i);
    }
    left.reverse();
    order = left.concat(['*']).concat(right);

    // === DIBUJAR FIGURA NEGRA (según tipo) ===
    // Para lineal no se dibuja figura
    if (type !== 'lineal') {
        const shape = document.createElement('div');
        shape.className = 'presidium-shape-black';
        shape.style.position = 'absolute';
        shape.style.backgroundColor = '#111';
        shape.style.opacity = '0.85';
        shape.style.zIndex = '5';
        shape.style.transform = 'translate(-50%, -50%)';
        shape.style.left = '50%';
        shape.style.top = '50%';

        switch (type) {
            case 'redondo':
                shape.style.width = '140px';
                shape.style.height = '140px';
                shape.style.borderRadius = '50%';
                shape.style.backgroundColor = '#111';
                break;
            case 'herradura':
                shape.style.width = '150px';
                shape.style.height = '100px';
                shape.style.border = '15px solid #111';
                shape.style.borderBottom = 'none';
                shape.style.borderRadius = '80px 80px 0 0';
                shape.style.backgroundColor = 'transparent';
                break;
            case 'media_luna':
                // Corregido: curva hacia arriba (como sonrisa)
                shape.style.width = '150px';
                shape.style.height = '100px';
                shape.style.border = '15px solid #111';
                shape.style.borderBottom = 'none';
                shape.style.borderRadius = '80px 80px 0 0'; // curva arriba
                shape.style.backgroundColor = 'transparent';
                shape.style.top = '55%'; // ajustar posición vertical
                break;
            case 'rusa':
                shape.style.width = '160px';
                shape.style.height = '90px';
                shape.style.border = '15px solid #111';
                shape.style.borderBottom = 'none';
                shape.style.borderRadius = '0';
                shape.style.backgroundColor = 'transparent';
                break;
            case 'cuadrada':
                shape.style.width = '160px';
                shape.style.height = '120px';
                shape.style.border = '15px solid #111';
                shape.style.borderRadius = '0';
                shape.style.backgroundColor = 'transparent';
                break;
            default:
                break;
        }
        canvas.appendChild(shape);
    }

    // === DIBUJAR PUNTOS (círculos) ===
    // Siempre dibujamos puntos (incluso en lineal)
    order.forEach((spot, index) => {
        let leftPos = 50, topPos = 50;
        const totalSpots = order.length;

        // Calcular posición según tipo
        if (type === 'lineal') {
            // Línea horizontal: distribución uniforme a lo largo del ancho
            leftPos = 10 + (index / (totalSpots - 1)) * 80;
            topPos = 50;
        } else {
            switch (type) {
                case 'redondo':
                    const angleR = (index / totalSpots) * 2 * Math.PI - Math.PI / 2;
                    leftPos = 50 + 32 * Math.cos(angleR);
                    topPos = 50 + 32 * Math.sin(angleR);
                    break;
                case 'herradura':
                    const angleH = (index / (totalSpots - 1)) * Math.PI;
                    leftPos = 50 + 35 * Math.cos(angleH + Math.PI);
                    topPos = 45 + 32 * Math.sin(angleH + Math.PI);
                    break;
                case 'media_luna':
                    // Curva hacia arriba
                    const angleM = (index / (totalSpots - 1)) * Math.PI;
                    leftPos = 50 + 35 * Math.cos(angleM);
                    topPos = 55 - 32 * Math.sin(angleM); // invertir signo para curva arriba
                    break;
                case 'rusa':
                    const seg = Math.max(1, totalSpots - 1);
                    leftPos = 20 + (index * (60 / seg));
                    topPos = (index === 0 || index === totalSpots - 1) ? 65 : 40;
                    break;
                case 'cuadrada':
                    const cols = 4;
                    const row = Math.floor(index / cols);
                    const col = index % cols;
                    leftPos = 25 + col * 16;
                    topPos = 30 + row * 18;
                    break;
                default:
                    break;
            }
        }

        // Crear el punto
        const sDiv = document.createElement('div');
        sDiv.className = `presidium-spot ${spot === '*' ? 'center-spot' : ''}`;
        sDiv.style.left = leftPos + '%';
        sDiv.style.top = topPos + '%';
        sDiv.innerText = spot;
        canvas.appendChild(sDiv);
    });

    // === CREAR LISTA DE INPUTS (nombres y cargos) ===
    order.forEach((spot, index) => {
        const row = document.createElement('div');
        row.className = 'presidium-row-item';
        let side = spot === '*' ? 'Centro (Presidente)' : (spot % 2 !== 0 ? 'Izquierda' : 'Derecha');
        let oldN = savedNames[index] || '';
        let oldC = savedCargos[index] || '';

        if (spot === '*') {
            row.innerHTML = `
                <div class="spot-tag">*</div>
                <input type="text" value="Lcdo. Ricardo Moreno Bastida" readonly style="flex:1; background:#eee; font-weight:bold;">
                <input type="text" value="Presidente Municipal Constitucional de Toluca" readonly style="width:35%; background:#eee;">
            `;
        } else {
            row.innerHTML = `
                <div class="spot-tag">${spot}</div>
                <input type="text" id="name-${index}" value="${oldN}" style="flex:1;" placeholder="Nombre (${side})">
                <input type="text" id="cargo-${index}" value="${oldC}" style="width:35%;" placeholder="Cargo">
            `;
        }
        inputs.appendChild(row);
    });
}

// ================================================================
// INVITADOS ESPECIALES
// ================================================================
function agregarFilaInvitados() {
    const tbody = document.getElementById('invitadosBody');
    if (!tbody) return;
    const idx = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>${idx + 1}</td>
        <td><input type="text" name="invitados[${idx}][nombre]" style="width:100%;"></td>
        <td><input type="text" name="invitados[${idx}][cargo]" style="width:100%; font-weight:bold;"></td>
        <td><button type="button" class="btn btn-danger" onclick="eliminarFilaInvitados(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
    reordenarInvitados();
}

function eliminarFilaInvitados(btn) {
    const row = btn.closest('tr');
    if (row && row.parentElement.children.length > 1) {
        row.remove();
        reordenarInvitados();
    }
}

function reordenarInvitados() {
    const rows = document.querySelectorAll('#invitadosBody tr');
    rows.forEach((r, idx) => {
        r.cells[0].innerText = idx + 1;
    });
}

// ================================================================
// MÓDULOS JORNADA INTEGRAL
// ================================================================
function agregarFilaModulos() {
    const tbody = document.getElementById('modulosBody');
    if (!tbody) return;
    const idx = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>${idx + 1}</td>
        <td><input type="text" name="modulos[${idx}][institucion]" style="width:100%;"></td>
        <td><input type="text" name="modulos[${idx}][servicio]" style="width:100%;"></td>
        <td><button type="button" class="btn btn-danger" onclick="eliminarFilaModulos(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
    reordenarModulos();
}

function eliminarFilaModulos(btn) {
    const row = btn.closest('tr');
    if (row && row.parentElement.children.length > 1) {
        row.remove();
        reordenarModulos();
    }
}

function reordenarModulos() {
    const rows = document.querySelectorAll('#modulosBody tr');
    rows.forEach((r, idx) => {
        r.cells[0].innerText = idx + 1;
    });
}

// ================================================================
// REQUERIMIENTOS - AGREGAR Y ELIMINAR
// ================================================================
function agregarFilaReq(tableId, tipo) {
    const tbody = document.querySelector(`#${tableId} tbody`);
    if (!tbody) return;
    const idx = tbody.children.length;
    const tr = document.createElement('tr');

    const prefijo = tipo === 'internos' ? 'req_internos' : 'req_externos';
    const options = tipo === 'internos' ? insumosInternos : insumosExternos;

    let optHtml = '<option value="">Seleccione...</option>';
    options.forEach(ins => {
        optHtml += `<option value="${ins.id}" data-medida="${ins.medida || ''}" data-unidad="${ins.unidad || ''}" data-stock="${ins.stock_total}">${ins.nombre_insumo}</option>`;
    });

    tr.innerHTML = `
        <td><input type="number" name="${prefijo}[${idx}][cantidad]" value="1" min="1" class="rcant" style="width:100%;"></td>
        <td><select name="${prefijo}[${idx}][insumo_id]" class="rins" style="width:100%;" onchange="actualizarMedidaUnidad(this)">${optHtml}</select></td>
        <td><input type="text" name="${prefijo}[${idx}][medida]" placeholder="Medida" class="rmedida" style="width:100%;" readonly></td>
        <td><input type="text" name="${prefijo}[${idx}][unidad]" placeholder="Unidad" class="runidad" style="width:100%;" readonly></td>
        <td><button type="button" class="btn btn-danger" onclick="eliminarFilaReq(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
}

function eliminarFilaReq(btn) {
    const row = btn.closest('tr');
    if (row && row.parentElement.children.length > 1) {
        row.remove();
    }
}

function actualizarMedidaUnidad(select) {
    const row = select.closest('tr');
    const medidaInput = row.querySelector('.rmedida');
    const unidadInput = row.querySelector('.runidad');
    const option = select.options[select.selectedIndex];
    if (option && option.value) {
        if (medidaInput) medidaInput.value = option.dataset.medida || '';
        if (unidadInput) unidadInput.value = option.dataset.unidad || '';
    } else {
        if (medidaInput) medidaInput.value = '';
        if (unidadInput) unidadInput.value = '';
    }
}

// ================================================================
// SOLICITAR Y BLOQUEAR RECURSOS (CON VALIDACIÓN DE STOCK - SIN MOSTRAR CANTIDADES)
// ================================================================
function lockAndConsolidate() {
    const container = document.getElementById('consolidated-container');
    let errores = [];

    document.querySelectorAll('#t-int tbody tr').forEach(r => {
        const cantInput = r.querySelector('.rcant');
        const select = r.querySelector('.rins');
        if (select && select.value) {
            const stock = parseInt(select.options[select.selectedIndex].dataset.stock) || 0;
            const cantidad = parseInt(cantInput.value) || 0;
            if (cantidad > stock) {
                const insumo = select.options[select.selectedIndex].text;
                errores.push(`- ${insumo}`);
            }
        }
    });

    document.querySelectorAll('#t-ext tbody tr').forEach(r => {
        const cantInput = r.querySelector('.rcant');
        const select = r.querySelector('.rins');
        if (select && select.value) {
            const stock = parseInt(select.options[select.selectedIndex].dataset.stock) || 0;
            const cantidad = parseInt(cantInput.value) || 0;
            if (cantidad > stock) {
                const insumo = select.options[select.selectedIndex].text;
                errores.push(`- ${insumo}`);
            }
        }
    });

    if (errores.length > 0) {
        alert('❌ No hay suficiente stock para los siguientes insumos:\n' + errores.join('\n') + '\nPor favor ajusta las cantidades.');
        return;
    }

    const internos = [];
    document.querySelectorAll('#t-int tbody tr').forEach(r => {
        const cant = r.querySelector('.rcant')?.value || 1;
        const select = r.querySelector('.rins');
        const insumo = select ? select.options[select.selectedIndex] : null;
        const medida = r.querySelector('.rmedida')?.value || '';
        const unidad = r.querySelector('.runidad')?.value || '';
        if (insumo && insumo.value) {
            internos.push({
                cantidad: cant,
                insumo_id: insumo.value,
                nombre_insumo: insumo.text,
                medida: medida,
                unidad: unidad
            });
        }
    });

    const externos = [];
    document.querySelectorAll('#t-ext tbody tr').forEach(r => {
        const cant = r.querySelector('.rcant')?.value || 1;
        const select = r.querySelector('.rins');
        const insumo = select ? select.options[select.selectedIndex] : null;
        const medida = r.querySelector('.rmedida')?.value || '';
        const unidad = r.querySelector('.runidad')?.value || '';
        if (insumo && insumo.value) {
            externos.push({
                cantidad: cant,
                insumo_id: insumo.value,
                nombre_insumo: insumo.text,
                medida: medida,
                unidad: unidad
            });
        }
    });

    if (internos.length === 0 && externos.length === 0) {
        alert('No hay requerimientos para solicitar.');
        return;
    }

    let html = '<h4 style="font-size:14px; margin-bottom:10px; color:#800000;">Consolidado de Requerimientos Solicitados</h4>';
    html += '<table><thead><tr><th>Origen</th><th>Cantidad</th><th>Insumo</th><th>Medida</th><th>Unidad</th></tr></thead><tbody>';
    internos.forEach(item => {
        html += `<tr><td><span style="color:#28a745; font-weight:bold;">Interno</span></td><td>${item.cantidad}</td><td><strong>${item.nombre_insumo}</strong></td><td>${item.medida}</td><td>${item.unidad}</td></tr>`;
    });
    externos.forEach(item => {
        html += `<tr><td><span style="color:#007bff; font-weight:bold;">Externo</span></td><td>${item.cantidad}</td><td><strong>${item.nombre_insumo}</strong></td><td>${item.medida}</td><td>${item.unidad}</td></tr>`;
    });
    html += '</tbody></table>';
    if (container) container.innerHTML = html;

    alert('✅ Insumos bloqueados exitosamente.');
}

// ================================================================
// ENVÍO DEL FORMULARIO USANDO FormData
// ================================================================
function enviarFormulario() {
    const form = document.getElementById('carpetaForm');
    if (!form) return;
    const formData = new FormData(form);

    const presidiumList = [];
    document.querySelectorAll('#p-inputs .presidium-row-item').forEach((row, idx) => {
        const spotTag = row.querySelector('.spot-tag');
        const nombreInput = row.querySelector('input[id^="name-"]');
        const cargoInput = row.querySelector('input[id^="cargo-"]');
        if (spotTag && nombreInput && cargoInput) {
            presidiumList.push({
                orden: spotTag.innerText,
                nombre: nombreInput.value,
                cargo: cargoInput.value
            });
        }
    });
    formData.append('presidium_data', JSON.stringify(presidiumList));

    fetch('/Dir_bienestar/eventos/guardar_carpeta', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Carpeta guardada correctamente.');
                if (result.carpeta_id) {
                    window.location.href = '/Dir_bienestar/eventos/editar_carpeta?id_registro=' + formData.get('registro_actividad_id');
                }
            } else {
                alert('Error: ' + (result.error || 'No se pudo guardar'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión. Revisa la consola para más detalles.');
        });
}

// ================================================================
// GENERAR POWERPOINT (PDF)
// ================================================================
function generarPowerPoint() {
    const form = document.getElementById('carpetaForm');
    if (!form) return;
    const formData = new FormData(form);
    const registroId = formData.get('registro_actividad_id');
    if (registroId) {
        window.location.href = '/Dir_bienestar/evento_ppt/generar?id_registro=' + registroId;
    } else {
        alert('No se encontró el registro de actividad.');
    }
}

// ================================================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    const mainDate = document.getElementById('main-date');
    if (mainDate) syncDates(mainDate.value);

    calculateAgenda();
    renderPresidium();

    const bgCheck = document.getElementById('bg-in-progress');
    if (bgCheck && bgCheck.checked) {
        toggleBgInput(true);
    }

    const guardarBtn = document.getElementById('guardarBtn');
    if (guardarBtn) {
        guardarBtn.addEventListener('click', enviarFormulario);
    }

    const generarBtn = document.getElementById('generarPPTBtn');
    if (generarBtn) {
        generarBtn.addEventListener('click', generarPowerPoint);
    }
});