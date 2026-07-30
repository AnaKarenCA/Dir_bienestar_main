<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Evidencia | DG Bienestar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        :root {
            --principal: #800000;
            --principal-hover: #660000;
            --verde: #2e7d32;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        body { background:#f4f6f8; color:#333; padding:24px 28px; }
        .container { max-width:1200px; margin:0 auto; }
        .header { display:flex; align-items:center; gap:24px; flex-wrap:wrap; margin-bottom:28px; border-bottom:2px solid #E6D8C8; padding-bottom:16px; }
        .title-area h1 { font-size:1.8rem; color:var(--principal); font-weight:800; }
        .title-area p { color:#7A5A3A; font-weight:500; }

        .form-box { background:white; border-radius:28px; padding:30px; box-shadow:0 6px 14px rgba(0,0,0,0.04); }
        .form-box h2 { color:var(--principal); margin-bottom:20px; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; font-weight:700; color:var(--principal); font-size:0.9rem; margin-bottom:6px; }
        .form-group input[type="file"], .form-group textarea { width:100%; padding:10px; border:1px solid #ddd; border-radius:12px; font-size:0.9rem; }
        .form-group textarea { resize:vertical; min-height:80px; }

        .preview { width:100%; max-height:300px; border:2px dashed #bbb; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#888; overflow:hidden; margin-bottom:12px; background:#fafafa; }
        .preview img { width:100%; height:100%; object-fit:contain; }
        .preview .placeholder { padding:40px; text-align:center; }

        .btn { background:var(--principal); color:white; border:none; padding:10px 24px; border-radius:40px; font-weight:700; cursor:pointer; transition:0.15s; font-size:0.9rem; }
        .btn:hover { background:var(--principal-hover); }
        .btn-success { background:var(--verde); }
        .btn-success:hover { background:#1b5e20; }
        .btn-secondary { background:#e0e0e0; color:#333; }
        .btn-secondary:hover { background:#d0d0d0; }
        .btn:disabled { background:#bdbdbd; cursor:not-allowed; }

        .mapa { width:100%; height:300px; border-radius:12px; border:1px solid #ddd; margin-top:10px; }

        .ubicacion-info { background:#f5f5f5; padding:12px; border-radius:12px; font-size:0.85rem; margin:6px 0; }
        .ubicacion-info span { display:inline-block; margin-right:16px; }

        .acciones { display:flex; gap:12px; margin-top:20px; flex-wrap:wrap; }

        .volver { display:inline-block; margin-bottom:20px; color:var(--principal); text-decoration:none; font-weight:600; }
        .volver:hover { text-decoration:underline; }

        .mensaje { padding:12px 20px; border-radius:12px; margin-top:16px; display:none; }
        .mensaje.exito { background:#d4edda; color:#155724; display:block; }
        .mensaje.error { background:#f8d7da; color:#721c24; display:block; }
    </style>
</head>
<body>
<?php include_once APPROOT . '/views/partials/menu.php'; ?>

<div class="container">
    <div class="header">
        <div class="title-area">
            <h1>Registrar Evidencia</h1>
            <p><?= htmlspecialchars($tipoNombre) ?> - <?= htmlspecialchars($actividad['actividad_desc'] ?? '') ?></p>
        </div>
    </div>

    <a href="/Dir_bienestar/evidencias/detalle/<?= $registroId ?>" class="volver">← Volver al detalle</a>

    <div class="form-box">
        <h2> <?= htmlspecialchars($tipoNombre) ?></h2>

        <form id="formEvidencia" enctype="multipart/form-data">
            <input type="hidden" name="registro_id" value="<?= $registroId ?>">
            <input type="hidden" name="tipo" value="<?= $tipo ?>">
            <input type="hidden" name="latitud" id="latitud" value="<?= $evidencia['latitud'] ?? '' ?>">
            <input type="hidden" name="longitud" id="longitud" value="<?= $evidencia['longitud'] ?? '' ?>">
            <input type="hidden" name="precision" id="precision" value="<?= $evidencia['precision_geolocalizacion'] ?? '' ?>">

            <!-- Fotografía -->
            <div class="form-group">
                <label>Fotografía</label>
                <div class="preview" id="previewContainer">
                    <?php if (!empty($evidencia['fotografia'])): ?>
                        <img src="/<?= htmlspecialchars($evidencia['fotografia']) ?>" alt="Evidencia">
                    <?php else: ?>
                        <div class="placeholder">Selecciona una imagen o toma una foto</div>
                    <?php endif; ?>
                </div>
                <input type="file" name="fotografia" id="fotografia" accept="image/*" capture="environment">
                <div style="margin-top:8px;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('fotografia').click();">
                        <span class="material-symbols-outlined" style="font-size:1.2rem;vertical-align:middle;">image</span> Seleccionar imagen
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="tomarFoto()">
                        <span class="material-symbols-outlined" style="font-size:1.2rem;vertical-align:middle;">photo_camera</span> Tomar foto
                    </button>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="form-group">
                <label>Ubicación</label>
                <button type="button" class="btn btn-secondary" onclick="obtenerUbicacion()">
                    <span class="material-symbols-outlined" style="font-size:1.2rem;vertical-align:middle;">gps_fixed</span> Obtener ubicación
                </button>
                <div id="ubicacionInfo" class="ubicacion-info">
                    <span>Latitud: <strong id="mostrarLatitud"><?= $evidencia['latitud'] ?? 'No obtenida' ?></strong></span>
                    <span>Longitud: <strong id="mostrarLongitud"><?= $evidencia['longitud'] ?? 'No obtenida' ?></strong></span>
                    <span>Precisión: <strong id="mostrarPrecision"><?= $evidencia['precision_geolocalizacion'] ?? 'N/A' ?> m</strong></span>
                </div>
                <div id="mapaContainer" class="mapa"></div>
            </div>

            <!-- Fecha y hora automáticas -->
            <div class="form-group">
                <label>Fecha y hora</label>
                <p><strong><?= date('d/m/Y H:i:s') ?></strong></p>
            </div>

            <!-- Comentarios -->
            <div class="form-group">
                <label>Comentarios (opcional)</label>
                <textarea name="comentarios" rows="4" placeholder="Describe lo que sucedió..."><?= htmlspecialchars($evidencia['comentarios'] ?? '') ?></textarea>
            </div>

            <div id="mensaje" class="mensaje"></div>

            <div class="acciones">
                <button type="submit" class="btn btn-success">
                    <span class="material-symbols-outlined" style="font-size:1.2rem;vertical-align:middle;">save</span> Guardar evidencia
                </button>
                <a href="/Dir_bienestar/evidencias/detalle/<?= $registroId ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
// ============================================================
// VISTA PREVIA DE IMAGEN
// ============================================================
document.getElementById('fotografia').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const container = document.getElementById('previewContainer');
            container.innerHTML = `<img src="${event.target.result}" alt="Vista previa">`;
        }
        reader.readAsDataURL(file);
    }
});

// ============================================================
// TOMAR FOTO CON CÁMARA (solo funciona en HTTPS y dispositivos móviles)
// ============================================================
function tomarFoto() {
    const input = document.getElementById('fotografia');
    input.click();
}

// ============================================================
// OBTENER UBICACIÓN GPS (CORREGIDO)
// ============================================================
let mapa = null;
let marcador = null;

function obtenerUbicacion() {
    if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización.');
        return;
    }

    // En lugar de reemplazar todo el contenido, solo mostramos estado "cargando" en los elementos existentes
    const latEl = document.getElementById('mostrarLatitud');
    const lonEl = document.getElementById('mostrarLongitud');
    const precEl = document.getElementById('mostrarPrecision');

    if (!latEl || !lonEl || !precEl) {
        console.error('Faltan elementos de ubicación en el DOM');
        return;
    }

    latEl.textContent = 'Obteniendo...';
    lonEl.textContent = 'Obteniendo...';
    precEl.textContent = 'Obteniendo...';

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            const lat = pos.coords.latitude;
            const lon = pos.coords.longitude;
            const precision = pos.coords.accuracy;

            // Guardar en campos ocultos
            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lon;
            document.getElementById('precision').value = precision;

            // Actualizar los textos de los <strong> (no se destruyen)
            latEl.textContent = lat.toFixed(6);
            lonEl.textContent = lon.toFixed(6);
            precEl.textContent = precision.toFixed(1) + ' m';

            mostrarMapa(lat, lon);
        },
        function(err) {
            // Si falla, mostramos el error sin romper la estructura (usamos innerHTML esta vez para dar claridad)
            document.getElementById('ubicacionInfo').innerHTML = `
                <span style="color:red;">❌ Error: ${err.message}</span>
            `;
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }
    );
}

// ============================================================
// MOSTRAR MAPA CON LEAFLET
// ============================================================
function mostrarMapa(lat, lon) {
    const container = document.getElementById('mapaContainer');
    container.innerHTML = '';

    if (mapa) {
        mapa.remove();
        mapa = null;
        marcador = null;
    }

    mapa = L.map(container).setView([lat, lon], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(mapa);

    marcador = L.marker([lat, lon]).addTo(mapa);
    marcador.bindPopup('Ubicación registrada').openPopup();
}

// Si ya hay coordenadas guardadas, mostrar mapa al cargar
window.addEventListener('load', function() {
    const lat = document.getElementById('latitud').value;
    const lon = document.getElementById('longitud').value;
    if (lat && lon) {
        mostrarMapa(parseFloat(lat), parseFloat(lon));
    }
});

// ============================================================
// ENVÍO DEL FORMULARIO (AJAX) – MEJORADO CON DEPURACIÓN
// ============================================================
document.getElementById('formEvidencia').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const mensaje = document.getElementById('mensaje');
    mensaje.className = 'mensaje';
    mensaje.style.display = 'none';

    // Validar que haya imagen (excepto si ya existe)
    const archivo = formData.get('fotografia');
    const tieneExistente = <?= !empty($evidencia['fotografia']) ? 'true' : 'false' ?>;
    if ((!archivo || archivo.size === 0) && !tieneExistente) {
        mensaje.className = 'mensaje error';
        mensaje.textContent = '❌ Debes seleccionar una fotografía.';
        mensaje.style.display = 'block';
        return;
    }

    try {
        const response = await fetch('/Dir_bienestar/evidencias/guardar', {
            method: 'POST',
            body: formData
        });

        // Intentar parsear JSON, con manejo de error específico
        let result;
        try {
            result = await response.json();
        } catch (jsonError) {
            console.error('Respuesta no es JSON:', response);
            throw new Error('El servidor devolvió una respuesta inesperada.');
        }

        if (result.success) {
            mensaje.className = 'mensaje exito';
            mensaje.textContent = '✅ ' + result.mensaje;
            mensaje.style.display = 'block';
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1500);
        } else {
            mensaje.className = 'mensaje error';
            mensaje.textContent = '❌ ' + (result.error || 'Error al guardar');
            mensaje.style.display = 'block';
        }
    } catch (error) {
        console.error('Error en fetch:', error);
        mensaje.className = 'mensaje error';
        mensaje.textContent = '❌ Error de conexión. Intenta de nuevo. (' + error.message + ')';
        mensaje.style.display = 'block';
    }
});
</script>
</body>
</html>