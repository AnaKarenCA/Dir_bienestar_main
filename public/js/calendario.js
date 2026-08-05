// ============================================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// ============================================================
const BASE_URL = '/Dir_bienestar';
const now = new Date();
let currentYear = now.getFullYear();
let currentMonth = now.getMonth();   // 0-index (Enero = 0)
let currentDateFilter = null;
let allActivities = [];
let allActividades = [];

// Elementos de filtros
const filtroUA = document.getElementById('filtroUA');
const filtroAct = document.getElementById('filtroAct');
const filtroResp = document.getElementById('filtroResp');
const filtroLugar = document.getElementById('filtroLugar');
const filtroDel = document.getElementById('filtroDel');
const filtroDom = document.getElementById('filtroDom');

// ============================================================
// CARGAR ACTIVIDADES POR UNIDAD (para el filtro dependiente)
// ============================================================
async function loadActividadesPorUnidad(unidadId) {
    if (!unidadId) {
        // Si no hay unidad seleccionada, mantenemos el select con las opciones
        // que ya vienen desde PHP (no hacemos fetch adicional)
        // Solo aseguramos que no se pierdan las opciones
        return;
    }
    try {
        const response = await fetch(`${BASE_URL}/dashboard/actividadesPorUnidad/${unidadId}`);
        if (!response.ok) throw new Error('Error al cargar actividades');
        const data = await response.json();
        populateActividadesSelect(data);
    } catch (error) {
        console.error('Error cargando actividades por unidad:', error);
        // No resetear el select para no perder las opciones existentes
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

// ============================================================
// EVENTOS DE FILTROS
// ============================================================
// Escuchar cambio de unidad para recargar actividades
filtroUA.addEventListener('change', function() {
    const unidadId = this.value;
    loadActividadesPorUnidad(unidadId);
    loadActivities(); // Recargar datos
});

// ============================================================
// OBTENER ACTIVIDADES FILTRADAS DESDE EL SERVIDOR
// ============================================================
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

// ============================================================
// RENDERIZAR TABLA
// ============================================================
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

// ============================================================
// RENDERIZAR CALENDARIO
// ============================================================
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

// ============================================================
// NAVEGACIÓN DEL CALENDARIO
// ============================================================
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
    if(isNaN(currentYear)) currentYear = new Date().getFullYear();
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
    document.getElementById('yearInput').value = currentYear;
    select.addEventListener('change', setYearMonth);
    document.getElementById('yearInput').addEventListener('change', setYearMonth);
    document.getElementById('prevMonthBtn').addEventListener('click', () => changeMonth(-1));
    document.getElementById('nextMonthBtn').addEventListener('click', () => changeMonth(1));
}

// ============================================================
// RESET DE FILTROS
// ============================================================
function resetFilters() {
    filtroResp.value = '';
    filtroUA.value = '';
    filtroLugar.value = '';
    filtroDel.value = '';
    filtroAct.value = '';
    filtroDom.value = '';
    currentDateFilter = null;
    // Restaurar actividades a todas (sin recargar desde endpoint)
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
    filtroAct.addEventListener('change', () => loadActivities());
    document.getElementById('resetFilters').addEventListener('click', resetFilters);
}

// ============================================================
// UTILIDADES
// ============================================================
function escapeHtml(str) {
    if(!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if(m === '&') return '&amp;';
        if(m === '<') return '&lt;';
        if(m === '>') return '&gt;';
        return m;
    });
}

// ============================================================
// EXPORTAR A EXCEL Y PDF
// ============================================================
function getCurrentTable() {
    const container = document.getElementById('dynamicContent');
    if (!container) return null;
    const table = container.querySelector('table');
    return table;
}

function exportToExcel() {
    const table = getCurrentTable();
    if (!table) {
        alert('No hay datos para exportar.');
        return;
    }
    const clone = table.cloneNode(true);
    clone.querySelectorAll('button, .btn-export').forEach(el => el.remove());
    
    const wb = XLSX.utils.table_to_book(clone, { sheet: "Actividades" });
    const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([wbout], { type: 'application/octet-stream' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'actividades_calendario.xlsx';
    link.click();
    URL.revokeObjectURL(link.href);
}

function exportToPDF() {
    const table = getCurrentTable();
    if (!table) {
        alert('No hay datos para exportar.');
        return;
    }
    const clone = table.cloneNode(true);
    clone.querySelectorAll('button, .btn-export').forEach(el => el.remove());
    
    const headers = [];
    const rows = [];
    const thead = clone.querySelector('thead');
    if (thead) {
        const headerCells = thead.querySelectorAll('th');
        headerCells.forEach(th => headers.push(th.textContent.trim()));
    } else {
        const firstRow = clone.querySelector('tr');
        if (firstRow) {
            firstRow.querySelectorAll('td, th').forEach(td => headers.push(td.textContent.trim()));
            firstRow.remove();
        }
    }
    const tbody = clone.querySelector('tbody') || clone;
    tbody.querySelectorAll('tr').forEach(tr => {
        const rowData = [];
        tr.querySelectorAll('td').forEach(td => rowData.push(td.textContent.trim()));
        if (rowData.length) rows.push(rowData);
    });
    
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape', 'pt', 'a4');
    doc.text('Listado de Actividades', 40, 40);
    
    doc.autoTable({
        head: [headers],
        body: rows,
        startY: 60,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [128, 0, 0] },
    });
    
    doc.save('actividades_calendario.pdf');
}

// ============================================================
// INICIALIZACIÓN
// ============================================================
function init() {
    initMonthSelect();
    attachFilterEvents();
    document.getElementById('weekdaysRow').innerHTML = ['LUN','MAR','MIÉ','JUE','VIE','SÁB','DOM'].map(d => `<div>${d}</div>`).join('');
    // Cargar actividades por defecto (todas)
    loadActivities();
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Asignar eventos a los botones de exportación
    const excelBtn = document.getElementById('exportExcelBtn');
    const pdfBtn = document.getElementById('exportPdfBtn');
    if (excelBtn) excelBtn.addEventListener('click', exportToExcel);
    if (pdfBtn) pdfBtn.addEventListener('click', exportToPDF);
    
    // Inicializar el calendario
    init();
});