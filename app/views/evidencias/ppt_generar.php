<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generando PowerPoint...</title>
    <script src="https://cdn.jsdelivr.net/npm/pptxgenjs@3.12.0/dist/pptxgen.bundle.js"></script>
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        .loader {
            font-size: 1.5rem;
            color: #27ae60;
            text-align: center;
            padding: 40px;
        }
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #27ae60;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader">
        
        <div class="spinner"></div>
        <p>Generando PowerPoint, por favor espere...</p>
    </div>

    <script>
        const datos = <?= json_encode($datos) ?>;

        async function generarPPT() {
            const PptxGenJS = window.PptxGenJS;
            const pres = new PptxGenJS();
            pres.layout = "LAYOUT_WIDE"; // 16:9

            // ========== SLIDE 1: PORTADA ==========
            let s1 = pres.addSlide();
            s1.addText(datos.direccion || "Dirección General de Bienestar", { x: 0.5, y: 2, fontSize: 28, bold: true, align: "center", w: 9 });
            s1.addText(datos.evento_nombre || "Nombre del evento", { x: 0.5, y: 3.2, fontSize: 24, align: "center", w: 9 });

            // ========== SLIDE 2: APROBACIÓN Y FIRMAS ==========
            let s2 = pres.addSlide();
            s2.addText("APROBACIÓN Y RESPONSABLES", { x: 0.5, y: 0.3, fontSize: 22, bold: true });
            s2.addText(`Aprobado por: ${datos.aprobado_por || ''}`, { x: 0.5, y: 1.2 });
            s2.addText(`Responsable por: ${datos.responsable_por || ''}`, { x: 0.5, y: 1.8 });
            s2.addText(`Fecha de entrega: ${datos.fecha_entrega || ''}`, { x: 0.5, y: 2.4 });
            s2.addText(`Realizó: ${datos.realizo || ''}`, { x: 0.5, y: 3.0 });
            s2.addText(`Firma: ${datos.firma_nombre || ''}`, { x: 0.5, y: 3.6 });
            s2.addText(`Fecha del evento: ${datos.fecha_evento || ''}`, { x: 0.5, y: 4.2 });

            // ========== SLIDE 3: OBJETIVO Y BENEFICIARIOS ==========
            let s3 = pres.addSlide();
            s3.addText("OBJETIVO Y BENEFICIARIOS", { x: 0.5, y: 0.3, fontSize: 22, bold: true });
            s3.addText(`Línea de acción PbRM: ${datos.linea_accion || 'No especificada'}`, { x: 0.5, y: 1.2 });
            s3.addText(`Objetivo del evento:\n${datos.objetivo_evento || ''}`, { x: 0.5, y: 1.8, fontSize: 14 });
            s3.addText(`Número de Beneficiarios: ${datos.num_beneficiarios || 0}`, { x: 0.5, y: 3.5 });

            // ========== SLIDE 4: JUSTIFICACIÓN ==========
            let s4 = pres.addSlide();
            s4.addText("JUSTIFICACIÓN", { x: 0.5, y: 0.3, fontSize: 22, bold: true });
            s4.addText(datos.justificacion || "Sin justificación", { x: 0.5, y: 1.2, fontSize: 14, w: 9 });

            // ========== SLIDE 5: GENERALES ==========
            let s5 = pres.addSlide();
            s5.addText("GENERALES DEL EVENTO", { x: 0.5, y: 0.3, fontSize: 22, bold: true });
            s5.addText(
                `Fecha: ${datos.gen_fecha || ''}\n` +
                `Hora: ${datos.gen_hora || ''}\n` +
                `Lugar: ${datos.gen_lugar || ''}\n` +
                `Vestimenta: ${datos.gen_vestimenta || ''}\n` +
                `Duración: ${datos.gen_duracion || ''}\n` +
                `Coordinación: ${datos.gen_coordinacion || ''}\n` +
                `Responsable: ${datos.gen_responsable || ''}`,
                { x: 0.5, y: 1.2, fontSize: 14 }
            );

            // ========== SLIDE 6: UBICACIÓN ==========
            let s6 = pres.addSlide();
            s6.addText("UBICACIÓN DEL EVENTO", { x: 0.5, y: 0.3, fontSize: 22, bold: true });
            s6.addText(`Dirección: ${datos.ubic_direccion || ''}`, { x: 0.5, y: 1.0 });
            s6.addText(`Link: ${datos.ubic_link || ''}`, { x: 0.5, y: 1.5 });
            // (Las imágenes no se incluyen desde la base, se omiten)

            // ========== SLIDE 7: ORDEN DEL DÍA ==========
            let s7 = pres.addSlide();
            s7.addText("ORDEN DEL DÍA - PROGRAMA PROTOCOLARIO", { x: 0.5, y: 0.3, fontSize: 18, bold: true });
            if (datos.agenda && datos.agenda.length) {
                const matrix = [["Hora", "Actividad", "Responsable", "Duración"]];
                datos.agenda.forEach(item => {
                    // Calculamos duración (hora_fin - hora_inicio)
                    let duracion = '';
                    if (item.hora_inicio && item.hora_fin) {
                        const start = new Date('1970-01-01T' + item.hora_inicio);
                        const end = new Date('1970-01-01T' + item.hora_fin);
                        const diff = (end - start) / 60000; // minutos
                        if (diff > 0) {
                            const h = Math.floor(diff / 60);
                            const m = diff % 60;
                            duracion = (h > 0 ? h + 'h ' : '') + (m > 0 ? m + 'min' : '');
                        }
                    }
                    matrix.push([
                        item.hora_inicio ? item.hora_inicio.substring(0,5) : '',
                        item.nombre_actividad || '',
                        item.responsable_id ? 'Responsable' : '', // no tenemos nombre responsable
                        duracion
                    ]);
                });
                s7.addTable(matrix, { x: 0.5, y: 1.0, w: 9, colW: [1.5, 3.5, 2.5, 1.5], fontSize: 10 });
            } else {
                s7.addText("No se registraron actividades", { x: 0.5, y: 1.5 });
            }

            // ========== SLIDE 8: EVENTO PROTOCOLARIO Y CIERRE ==========
            let s8 = pres.addSlide();
            s8.addText("EVENTO PROTOCOLARIO Y CIERRE", { x: 0.5, y: 0.3, fontSize: 22, bold: true });
            s8.addText(`Programa protocolario:\n${datos.evento_protocolario || ''}`, { x: 0.5, y: 1.2, fontSize: 14 });
            s8.addText(`Fin del evento - Duración total: ${datos.duracion_total_evento || ''}`, { x: 0.5, y: 3.0, fontSize: 14, bold: true });

            // ========== SLIDE 9: PRESIDIUM ==========
            let s9 = pres.addSlide();
            s9.addText("PRESIDIUM", { x: 0.5, y: 0.3, fontSize: 22, bold: true });
            if (datos.presidium && datos.presidium.length) {
                let texto = '';
                datos.presidium.forEach((p, i) => {
                    texto += `${i+1}. ${p.nombre_invitado} - ${p.cargo_invitado}\n`;
                });
                s9.addText(texto, { x: 0.5, y: 1.2, fontSize: 14 });
            } else {
                s9.addText("No se especificó presidium", { x: 0.5, y: 1.2 });
            }

            // ========== SLIDE 10: CROQUIS ==========
            let s10 = pres.addSlide();
            s10.addText("CROQUIS DEL EVENTO", { x: 0.5, y: 0.3, fontSize: 22, bold: true });
            s10.addText(`Pantalla / Nota: ${datos.croquis_pantalla || ''}`, { x: 0.5, y: 5.2, fontSize: 12 });

            // ========== SLIDE 11: INVITADOS ESPECIALES ==========
            let s11 = pres.addSlide();
            s11.addText("INVITADOS ESPECIALES", { x: 0.5, y: 0.3, fontSize: 20, bold: true });
            if (datos.invitados && datos.invitados.length) {
                let texto = '';
                datos.invitados.forEach((inv, i) => {
                    texto += `${i+1}. ${inv.nombre} - ${inv.cargo}\n`;
                });
                s11.addText(texto, { x: 0.5, y: 1.2, fontSize: 14 });
            } else {
                s11.addText("No hay invitados especiales", { x: 0.5, y: 1.2 });
            }

            // ========== SLIDE 12: MÓDULOS JORNADA INTEGRAL ==========
            let s12 = pres.addSlide();
            s12.addText("MÓDULOS JORNADA INTEGRAL", { x: 0.5, y: 0.3, fontSize: 20, bold: true });
            if (datos.modulos && datos.modulos.length) {
                const matrix = [["N°", "Institución", "Servicio"]];
                datos.modulos.forEach((m, i) => {
                    matrix.push([(i+1).toString(), m.nombre_institucion, m.servicio]);
                });
                s12.addTable(matrix, { x: 0.5, y: 1.0, w: 9, colW: [1, 4, 4], fontSize: 10 });
            } else {
                s12.addText("No hay módulos registrados", { x: 0.5, y: 1.5 });
            }

            // ========== SLIDE 13: REQUERIMIENTOS - DELEGACIÓN ADMINISTRATIVA ==========
            let s13 = pres.addSlide();
            s13.addText("REQUERIMIENTOS - DELEGACIÓN ADMINISTRATIVA", { x: 0.5, y: 0.3, fontSize: 18, bold: true });
            if (datos.req_delegacion && datos.req_delegacion.length) {
                let texto = '';
                datos.req_delegacion.forEach(r => {
                    texto += `• ${r.nombre_insumo} (${r.cantidad} ${r.unidad || ''})\n`;
                });
                s13.addText(texto, { x: 0.5, y: 1.2, fontSize: 14 });
            } else {
                s13.addText("Sin requerimientos", { x: 0.5, y: 1.2 });
            }

            // ========== SLIDE 14: REQUERIMIENTOS - COMUNICACIÓN SOCIAL ==========
            let s14 = pres.addSlide();
            s14.addText("REQUERIMIENTOS - COORD. COMUNICACIÓN SOCIAL", { x: 0.5, y: 0.3, fontSize: 18, bold: true });
            if (datos.req_comunicacion && datos.req_comunicacion.length) {
                let texto = '';
                datos.req_comunicacion.forEach(r => {
                    texto += `• ${r.nombre_insumo} (${r.cantidad} ${r.unidad || ''})\n`;
                });
                s14.addText(texto, { x: 0.5, y: 1.2, fontSize: 14 });
            } else {
                s14.addText("Sin requerimientos", { x: 0.5, y: 1.2 });
            }

            // ========== SLIDE 15: REQUERIMIENTOS - DIRECCIÓN GRAL. ADMINISTRACIÓN ==========
            let s15 = pres.addSlide();
            s15.addText("REQUERIMIENTOS - DIRECCIÓN GRAL. DE ADMINISTRACIÓN", { x: 0.5, y: 0.3, fontSize: 18, bold: true });
            if (datos.req_administracion && datos.req_administracion.length) {
                let texto = '';
                datos.req_administracion.forEach(r => {
                    texto += `• ${r.nombre_insumo} (${r.cantidad} ${r.unidad || ''})\n`;
                });
                s15.addText(texto, { x: 0.5, y: 1.2, fontSize: 14 });
            } else {
                s15.addText("Sin requerimientos", { x: 0.5, y: 1.2 });
            }

            // ========== SLIDE 16: REQUERIMIENTOS FINALES + FIRMAS ==========
            let s16 = pres.addSlide();
            s16.addText("REQUERIMIENTOS ADICIONALES Y FIRMAS", { x: 0.5, y: 0.3, fontSize: 20, bold: true });
            s16.addText(`Eventos y Ubicación:\nDía: ${datos.evento_dia || ''} | Horario: ${datos.evento_horario || ''}\nUbicación: ${datos.evento_ubicacion || ''}`, { x: 0.5, y: 1.5, fontSize: 12 });
            s16.addText(`Firma 1: ${datos.firma1 || ''}\nFirma 2: ${datos.firma2 || ''}`, { x: 0.5, y: 3.5, fontSize: 14, bold: true });

            // Descargar
            await pres.writeFile({ fileName: `Presentacion_${datos.evento_nombre.replace(/\s/g, "_") || "evento"}.pptx` });
            alert("¡PowerPoint generado con éxito! Revisa la descarga.");
            window.location.href = "/Dir_bienestar/evento_ppt";
        }

        generarPPT();
    </script>
</body>
</html>