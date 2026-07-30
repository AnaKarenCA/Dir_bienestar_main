<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Actividades | DG Bienestar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #FEF7F0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 24px 28px;
            color: #2C241A;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 28px;
            border-bottom: 2px solid #E6D8C8;
            padding-bottom: 16px;
        }
        .logo-area img {
            height: 70px;
            width: auto;
        }
        .title-area h1 {
            font-size: 1.8rem;
            color: #800000;
            font-weight: 800;
            letter-spacing: -0.3px;
        }
        .title-area p {
            color: #7A5A3A;
            font-weight: 500;
        }

        .filters-card {
            background: white;
            border-radius: 28px;
            padding: 20px 28px;
            margin-bottom: 28px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.04);
            border: 1px solid #EFE4D6;
        }
        /* Pestañas de tipo de período */
        .period-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 20px;
        }
        .period-tab {
            padding: 8px 20px;
            border-radius: 30px;
            background: #f0e8dc;
            color: #5E3E22;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            border: none;
            transition: 0.15s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .period-tab:hover {
            background: #e0d4c2;
        }
        .period-tab.active {
            background: #800000;
            color: white;
            box-shadow: 0 2px 8px rgba(128,0,0,0.3);
        }

        .filters-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 24px;
            align-items: flex-end;
            margin-top: 6px;
        }
        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 160px;
        }
        .filter-item label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #800000;
            letter-spacing: 0.3px;
        }
        .filter-item select,
        .filter-item input {
            padding: 8px 12px;
            border-radius: 40px;
            border: 1px solid #DBCAB2;
            background: #FFFDF9;
            font-size: 0.8rem;
            outline: none;
            width: 100%;
        }
        .filter-item select:focus,
        .filter-item input:focus {
            border-color: #800000;
        }
        .filters-actions {
            display: flex;
            justify-content: flex-end;
            flex: 1;
            margin-top: 8px;
        }
        .reset-button {
            background: #E7DAC8;
            border: none;
            padding: 8px 24px;
            border-radius: 40px;
            font-weight: 600;
            color: #5E3E22;
            cursor: pointer;
            transition: 0.1s;
        }
        .reset-button:hover {
            background: #D4C3AB;
        }

        .result-card {
            background: white;
            border-radius: 32px;
            padding: 20px;
            box-shadow: 0 12px 24px -10px rgba(0,0,0,0.08);
            overflow-x: auto;
        }
        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .result-header h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #800000;
        }
        .export-buttons {
            display: flex;
            gap: 10px;
        }
        .export-btn {
            background: #E7DAC8;
            border: none;
            padding: 6px 16px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            color: #5E3E22;
            transition: 0.1s;
        }
        .export-btn:hover {
            background: #D4C3AB;
        }
        .export-btn.excel {
            background: #1e7e34;
            color: white;
        }
        .export-btn.excel:hover {
            background: #146b2a;
        }
        .export-btn.pdf {
            background: #c62828;
            color: white;
        }
        .export-btn.pdf:hover {
            background: #b71c1c;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            min-width: 600px;
        }
        .summary-table th {
            background: #800000;
            color: white;
            padding: 12px 8px;
            font-weight: 600;
            text-align: center;
        }
        .summary-table td {
            border-bottom: 1px solid #EDE0D2;
            padding: 10px 8px;
            text-align: center;
        }
        .summary-table tr:hover {
            background: #FCF5EA;
        }
        .avance-bar {
            background: #E9DDCF;
            border-radius: 20px;
            height: 8px;
            width: 100%;
            overflow: hidden;
            min-width: 80px;
        }
        .avance-fill {
            background: #B9975B;
            height: 8px;
            border-radius: 20px;
            transition: width 0.3s;
        }
        .avance-text {
            font-weight: 600;
            margin-left: 8px;
            font-size: 0.75rem;
        }
        .empty-placeholder {
            text-align: center;
            padding: 48px;
            color: #AB8E66;
            font-size: 0.9rem;
        }
        footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.7rem;
            color: #B28B60;
        }
        @media (max-width: 850px) {
            .filters-grid {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-item {
                min-width: 100%;
            }
        }
        @media (max-width: 550px) {
            body {
                padding: 12px;
            }
            .period-tab {
                font-size: 0.7rem;
                padding: 6px 12px;
            }
        }
    </style>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<div class="container">
    <div class="header">
        <div class="logo-area">
            <img src="/img/logo_d_bienestar.png" alt="DG Bienestar" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 70%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%23800000%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 fill=%22white%22 text-anchor=%22middle%22%3EDGB%3C/text%3E%3C/svg%3E'">
        </div>
        <div class="title-area">
            <h1>DIRECCIÓN GENERAL DE BIENESTAR</h1>
            <p>Reportes de Actividades · Seguimiento de metas</p>
        </div>
    </div>

    <div class="filters-card">
        <!-- Pestañas de tipo de período -->
        <div class="period-tabs" id="periodTabs">
            <button class="period-tab active" data-tipo="mensual">Mensual</button>
            <button class="period-tab" data-tipo="trimestral">Trimestral</button>
            <button class="period-tab" data-tipo="semestral">Semestral</button>
            <button class="period-tab" data-tipo="anual">Anual</button>
        </div>

        <div class="filters-grid">
            <div class="filter-item" id="periodoValorContainer">
                <label id="periodoValorLabel">Mes:</label>
                <select id="periodoValor"></select>
            </div>
            <div class="filter-item">
                <label>Año</label>
                <input type="number" id="anio" min="2024" max="2030" value="<?= date('Y') ?>">
            </div>
            <div class="filter-item">
                <label>Unidad Administrativa</label>
                <select id="unidadId">
                    <option value="">Seleccione...</option>
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filters-actions">
                <button class="reset-button" id="resetFilters">Limpiar filtros</button>
            </div>
        </div>
    </div>

    <div class="result-card">
        <div class="result-header">
            <h3>Resumen de metas</h3>
            <div class="export-buttons">
                <button class="export-btn excel" id="exportExcel">Excel</button>
                <button class="export-btn pdf" id="exportPDF">PDF</button>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="summary-table" id="summaryTable">
                <thead>
                    <tr><th>Actividad</th><th>Meta</th><th>Registrado</th><th>Diferencia</th><th>Avance</th></tr>
                </thead>
                <tbody id="summaryBody">
                    <tr><td colspan="5" class="empty-placeholder">Seleccione una unidad administrativa para ver los datos.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
        <footer>Sistema oficial de registro | Datos protegidos: Hecho por: <br> Carmona Aviles Ana Karen <br> Onofre Garcia Halem <br> Oscar Arturo Díaz Duran</footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
    const BASE_URL = '/Dir_bienestar';

    const periodTabs = document.querySelectorAll('.period-tab');
    const periodoValor = document.getElementById('periodoValor');
    const periodoValorLabel = document.getElementById('periodoValorLabel');
    const anioInput = document.getElementById('anio');
    const unidadSelect = document.getElementById('unidadId');
    const resetBtn = document.getElementById('resetFilters');
    const tbody = document.getElementById('summaryBody');

    let currentTipo = 'mensual';

    // Cambiar pestaña
    periodTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            periodTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentTipo = this.dataset.tipo;
            actualizarPeriodoValor();
            cargarDatos();
        });
    });

    // Poblar select de período según tipo
    function actualizarPeriodoValor() {
        const tipo = currentTipo;
        let valores = [];
        let label = '';

        if (tipo === 'mensual') {
            label = 'Mes:';
            const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            for (let i = 0; i < 12; i++) {
                valores.push({ value: i+1, label: meses[i] });
            }
        } else if (tipo === 'trimestral') {
            label = 'Trimestre:';
            for (let i = 1; i <= 4; i++) {
                valores.push({ value: i, label: `Trimestre ${i}` });
            }
        } else if (tipo === 'semestral') {
            label = 'Semestre:';
            for (let i = 1; i <= 2; i++) {
                valores.push({ value: i, label: `Semestre ${i}` });
            }
        } else { // anual
            label = '';
            valores.push({ value: 1, label: 'Año completo' });
        }

        periodoValorLabel.textContent = label;
        periodoValor.innerHTML = valores.map(v => `<option value="${v.value}">${v.label}</option>`).join('');
        // Mantener selección si es posible
        if (periodoValor.options.length > 0) {
            periodoValor.selectedIndex = 0;
        }
    }

    // Cargar datos del reporte vía AJAX
    async function cargarDatos() {
        const unidadId = unidadSelect.value;
        if (!unidadId) {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-placeholder">Seleccione una unidad administrativa para ver los datos.</td></tr>';
            return;
        }

        const params = new URLSearchParams({
            anio: anioInput.value,
            periodo_tipo: currentTipo,
            periodo_valor: periodoValor.value,
            unidad_id: unidadId
        });

        try {
            const response = await fetch(`${BASE_URL}/reporte/data?${params.toString()}`);
            const data = await response.json();
            renderTable(data);
        } catch (error) {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="5" class="empty-placeholder">Error al cargar datos.</td></tr>';
        }
    }

    // Renderizar tabla
    function renderTable(data) {
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-placeholder">No hay datos para los filtros seleccionados.</td></tr>';
            return;
        }

        let html = '';
        data.forEach(item => {
            const avance = item.avance;
            const color = avance >= 100 ? '#2B7A4B' : (avance > 0 ? '#B9975B' : '#D98C2B');
            const diff = item.diferencia;
            const diffColor = diff < 0 ? '#B3422E' : (diff > 0 ? '#2B7A4B' : '#888');
            html += `
                <tr>
                    <td style="text-align:left; font-weight:500;">${escapeHtml(item.actividad)}</td>
                    <td>${item.meta}</td>
                    <td>${item.registrado}</td>
                    <td style="color:${diffColor};">${diff}</td>
                    <td style="min-width:120px;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div class="avance-bar">
                                <div class="avance-fill" style="width:${Math.min(avance,100)}%; background:${color};"></div>
                            </div>
                            <span class="avance-text">${avance}%</span>
                        </div>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    // Limpiar filtros
    function resetFilters() {
        unidadSelect.value = '';
        anioInput.value = new Date().getFullYear();
        // Resetear pestaña a Mensual
        periodTabs.forEach(t => t.classList.remove('active'));
        document.querySelector('.period-tab[data-tipo="mensual"]').classList.add('active');
        currentTipo = 'mensual';
        actualizarPeriodoValor();
        cargarDatos();
    }

    // Eventos
    anioInput.addEventListener('change', cargarDatos);
    periodoValor.addEventListener('change', cargarDatos);
    unidadSelect.addEventListener('change', cargarDatos);
    resetBtn.addEventListener('click', resetFilters);

    // Exportar a Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        const tabla = document.getElementById('summaryTable');
        const wb = XLSX.utils.table_to_book(tabla, { sheet: "Reporte" });
        XLSX.writeFile(wb, `Reporte_${new Date().toISOString().slice(0,10)}.xlsx`);
    });

    // Exportar a PDF
    document.getElementById('exportPDF').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape', 'mm', 'a4');
        doc.autoTable({ html: '#summaryTable', theme: 'striped' });
        doc.save(`Reporte_${new Date().toISOString().slice(0,10)}.pdf`);
    });

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Inicializar
    actualizarPeriodoValor();
</script>
</body>
</html>