    const BASE_URL = '/Dir_bienestar';
    const modal = document.getElementById('confirmModal');
    let currentFormData = null;

    // Elementos
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
    const fechaInicioInput = document.getElementById('fecha_inicio');
    const fechaFinInput = document.getElementById('fecha_fin');
    const horaInicioInput = document.getElementById('hora_inicio');
    const horaFinInput = document.getElementById('hora_fin');

    let allCps = [];

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

    // --- Delegación: cargar subdelegaciones y CPs ---
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

    // --- Obtener datos ---
    function getFormData() {
        let lugar_id = lugarSelect.value;
        let otro_lugar = (lugar_id === '0') ? otroLugarInput.value.trim() : '';
        let cp = cpSelect.value ? cpSelect.value : null;
        let subdelegacion_id = subdelegacionSelect.value || null;

        const calle = calleInput.value.trim();
        const numeroExterior = numeroExteriorInput.value.trim();
        const numeroInterior = document.getElementById('numero_interior').value ? document.getElementById('numero_interior').value.trim() : null;
        const descripcion = descripcionInput.value.trim();

        return {
            responsable: document.getElementById('responsable').value,
            unidad_administrativa_id: unidadSelect.value,
            actividad_programada_id: actividadSelect.value,
            unidad_medida_id: unidadMedidaIdHidden.value,
            fecha_inicio: fechaInicioInput.value,
            fecha_fin: fechaFinInput.value,
            hora_inicio: horaInicioInput.value,
            hora_fin: horaFinInput.value,
            lugar_id: lugar_id,
            otro_lugar: otro_lugar,
            delegacion_id: delegacionSelect.value,
            subdelegacion_id: subdelegacion_id,
            cp: cp,
            calle: calle,
            numero_exterior: numeroExterior,
            numero_interior: numeroInterior,
            beneficiarios_asistentes: document.getElementById('beneficiarios_asistentes').value,
            tipo_entregable_id: document.getElementById('tipo_entregable_id').value,
            descripcion: descripcion
        };
    }

    // --- Validación ---
    function validate(data) {
        if (!data.unidad_administrativa_id) { mostrarToast('Seleccione unidad administrativa', true); return false; }
        if (!data.actividad_programada_id) { mostrarToast('Seleccione actividad', true); return false; }
        if (!data.fecha_inicio) { mostrarToast('Ingrese fecha inicio', true); return false; }
        if (!data.fecha_fin) { mostrarToast('Ingrese fecha fin', true); return false; }
        if (data.fecha_fin < data.fecha_inicio) { mostrarToast('La fecha fin no puede ser anterior a la fecha inicio', true); return false; }
        if (!data.hora_inicio) { mostrarToast('Ingrese hora inicio', true); return false; }
        if (!data.hora_fin) { mostrarToast('Ingrese hora fin', true); return false; }
        // Validación CRÍTICA: hora fin debe ser mayor que hora inicio
        if (data.hora_fin <= data.hora_inicio) {
            mostrarToast('La hora fin debe ser mayor que la hora inicio', true);
            return false;
        }
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
        return true;
    }

    // --- Modal ---
    function showModal(data) {
        const container = document.getElementById('modalDataSummary');
        let lugarTexto = lugarSelect.options[lugarSelect.selectedIndex]?.text;
        if (data.lugar_id === '0') lugarTexto = `Otro: ${data.otro_lugar}`;
        let cpTexto = cpSelect.options[cpSelect.selectedIndex]?.text || 'No seleccionado';
        let subTexto = subdelegacionSelect.options[subdelegacionSelect.selectedIndex]?.text || 'No aplica';
        let delegTexto = delegacionSelect.options[delegacionSelect.selectedIndex]?.text || '';
        let unidadTexto = unidadSelect.options[unidadSelect.selectedIndex]?.text || '';
        let actividadTexto = actividadSelect.options[actividadSelect.selectedIndex]?.text || '';

        let periodoTexto = data.fecha_inicio;
        if (data.fecha_fin && data.fecha_fin !== data.fecha_inicio) {
            periodoTexto += ` al ${data.fecha_fin}`;
        }
        periodoTexto += ` ${data.hora_inicio} - ${data.hora_fin}`;

        container.innerHTML = `
            <div class="summary-row"><span class="summary-label">Responsable:</span><span>${escapeHtml(data.responsable)}</span></div>
            <div class="summary-row"><span class="summary-label">Unidad:</span><span>${escapeHtml(unidadTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Actividad:</span><span>${escapeHtml(actividadTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Período:</span><span>${escapeHtml(periodoTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Lugar:</span><span>${escapeHtml(lugarTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Delegación:</span><span>${escapeHtml(delegTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Subdelegación:</span><span>${escapeHtml(subTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Código Postal:</span><span>${escapeHtml(cpTexto)}</span></div>
            <div class="summary-row"><span class="summary-label">Domicilio:</span><span>${escapeHtml(data.calle)} ${escapeHtml(data.numero_exterior)}, Int. ${escapeHtml(data.numero_interior || '')}</span></div>
            <div class="summary-row"><span class="summary-label">Beneficiarios:</span><span>${data.beneficiarios_asistentes}</span></div>
            <div class="summary-row"><span class="summary-label">Entregable:</span><span>${escapeHtml(document.getElementById('tipo_entregable_id').options[document.getElementById('tipo_entregable_id').selectedIndex]?.text || '')}</span></div>
            <div class="summary-row"><span class="summary-label">Descripción:</span><span>${escapeHtml(data.descripcion)}</span></div>
            ${data.fecha_fin !== data.fecha_inicio ? `<div class="summary-row" style="color:#a90303; font-weight:bold;"><span colspan="2">⚠️ Se generarán ${calcularDiferenciaDias(data.fecha_inicio, data.fecha_fin)} registros (uno por día)</span></div>` : ''}
        `;
        modal.classList.add('active');
    }

    function calcularDiferenciaDias(fechaInicio, fechaFin) {
        const inicio = new Date(fechaInicio);
        const fin = new Date(fechaFin);
        const diffTime = Math.abs(fin - inicio);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    }

    // --- Enviar ---
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
        const hoy = new Date().toISOString().split('T')[0];
        fechaInicioInput.value = hoy;
        fechaFinInput.value = hoy;
        // Limpiar valores de hora (opcional)
        horaInicioInput.value = '';
        horaFinInput.value = '';
    }

    // --- Eventos ---
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

    document.addEventListener('DOMContentLoaded', function() {
        const hoy = new Date().toISOString().split('T')[0];
        fechaInicioInput.value = hoy;
        fechaFinInput.value = hoy;
    });
