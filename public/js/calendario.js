    const BASE_URL = '/Dir_bienestar';
    let currentYear = 2026;
    let currentMonth = 5;   // 0-index: junio = 5
    let currentDateFilter = null;
    let allActivities = [];
    let allActividades = [];  // Para guardar todas las actividades y filtrar localmente

    // Elementos de filtros
    const filtroUA = document.getElementById('filtroUA');
    const filtroAct = document.getElementById('filtroAct');
    const filtroResp = document.getElementById('filtroResp');
    const filtroLugar = document.getElementById('filtroLugar');
    const filtroDel = document.getElementById('filtroDel');
    const filtroDom = document.getElementById('filtroDom');

    // --- Cargar todas las actividades (para el filtro dependiente) ---
    async function loadAllActividades() {
        try {
            const response = await fetch(`${BASE_URL}/dashboard/actividadesPorUnidad/0`); // 0 = todas
            // Pero mejor: cargar todas desde un endpoint que devuelva todas las actividades
            // Como no tenemos ese endpoint, usamos las que ya vienen en el select de PHP,
            // pero en el frontend las cargamos con un fetch a un endpoint que no existe.
            // Solución: usar el select de PHP y manejarlo con JavaScript.
            // O mejor: agregar un endpoint en DashboardController que devuelva todas las actividades.
            // Por ahora, usamos las opciones que ya vienen en el select de PHP,
            // pero las recargamos dinámicamente al cambiar unidad.
            // Para ello, necesitamos un endpoint que devuelva actividades por unidad.
            // Ya existe: /dashboard/actividadesPorUnidad/{unidadId}
            // Lo usaremos.
        } catch (e) {
            console.error('Error cargando actividades:', e);
        }
    }

    // --- Cargar actividades por unidad (para el filtro) ---
    async function loadActividadesPorUnidad(unidadId) {
        if (!unidadId) {
            // Mostrar todas las actividades (las del select original)
            // Pero mejor: cargar todas las actividades de la base de datos
            // Para simplificar, hacemos una petición a un endpoint que devuelva todas las actividades
            // Usamos el endpoint existente pero con un parámetro especial (ej. 0 = todas)
            try {
                const response = await fetch(`${BASE_URL}/dashboard/actividadesPorUnidad/0`);
                if (response.ok) {
                    const data = await response.json();
                    populateActividadesSelect(data);
                } else {
                    // Fallback: no cargar nada
                    filtroAct.innerHTML = '<option value="">Todas</option>';
                }
            } catch (e) {
                filtroAct.innerHTML = '<option value="">Todas</option>';
            }
            return;
        }
        try {
            const response = await fetch(`${BASE_URL}/dashboard/actividadesPorUnidad/${unidadId}`);
            if (!response.ok) throw new Error('Error al cargar actividades');
            const data = await response.json();
            populateActividadesSelect(data);
        } catch (error) {
            console.error('Error cargando actividades por unidad:', error);
            filtroAct.innerHTML = '<option value="">Todas</option>';
        }
    }

    function populateActividadesSelect(actividades) {
        let options = '<option value="">Todas</option>';
        if (actividades && actividades.length > 0) {
            actividades.forEach(act => {
                options += `<option value="${act.id}">${escapeHtml(act.descripcion)}</option>`;
            });
        }
        filtroAct.innerHTML = options;
    }

    // --- Escuchar cambio de unidad ---
    filtroUA.addEventListener('change', function() {
        const unidadId = this.value;
        loadActividadesPorUnidad(unidadId);
        // Después de cargar, disparar la carga de datos
        loadActivities();
    });

    // --- Obtener actividades filtradas desde el servidor ---
    async function loadActivities() {
        const params = new URLSearchParams();
        params.append('year', currentYear);
        params.append('month', currentMonth + 1);
        const respFilter = filtroResp.value.trim();
        if (respFilter) params.append('filtro_responsable', respFilter);
        const unidad = filtroUA.value;
        if (unidad) params.append('filtro_unidad', unidad);
        const lugar = filtroLugar.value;
        if (lugar) params.append('filtro_lugar', lugar);
        const deleg = filtroDel.value;
        if (deleg) params.append('filtro_delegacion', deleg);
        const act = filtroAct.value;
        if (act) params.append('filtro_actividad', act);
        const dom = filtroDom.value.trim();
        if (dom) params.append('filtro_domicilio', dom);
        if (currentDateFilter) params.append('fecha_dia', currentDateFilter);
        
        try {
            const response = await fetch(`${BASE_URL}/calendario/datos?${params.toString()}`);
            const data = await response.json();
            allActivities = data;
            renderTable();
            renderCalendar();
        } catch (error) {
            console.error('Error cargando actividades:', error);
            document.getElementById('dynamicContent').innerHTML = '<div class="empty-placeholder">Error al cargar datos</div>';
        }
    }

    // --- Renderizar tabla ---
    function renderTable() {
    const container = document.getElementById('dynamicContent');
    if (!allActivities.length) {
        container.innerHTML = '<div class="empty-placeholder">📭 No hay actividades con los filtros seleccionados.</div>';
        return;
    }
    let html = `<table class="activity-table">
        <thead>
            <tr>
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <th>Hora inicio</th>
                <th>Hora fin</th>
                <th>Responsable</th>
                <th>Unidad</th>
                <th>Actividad</th>
                <th>Beneficiarios / Asistentes</th>
                <th>Descripción</th>
                <th>Lugar</th>
                <th>Delegación</th>
                <th>Subdelegación</th>
                <th>Domicilio</th>
            </tr>
        </thead>
        <tbody>`;
    allActivities.forEach(item => {
        const fechaInicio = item.fecha_inicio || '';
        const fechaFin = item.fecha_fin || '';
        const horaInicio = item.hora_inicio ? item.hora_inicio.substring(0,5) : '';
        const horaFin = item.hora_fin ? item.hora_fin.substring(0,5) : '';
        // Construir domicilio + CP
        let domicilioCompleto = item.domicilio_completo || '';
        if (item.codigo_postal) {
            domicilioCompleto += ` CP ${item.codigo_postal}`;
        }
        const descripcion = item.descripcion_actividad || '';
        const descripcionCorta = descripcion.length > 80 ? descripcion.substring(0,80) + '…' : descripcion;
        html += `<tr>
            <td>${fechaInicio}</td>
            <td>${fechaFin}</td>
            <td>${horaInicio}</td>
            <td>${horaFin}</td>
            <td>${escapeHtml(item.responsable)}</td>
            <td>${escapeHtml(item.unidad_nombre)}</td>
            <td>${escapeHtml(item.actividad_desc || '')}</td>
            <td>${item.cantidad}</td>
            <td title="${escapeHtml(descripcion)}">${escapeHtml(descripcionCorta)}</td>
            <td>${escapeHtml(item.lugar_nombre)}</td>
            <td>${escapeHtml(item.delegacion_nombre || '')}</td>
            <td>${escapeHtml(item.subdelegacion_nombre || '')}</td>
            <td style="max-width:280px;">${escapeHtml(domicilioCompleto)}</td>
        </tr>`;
    });
    html += '</tbody></table>';
    container.innerHTML = html;
}

    // --- Renderizar calendario ---
    function renderCalendar() {
        const firstDay = new Date(currentYear, currentMonth, 1);
        let startWeekday = firstDay.getDay();
        let startOffset = startWeekday === 0 ? 6 : startWeekday - 1;
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const prevDays = new Date(currentYear, currentMonth, 0).getDate();
        
        const eventDateSet = new Set();
        allActivities.forEach(a => {
            if (a.fecha_inicio) eventDateSet.add(a.fecha_inicio);
            if (a.fecha_fin && a.fecha_fin !== a.fecha_inicio) eventDateSet.add(a.fecha_fin);
        });
        
        let gridHtml = '';
        let dayCounter = 1;
        let nextCounter = 1;
        
        for(let i = 0; i < 42; i++) {
            let year = currentYear, month = currentMonth, dayNum, isCurrentMonth;
            let dateStr;
            if(i < startOffset) {
                dayNum = prevDays - startOffset + i + 1;
                month = currentMonth - 1;
                isCurrentMonth = false;
                if(month < 0) { month = 11; year = currentYear - 1; }
            } else if(i >= startOffset + daysInMonth) {
                dayNum = nextCounter++;
                month = currentMonth + 1;
                isCurrentMonth = false;
                if(month > 11) { month = 0; year = currentYear + 1; }
            } else {
                dayNum = dayCounter++;
                month = currentMonth;
                isCurrentMonth = true;
            }
            const mm = String(month + 1).padStart(2,'0');
            const dd = String(dayNum).padStart(2,'0');
            dateStr = `${year}-${mm}-${dd}`;
            let cls = isCurrentMonth ? 'current-month' : 'other-month';
            if(eventDateSet.has(dateStr) && isCurrentMonth) cls = 'has-activity';
            gridHtml += `<div class="cal-day ${cls}" data-fecha="${dateStr}">${dayNum}</div>`;
        }
        document.getElementById('calendarGrid').innerHTML = gridHtml;
        
        const monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        document.getElementById('monthYearLabel').innerHTML = `${monthNames[currentMonth]} ${currentYear}`;
        
        const eventosMes = allActivities.length;
        const diasOcup = new Set(allActivities.map(a => a.fecha_inicio)).size;
        const totalDiasMes = daysInMonth;
        document.getElementById('resumenMensual').innerHTML = `
            <div class="resumen-stat"><div class="num">${eventosMes}</div><div class="label">Eventos</div></div>
            <div class="resumen-stat"><div class="num">${diasOcup}</div><div class="label">Días ocupados</div></div>
            <div class="resumen-stat"><div class="num">${totalDiasMes - diasOcup}</div><div class="label">Días libres</div></div>
        `;
        
        document.querySelectorAll('.cal-day').forEach(el => {
            el.removeEventListener('click', dayClickHandler);
            el.addEventListener('click', dayClickHandler);
        });
    }
    
    function dayClickHandler(e) {
        const fecha = this.getAttribute('data-fecha');
        if(currentDateFilter === fecha) {
            currentDateFilter = null;
        } else {
            currentDateFilter = fecha;
        }
        loadActivities();
        showToast(currentDateFilter ? `Filtrando día: ${currentDateFilter}` : 'Filtro de día eliminado');
    }
    
    function showToast(msg) {
        let toast = document.querySelector('.custom-toast');
        if(!toast) {
            toast = document.createElement('div');
            toast.className = 'custom-toast';
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.left = '20px';
            toast.style.backgroundColor = '#800000';
            toast.style.color = 'white';
            toast.style.padding = '6px 12px';
            toast.style.borderRadius = '30px';
            toast.style.fontSize = '0.7rem';
            toast.style.zIndex = '999';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.style.opacity = '1';
        setTimeout(() => toast.style.opacity = '0', 1500);
    }
    
    function changeMonth(delta) {
        let newMonth = currentMonth + delta;
        let newYear = currentYear;
        if(newMonth < 0) { newMonth = 11; newYear--; }
        if(newMonth > 11) { newMonth = 0; newYear++; }
        currentMonth = newMonth;
        currentYear = newYear;
        document.getElementById('yearInput').value = currentYear;
        document.getElementById('monthSelect').value = currentMonth;
        currentDateFilter = null;
        loadActivities();
    }
    
    function setYearMonth() {
        currentYear = parseInt(document.getElementById('yearInput').value);
        currentMonth = parseInt(document.getElementById('monthSelect').value);
        if(isNaN(currentYear)) currentYear = 2026;
        if(currentYear < 2024) currentYear = 2024;
        if(currentYear > 2030) currentYear = 2030;
        currentDateFilter = null;
        loadActivities();
    }
    
    function initMonthSelect() {
        const select = document.getElementById('monthSelect');
        const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        for(let i=0; i<12; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = meses[i];
            select.appendChild(option);
        }
        select.value = currentMonth;
        select.addEventListener('change', setYearMonth);
        document.getElementById('yearInput').addEventListener('change', setYearMonth);
        document.getElementById('prevMonthBtn').addEventListener('click', () => changeMonth(-1));
        document.getElementById('nextMonthBtn').addEventListener('click', () => changeMonth(1));
    }
    
    function resetFilters() {
        filtroResp.value = '';
        filtroUA.value = '';
        filtroLugar.value = '';
        filtroDel.value = '';
        filtroAct.value = '';
        filtroDom.value = '';
        currentDateFilter = null;
        // Restaurar actividades a todas
        loadActividadesPorUnidad('');
        loadActivities();
    }
    
    function attachFilterEvents() {
        const filterIds = ['filtroResp', 'filtroLugar', 'filtroDel', 'filtroDom'];
        filterIds.forEach(id => {
            const el = document.getElementById(id);
            if(el) {
                el.addEventListener('input', () => loadActivities());
                el.addEventListener('change', () => loadActivities());
            }
        });
        // filtroAct ya tiene evento de cambio, pero también debe recargar
        filtroAct.addEventListener('change', () => loadActivities());
        document.getElementById('resetFilters').addEventListener('click', resetFilters);
    }
    
    function escapeHtml(str) {
        if(!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        });
    }
    
    // --- Inicialización ---
    function init() {
        initMonthSelect();
        attachFilterEvents();
        document.getElementById('weekdaysRow').innerHTML = ['LUN','MAR','MIÉ','JUE','VIE','SÁB','DOM'].map(d => `<div>${d}</div>`).join('');
        // Cargar actividades por defecto (todas)
        loadActividadesPorUnidad('');
        loadActivities();
    }
    // Obtener la tabla actual (puede cambiar su estructura, pero asumimos que es una tabla HTML)
function getCurrentTable() {
    // El contenido de #dynamicContent es actualizado por tu lógica
    // Buscamos la primera tabla dentro de ese contenedor
    const container = document.getElementById('dynamicContent');
    if (!container) return null;
    const table = container.querySelector('table');
    return table;
}

// Exportar a Excel (formato .xlsx)
function exportToExcel() {
    const table = getCurrentTable();
    if (!table) {
        alert('No hay datos para exportar.');
        return;
    }
    // Clonar la tabla para no afectar la vista
    const clone = table.cloneNode(true);
    // Eliminar posibles botones o elementos extra dentro de celdas
    clone.querySelectorAll('button, .btn-export').forEach(el => el.remove());
    
    // Usar SheetJS para convertir la tabla a libro de Excel
    const wb = XLSX.utils.table_to_book(clone, { sheet: "Actividades" });
    const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([wbout], { type: 'application/octet-stream' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'actividades_calendario.xlsx';
    link.click();
    URL.revokeObjectURL(link.href);
}

// Exportar a PDF (usando jsPDF + autoTable)
function exportToPDF() {
    const table = getCurrentTable();
    if (!table) {
        alert('No hay datos para exportar.');
        return;
    }
    // Clonar y limpiar
    const clone = table.cloneNode(true);
    clone.querySelectorAll('button, .btn-export').forEach(el => el.remove());
    
    // Extraer encabezados y filas
    const headers = [];
    const rows = [];
    const thead = clone.querySelector('thead');
    if (thead) {
        const headerCells = thead.querySelectorAll('th');
        headerCells.forEach(th => headers.push(th.textContent.trim()));
    } else {
        // Si no hay thead, intentar obtener de la primera fila
        const firstRow = clone.querySelector('tr');
        if (firstRow) {
            firstRow.querySelectorAll('td, th').forEach(td => headers.push(td.textContent.trim()));
            // Remover esa fila para no duplicarla
            firstRow.remove();
        }
    }
    // Obtener filas del cuerpo
    const tbody = clone.querySelector('tbody') || clone;
    tbody.querySelectorAll('tr').forEach(tr => {
        const rowData = [];
        tr.querySelectorAll('td').forEach(td => rowData.push(td.textContent.trim()));
        if (rowData.length) rows.push(rowData);
    });
    
    // Crear PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape', 'pt', 'a4');
    doc.text('Listado de Actividades', 40, 40);
    
    doc.autoTable({
        head: [headers],
        body: rows,
        startY: 60,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [128, 0, 0] }, // color granate
    });
    
    doc.save('actividades_calendario.pdf');
}

// Asignar eventos a los botones (después de que el DOM esté listo)
document.addEventListener('DOMContentLoaded', function() {
    const excelBtn = document.getElementById('exportExcelBtn');
    const pdfBtn = document.getElementById('exportPdfBtn');
    if (excelBtn) excelBtn.addEventListener('click', exportToExcel);
    if (pdfBtn) pdfBtn.addEventListener('click', exportToPDF);
});
    
    init();
