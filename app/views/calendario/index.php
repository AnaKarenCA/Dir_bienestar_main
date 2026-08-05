<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>DG Bienestar · Calendario de Actividades</title>
    <link rel="stylesheet" href="/css/calendario.css">
    <!-- Para exportar a Excel (XLSX) -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <!-- Para exportar a PDF (jsPDF + autoTable) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <!-- ExcelJS para mejor formato -->
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
    <script src="/js/calendario.js" defer></script>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<div class="app-container">

    <!-- ===== HEADER ===== -->
    <div class="header-container">
        <div class="logo-area">
            <img src="/img/logo_d_bienestar.png" alt="DG Bienestar">
        </div>
        <div class="title-header">
            <h1>DIRECCIÓN GENERAL DE BIENESTAR</h1>
            <p>Calendario de Actividades · Planeación y seguimiento</p>
        </div>
    </div>

    <!-- ===== FILTROS ===== -->
    <div class="filters-card">
        <div class="filters-grid">
            <div class="filter-item">
                <label>Responsable</label>
                <input type="text" id="filtroResp" placeholder="Nombre">
            </div>
            <div class="filter-item">
                <label>Unidad Administrativa</label>
                <select id="filtroUA">
                    <option value="">Todas</option>
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-item">
                <label>Actividad</label>
                <select id="filtroAct">
                    <option value="">Todas</option>
                    <!-- Se llena dinámicamente con JavaScript -->
                </select>
            </div>
            <div class="filter-item">
                <label>Lugar</label>
                <select id="filtroLugar">
                    <option value="">Todos</option>
                    <?php foreach ($lugares as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-item">
                <label>Delegación</label>
                <select id="filtroDel">
                    <option value="">Todas</option>
                    <?php foreach ($delegaciones as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-item">
                <label>Domicilio</label>
                <input type="text" id="filtroDom" placeholder="Calle, número o CP">
            </div>
            <div class="filters-actions">
                <button class="reset-button" id="resetFilters">Limpiar filtros</button>
            </div>
        </div>
    </div>

    <!-- ===== CALENDARIO + TABLA ===== -->
    <div class="two-columns">
        <!-- Columna izquierda: Calendario -->
        <div class="calendar-col">
            <div class="calendar-nav">
                <div class="nav-buttons">
                    <button class="nav-btn" id="prevMonthBtn">◀</button>
                    <button class="nav-btn" id="nextMonthBtn">▶</button>
                </div>
                <div class="month-year-selector">
                    <select id="monthSelect"></select>
                    <input type="number" id="yearInput" min="2024" max="2030" step="1" value="<?= date('Y') ?>">
                </div>
                <div class="month-year-label" id="monthYearLabel"></div>
            </div>
            <div class="weekdays" id="weekdaysRow"></div>
            <div id="calendarGrid" class="calendar-grid"></div>
            <div class="resumen-box" id="resumenMensual"></div>
            <div style="font-size:0.65rem; margin-top:12px; text-align:center;">Haz clic en un día para filtrar rápidamente la tabla</div>
        </div>

        <!-- Columna derecha: Tabla de actividades -->
        <div class="right-col">
            <h3 style="color:#800000; margin-bottom: 12px; font-size: 1.2rem;">
                Registro detallado de actividades
                <span style="float:right; font-size:0.8rem;">
                    <button id="exportExcelBtn" class="btn-export" style="background:#1d6f42; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;">Excel</button>
                    <button id="exportPdfBtn" class="btn-export" style="background:#b22222; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;">PDF</button>
                </span>
            </h3>
            <div id="dynamicContent" class="data-table-wrapper"></div>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <footer>
        Sistema Oficial de Registro <br>
        Hecho por: <br>
        Carmona Aviles Ana Karen <br>
        Onofre Garcia Halem <br>
        Oscar Arturo Díaz Duran
    </footer>
</div>

</body>
</html>