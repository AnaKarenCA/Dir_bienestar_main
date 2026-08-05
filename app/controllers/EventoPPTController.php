<?php

class EventoPPTController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }
    }

    public function login()
    {
        header('Location: /Dir_bienestar/evento_ppt/index');
        exit;
    }

    public function index()
    {
        $registroModel = $this->model('RegistroActividad');
        $registros = $registroModel->obtenerRegistrosConCarpeta();
        $this->view('evidencias/ppt_selector', ['registros' => $registros]);
    }

    public function generar()
    {
        try {
            $idRegistro = $_GET['id_registro'] ?? $_POST['id_registro'] ?? 0;
            if (!$idRegistro) {
                throw new Exception('No se seleccionó registro.');
            }

            // --- Obtener todos los datos ---
            $registroModel = $this->model('RegistroActividad');
            $registro = $registroModel->obtenerRegistroCompletoPorId($idRegistro);
            if (!$registro) {
                throw new Exception('Registro no encontrado.');
            }

            $carpetaModel = $this->model('Carpeta');
            $carpeta = $carpetaModel->obtenerPorRegistroActividadId($idRegistro);

            $evento = null;
            $eventoId = 0;
            if ($carpeta && !empty($carpeta['id'])) {
                $eventoModel = $this->model('EventoDetalle');
                $evento = $eventoModel->obtenerPorCarpetaIdCompleto($carpeta['id']);
                if ($evento) {
                    $eventoId = $evento['id'];
                }
            }

            $ordenModel = $this->model('OrdenDelDia');
            $ordenes = $ordenModel->obtenerPorEventoDetalleId($eventoId);

            $presidiumModel = $this->model('PresidiumAsistente');
            $presidium = $presidiumModel->obtenerPorEventoDetalleId($eventoId);

            // Obtener tipo de presídium
            $tipoPresidium = 'lineal';
            if (!empty($presidium)) {
                $tipoId = $presidium[0]['tipo_presidium_id'] ?? null;
                if ($tipoId) {
                    $database = new Database();
                    $db = $database->getConnection();
                    $stmt = $db->prepare("SELECT nombre_tipo FROM tipo_presidium WHERE id = ?");
                    $stmt->execute([$tipoId]);
                    $row = $stmt->fetch();
                    if ($row) {
                        $tipoPresidium = $row['nombre_tipo'];
                    }
                }
            }

            // Decodificar JSON de invitados, módulos y requerimientos
            $invitados = [];
            $modulos = [];
            $reqInternos = [];
            $reqExternos = [];
            if ($evento) {
                $invitados = json_decode($evento['invitados_especiales'] ?? '[]', true);
                $modulos = json_decode($evento['modulos_jornada'] ?? '[]', true);
                $reqInternos = json_decode($evento['requerimientos_internos'] ?? '[]', true);
                $reqExternos = json_decode($evento['requerimientos_externos'] ?? '[]', true);
                // Asegurar que sean arrays
                if (!is_array($invitados)) $invitados = [];
                if (!is_array($modulos)) $modulos = [];
                if (!is_array($reqInternos)) $reqInternos = [];
                if (!is_array($reqExternos)) $reqExternos = [];
            }

            // Calcular duración
            $duracionTotalMinutos = 0;
            if (!empty($registro['hora_inicio']) && !empty($registro['hora_fin'])) {
                $inicio = strtotime($registro['hora_inicio']);
                $fin = strtotime($registro['hora_fin']);
                if ($inicio && $fin && $fin > $inicio) {
                    $duracionTotalMinutos = ($fin - $inicio) / 60;
                }
            }
            $horas = floor($duracionTotalMinutos / 60);
            $minutos = $duracionTotalMinutos % 60;
            $duracionTotalStr = trim(($horas > 0 ? $horas . ' h ' : '') . ($minutos > 0 ? $minutos . ' min' : ''));

            $datosPPT = [
                'direccion'             => $registro['unidad_nombre'] ?? 'Dirección General de Bienestar',
                'evento_nombre'         => $evento['nombre_evento'] ?? $registro['actividad_desc'] ?? 'Evento',
                'aprobado_por'          => $evento['aprobado_por'] ?? 'MTRA. ANDREA MA. DEL ROCÍO MERLOS NÁJERA',
                'responsable_por'       => $evento['responsable_evento'] ?? (($registro['usuario_nombre'] ?? '') . ' - ' . ($registro['usuario_puesto'] ?? '')),
                'fecha_entrega'         => $carpeta['fecha_entrega'] ?? date('Y-m-d'),
                'realizo'               => $registro['usuario_nombre'] ?? '',
                'fecha_evento'          => $evento['fecha_evento'] ?? $registro['fecha_inicio'] ?? date('Y-m-d'),
                'linea_accion'          => $evento['descripcion_meta'] ?? $registro['actividad_desc'] ?? '',
                'objetivo_evento'       => $evento['objetivo'] ?? $registro['unidad_objetivo'] ?? '',
                'num_beneficiarios'     => $registro['beneficiarios_asistentes'] ?? 0,
                'justificacion'         => $evento['justificacion'] ?? '',
                'gen_fecha'             => $registro['fecha_inicio'],
                'gen_hora'              => $registro['hora_inicio'],
                'gen_lugar'             => $registro['lugar_nombre'] ?? '',
                'gen_vestimenta'        => $evento['vestimenta'] ?? '',
                'gen_duracion'          => $duracionTotalStr,
                'gen_coordinacion'      => $evento['coordinacion_evento'] ?? '',
                'gen_responsable'       => ($registro['usuario_nombre'] ?? '') . ' - ' . ($registro['unidad_nombre'] ?? ''),
                'ubic_direccion'        => $carpeta['direccion_entrega'] ?? '',
                'ubic_link'             => $evento['link_mapa'] ?? $carpeta['link_mapa'] ?? '',
                'imagen_lugar'          => $evento['imagen_lugar'] ?? null,
                'imagen_maps'           => $evento['imagen_maps'] ?? null,
                'agenda'                => $ordenes,
                'presidium'             => $presidium,
                'tipo_presidium'        => $tipoPresidium,
                'invitados'             => $invitados,
                'modulos'               => $modulos,
                'req_internos'          => $reqInternos,
                'req_externos'          => $reqExternos,
                'firma1'                => 'Mtro. Omar Ruiz Castillo - Coordinador de Apoyo Técnico',
                'firma2'                => 'Lcdo. Marco Antonio Guadarrama López - Delegado Administrativo',
                'evento_dia'            => $evento['fecha_evento'] ?? $registro['fecha_inicio'],
                'evento_horario'        => $registro['hora_inicio'] . ' - ' . $registro['hora_fin'],
                'evento_ubicacion'      => $carpeta['direccion_entrega'] ?? '',
                'maestra_ceremonias'    => $evento['maestra_ceremonias'] ?? '',
            ];

            // --- Cargar autoload y crear presentación ---
            $rootPath = dirname(APPROOT);
            require_once $rootPath . '/vendor/autoload.php';

            $ppt = new \PhpOffice\PhpPresentation\PhpPresentation();

            $publicPath = $rootPath . '/public';
            $rutaFondo = $publicPath . '/img/fondo_pptx.png';
            $fondoExiste = file_exists($rutaFondo);

            $crearSlide = function() use ($ppt, $rutaFondo, $fondoExiste) {
                $slide = $ppt->createSlide();
                if ($fondoExiste) {
                    try {
                        $background = new \PhpOffice\PhpPresentation\Slide\Background\Image();
                        $background->setPath($rutaFondo);
                        $slide->setBackground($background);
                    } catch (Exception $e) {}
                }
                return $slide;
            };

            // ============================================================
            // D1. Portada
            // ============================================================
            $slide1 = $crearSlide();
            $this->addTitle($slide1, 'TOLUCA', 48);
            $this->addSubtitle($slide1, 'CAPITAL DE OPORTUNIDADES Y PROGRESO', 28);
            $this->addText($slide1, $datosPPT['direccion'], 22, 200, true);
            $this->addText($slide1, 'DIRECCIÓN DE CONVIVENCIA SOCIAL', 20, 240, true);
            $this->addText($slide1, 'INCLUSIÓN Y NO DISCRIMINACIÓN', 20, 270, true);
            $this->addText($slide1, $datosPPT['evento_nombre'], 26, 320, true);
            $this->addMiniTable($slide1, [
                ['APROBADO', 'RESPONSABLE'],
                [$datosPPT['aprobado_por'], $datosPPT['responsable_por']]
            ], 420);
            $this->addText($slide1, 'Fecha de entrega: ' . $datosPPT['fecha_entrega'], 16, 520);
            $this->addText($slide1, 'Realizó: ' . $datosPPT['realizo'], 16, 550);
            $this->addText($slide1, 'Firma:', 16, 580);
            $this->addText($slide1, 'Fecha del evento: ' . $datosPPT['fecha_evento'], 16, 630);

            // ============================================================
            // D2. Objetivo y Beneficiarios
            // ============================================================
            $slide2 = $crearSlide();
            $this->addTitle($slide2, 'Objetivo y Beneficiarios', 34);
            $this->addText($slide2, 'Línea de acción PbRM: ' . $datosPPT['linea_accion'], 18, 140);
            $this->addText($slide2, 'Objetivo del Evento: ' . $datosPPT['objetivo_evento'], 18, 190);
            $this->addText($slide2, 'Número de beneficiarios: ' . $datosPPT['num_beneficiarios'], 18, 240);

            // ============================================================
            // D3. Justificación
            // ============================================================
            $slide3 = $crearSlide();
            $this->addTitle($slide3, 'Justificación e Impacto', 34);
            $this->addText($slide3, $datosPPT['justificacion'], 18, 140);

            // ============================================================
            // D4. Generales del Evento
            // ============================================================
            $slide4 = $crearSlide();
            $this->addTitle($slide4, 'Generales del Evento', 34);
            $this->addText($slide4, 'Fecha: ' . $datosPPT['gen_fecha'], 18, 140);
            $this->addText($slide4, 'Hora inicio: ' . $datosPPT['gen_hora'], 18, 180);
            $this->addText($slide4, 'Lugar: ' . $datosPPT['gen_lugar'], 18, 220);
            $this->addText($slide4, 'Vestimenta: ' . $datosPPT['gen_vestimenta'], 18, 260);
            $this->addText($slide4, 'Duración: ' . $datosPPT['gen_duracion'], 18, 300);
            $this->addText($slide4, 'Coordinación: ' . $datosPPT['gen_coordinacion'], 18, 340);
            $this->addText($slide4, 'Responsable: ' . $datosPPT['gen_responsable'], 18, 380);

            // ============================================================
            // D5. Ubicación
            // ============================================================
            $slide5 = $crearSlide();
            $this->addTitle($slide5, 'Ubicación del Evento', 34);
            $this->addText($slide5, 'Dirección: ' . $datosPPT['ubic_direccion'], 18, 140);
            $this->addText($slide5, 'Google Maps: ' . $datosPPT['ubic_link'], 18, 180);
            $this->addLocationImages($slide5, $datosPPT['imagen_lugar'], $datosPPT['imagen_maps'], $publicPath);

            // ============================================================
            // D6. Orden del Día (tabla)
            // ============================================================
            $slide6 = $crearSlide();
            $this->addTitle($slide6, 'Orden del Día', 34);
            $this->addAgendaTable($slide6, $datosPPT['agenda']);

            // ============================================================
            // D7. Presídium (gráfico + tabla al lado)
            // ============================================================
            $slide7 = $crearSlide();
            $this->addTitle($slide7, 'Presídium', 34);
            $this->addPresidiumGraphicWithTable($slide7, $datosPPT['presidium'], $datosPPT['tipo_presidium'], $datosPPT['maestra_ceremonias']);

            // ============================================================
            // D8. Invitados Especiales
            // ============================================================
            $slide8 = $crearSlide();
            $this->addTitle($slide8, 'Invitados Especiales', 34);
            $this->addGenericTable($slide8, $datosPPT['invitados'], ['nombre', 'cargo']);

            // ============================================================
            // D9. Módulos Jornada Integral
            // ============================================================
            $slide9 = $crearSlide();
            $this->addTitle($slide9, 'Módulos Jornada Integral', 34);
            $this->addGenericTable($slide9, $datosPPT['modulos'], ['institucion', 'servicio']);

            // ============================================================
            // D10. Requerimientos (Internos y Externos)
            // ============================================================
            $slide10 = $crearSlide();
            $this->addTitle($slide10, 'Requerimientos Operativos', 34);
            if (!empty($datosPPT['req_internos'])) {
                $this->addGenericTable($slide10, $datosPPT['req_internos'], ['cantidad', 'nombre_insumo', 'medida', 'unidad'], 'Internos');
            }
            if (!empty($datosPPT['req_externos'])) {
                $this->addGenericTable($slide10, $datosPPT['req_externos'], ['cantidad', 'nombre_insumo', 'medida', 'unidad'], 'Externos');
            }
            if (empty($datosPPT['req_internos']) && empty($datosPPT['req_externos'])) {
                $this->addText($slide10, 'No hay requerimientos registrados.', 18, 140);
            }

            // ============================================================
            // D11. Firmas
            // ============================================================
            $slide11 = $crearSlide();
            $this->addTitle($slide11, 'Requerimientos Finales y Firmas', 34);
            $this->addText($slide11, 'Evento: ' . $datosPPT['evento_nombre'], 18, 140);
            $this->addText($slide11, 'Día: ' . $datosPPT['evento_dia'], 18, 180);
            $this->addText($slide11, 'Horario: ' . $datosPPT['evento_horario'], 18, 220);
            $this->addText($slide11, 'Ubicación: ' . $datosPPT['evento_ubicacion'], 18, 260);
            $this->addText($slide11, 'Firma 1: ' . $datosPPT['firma1'], 18, 310);
            $this->addText($slide11, 'Firma 2: ' . $datosPPT['firma2'], 18, 350);

            // --- Guardar archivo ---
            $nombreArchivo = 'presentacion_' . $idRegistro . '_' . time() . '.pptx';
            $rutaRelativa = 'uploads/ppts/' . $nombreArchivo;
            $rutaAbsoluta = $publicPath . '/' . $rutaRelativa;

            if (!is_dir(dirname($rutaAbsoluta))) {
                mkdir(dirname($rutaAbsoluta), 0777, true);
            }

            $writer = \PhpOffice\PhpPresentation\IOFactory::createWriter($ppt, 'PowerPoint2007');
            $writer->save($rutaAbsoluta);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'url' => '/' . $rutaRelativa]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function addTitle($slide, $texto, $size = 34)
    {
        $shape = $slide->createRichTextShape()
                       ->setHeight(60)
                       ->setWidth(1600)
                       ->setOffsetX(160)
                       ->setOffsetY(40);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $run = $shape->createTextRun($texto);
        $run->getFont()->setBold(true)->setSize($size)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF800000'));
    }

    private function addSubtitle($slide, $texto, $size = 24)
    {
        $shape = $slide->createRichTextShape()
                       ->setHeight(50)
                       ->setWidth(1600)
                       ->setOffsetX(160)
                       ->setOffsetY(100);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $run = $shape->createTextRun($texto);
        $run->getFont()->setBold(true)->setSize($size)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF000000'));
    }

    private function addText($slide, $texto, $size = 18, $y = 120, $center = false)
    {
        $shape = $slide->createRichTextShape()
                       ->setHeight(35)
                       ->setWidth(1600)
                       ->setOffsetX(160)
                       ->setOffsetY($y);
        $align = $center ? \PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER : \PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_LEFT;
        $shape->getActiveParagraph()->getAlignment()->setHorizontal($align);
        $run = $shape->createTextRun($texto);
        $run->getFont()->setSize($size)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF000000'));
    }

    private function addMiniTable($slide, $data, $y)
    {
        $x = 360;
        $width = 1200;
        $rowHeight = 40;

        // Cabecera
        $shape = $slide->createRichTextShape()
                       ->setHeight($rowHeight)
                       ->setWidth($width)
                       ->setOffsetX($x)
                       ->setOffsetY($y);
        $shape->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)
              ->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FFE0E0E0'));
        $paragraph = $shape->getActiveParagraph();
        $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $run = $paragraph->createTextRun($data[0][0] . ' | ' . $data[0][1]);
        $run->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF800000'));

        $y += $rowHeight + 2;
        $shape = $slide->createRichTextShape()
                       ->setHeight($rowHeight)
                       ->setWidth($width)
                       ->setOffsetX($x)
                       ->setOffsetY($y);
        $paragraph = $shape->getActiveParagraph();
        $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $run = $paragraph->createTextRun($data[1][0] . ' | ' . $data[1][1]);
        $run->getFont()->setSize(14)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF000000'));
    }

    private function addLocationImages($slide, $imgLugar, $imgMaps, $publicPath)
    {
        $x = 160;
        $y = 250;
        $maxWidth = 600;
        $maxHeight = 400;
        $images = [];
        if ($imgLugar && file_exists($publicPath . '/' . $imgLugar)) {
            $images[] = ['path' => $publicPath . '/' . $imgLugar, 'label' => 'Foto del lugar'];
        }
        if ($imgMaps && file_exists($publicPath . '/' . $imgMaps)) {
            $images[] = ['path' => $publicPath . '/' . $imgMaps, 'label' => 'Foto Google Maps'];
        }
        if (empty($images)) {
            $this->addText($slide, 'No hay imágenes disponibles.', 16, 250);
            return;
        }
        foreach ($images as $idx => $img) {
            try {
                $drawing = $slide->createDrawingShape();
                $drawing->setPath($img['path']);
                $drawing->setWidth($maxWidth);
                $drawing->setHeight($maxHeight);
                $drawing->setOffsetX($x + ($idx * ($maxWidth + 40)));
                $drawing->setOffsetY($y);
                $this->addText($slide, $img['label'], 14, $y + $maxHeight + 10);
            } catch (Exception $e) {}
        }
    }

    private function addAgendaTable($slide, $ordenes)
    {
        if (empty($ordenes)) {
            $this->addText($slide, 'No hay actividades registradas.', 16, 300);
            return;
        }
        $x = 160;
        $y = 260;
        $altoFila = 28;
        $anchoTotal = 1600;
        // Cabecera
        $shape = $slide->createRichTextShape()
                       ->setHeight($altoFila)
                       ->setWidth($anchoTotal)
                       ->setOffsetX($x)
                       ->setOffsetY($y);
        $shape->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)
              ->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FFE0E0E0'));
        $paragraph = $shape->getActiveParagraph();
        $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_LEFT);
        $run = $paragraph->createTextRun(sprintf("%-12s %-50s %-50s %-18s", "Hora", "Actividad", "Responsable", "Duración"));
        $run->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF800000'));
        $y += $altoFila + 4;
        foreach ($ordenes as $o) {
            $duracion = ($o['duracion_calculada'] ?? 15) . ' min';
            $responsable = '';
            if (!empty($o['otro_responsable'])) {
                $responsable = substr($o['otro_responsable'], 0, 40);
            } elseif (!empty($o['responsable_id'])) {
                $responsable = 'ID: ' . $o['responsable_id'];
            }
            $hora = substr($o['hora_inicio'] ?? '', 0, 5);
            $actividad = substr($o['actividad'] ?? '', 0, 60);
            $shape = $slide->createRichTextShape()
                           ->setHeight($altoFila)
                           ->setWidth($anchoTotal)
                           ->setOffsetX($x)
                           ->setOffsetY($y);
            $paragraph = $shape->getActiveParagraph();
            $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_LEFT);
            $run = $paragraph->createTextRun(sprintf("%-12s %-50s %-50s %-18s", $hora, $actividad, $responsable, $duracion));
            $run->getFont()->setSize(12)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF000000'));
            $y += $altoFila + 4;
            if ($y > 850) break;
        }
    }

    private function addGenericTable($slide, $datos, $headers, $titulo = null)
    {
        if (empty($datos)) {
            if ($titulo) {
                $this->addText($slide, $titulo . ': Sin datos', 16, 300);
            }
            return;
        }
        $x = 160;
        $y = 260;
        $altoFila = 28;
        $anchoTotal = 1600;
        $anchoCol = intval($anchoTotal / count($headers));
        if ($titulo) {
            $this->addText($slide, $titulo . ':', 16, $y - 30);
        }
        // Cabecera
        $shape = $slide->createRichTextShape()
                       ->setHeight($altoFila)
                       ->setWidth($anchoTotal)
                       ->setOffsetX($x)
                       ->setOffsetY($y);
        $shape->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)
              ->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FFE0E0E0'));
        $paragraph = $shape->getActiveParagraph();
        $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_LEFT);
        $texto = '';
        foreach ($headers as $h) {
            $texto .= sprintf("%-{$anchoCol}s", ucfirst($h));
        }
        $run = $paragraph->createTextRun($texto);
        $run->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF800000'));
        $y += $altoFila + 4;
        foreach ($datos as $item) {
            $shape = $slide->createRichTextShape()
                           ->setHeight($altoFila)
                           ->setWidth($anchoTotal)
                           ->setOffsetX($x)
                           ->setOffsetY($y);
            $paragraph = $shape->getActiveParagraph();
            $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_LEFT);
            $texto = '';
            foreach ($headers as $h) {
                $valor = isset($item[$h]) ? substr($item[$h], 0, 30) : '';
                $texto .= sprintf("%-{$anchoCol}s", $valor);
            }
            $run = $paragraph->createTextRun($texto);
            $run->getFont()->setSize(12)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF000000'));
            $y += $altoFila + 4;
            if ($y > 850) break;
        }
    }

    // =========================================================================
    // PRESÍDIUM GRÁFICO + TABLA
    // =========================================================================

    private function addPresidiumGraphicWithTable($slide, $presidium, $tipo, $maestra)
    {
        if (empty($presidium)) {
            $this->addText($slide, 'No hay miembros de presídium.', 16, 300);
            return;
        }

        // Separar presidente y miembros
        $presidente = null;
        $miembros = [];
        foreach ($presidium as $p) {
            if (strpos($p['nombre_invitado'] ?? '', 'Ricardo Moreno Bastida') !== false) {
                $presidente = $p;
            } else {
                $miembros[] = $p;
            }
        }

        // 1. Dibujar los círculos (mitad izquierda)
        $this->drawPresidiumCircles($slide, $presidium, $tipo, $presidente, $miembros);

        // 2. Mostrar tabla al lado derecho (x = 900)
        $this->drawPresidiumTable($slide, $presidium);
        
        // 3. Maestra de ceremonias
        if (!empty($maestra)) {
            $this->addText($slide, 'Maestra de ceremonias: ' . $maestra, 16, 780);
        }
    }

    private function drawPresidiumCircles($slide, $presidium, $tipo, $presidente, $miembros)
    {
        $totalMiembros = count($miembros);
        $total = $totalMiembros + 1;
        $areaX = 80;
        $areaY = 260;
        $areaW = 700;
        $areaH = 500;
        $cx = $areaX + $areaW / 2;
        $cy = $areaY + $areaH / 2;
        $radioBase = 45;
        $radioPresidente = 65;

        // Orden: izquierda y derecha
        $left = [];
        $right = [];
        for ($i = 1; $i < $total; $i++) {
            if ($i % 2 != 0) $left[] = $i;
            else $right[] = $i;
        }
        $left = array_reverse($left);
        $order = array_merge($left, ['*'], $right);

        $posiciones = [];
        $idx = 0;
        foreach ($order as $pos) {
            if ($pos === '*') {
                $posiciones['*'] = $presidente;
            } else {
                if (isset($miembros[$idx])) {
                    $posiciones[$pos] = $miembros[$idx];
                    $idx++;
                } else {
                    $posiciones[$pos] = null;
                }
            }
        }

        $spots = [];
        $index = 0;
        $totalPos = count($posiciones);
        foreach ($posiciones as $key => $miembro) {
            $spot = $key;
            if ($spot === '*') {
                $x = $cx;
                $y = $cy;
                $radio = $radioPresidente;
                $nombre = $miembro ? ($miembro['nombre_invitado'] ?? 'Presidente') : 'Presidente';
                $cargo = $miembro ? ($miembro['cargo_invitado'] ?? '') : '';
            } else {
                $pos = $index;
                switch (strtolower($tipo)) {
                    case 'lineal':
                        $x = $areaX + 40 + ($pos / max(1, $totalPos - 1)) * ($areaW - 80);
                        $y = $cy;
                        break;
                    case 'herradura':
                        $angle = ($pos / max(1, $totalPos - 1)) * M_PI;
                        $x = $cx - 150 * cos($angle);
                        $y = $cy + 80 * sin($angle);
                        break;
                    case 'media_luna':
                        $angle = ($pos / max(1, $totalPos - 1)) * M_PI;
                        $x = $cx + 150 * cos($angle);
                        $y = $cy - 80 * sin($angle);
                        break;
                    case 'redondo':
                        $angle = ($pos / $totalPos) * 2 * M_PI - M_PI / 2;
                        $x = $cx + 120 * cos($angle);
                        $y = $cy + 120 * sin($angle);
                        break;
                    case 'rusa':
                        $seg = max(1, $totalPos - 1);
                        $x = $areaX + 40 + ($pos * ($areaW - 80) / $seg);
                        $y = ($pos == 0 || $pos == $totalPos - 1) ? $cy + 80 : $cy - 40;
                        break;
                    case 'cuadrada':
                        $cols = 4;
                        $row = floor($pos / $cols);
                        $col = $pos % $cols;
                        $x = $areaX + 60 + $col * 120;
                        $y = $areaY + 40 + $row * 100;
                        break;
                    default:
                        $x = $areaX + 40 + ($pos / max(1, $totalPos - 1)) * ($areaW - 80);
                        $y = $cy;
                }
                $radio = $radioBase;
                $nombre = $miembro ? ($miembro['nombre_invitado'] ?? '') : '';
                $cargo = $miembro ? ($miembro['cargo_invitado'] ?? '') : '';
            }
            $spots[] = [
                'x' => $x,
                'y' => $y,
                'radio' => $radio,
                'nombre' => $nombre,
                'cargo' => $cargo,
                'es_presidente' => ($spot === '*')
            ];
            $index++;
        }

        foreach ($spots as $s) {
            $this->drawSquare($slide, $s['x'], $s['y'], $s['radio'], $s['nombre'], $s['cargo'], $s['es_presidente']);
        }
    }

    private function drawSquare($slide, $x, $y, $radio, $nombre, $cargo, $esPresidente = false)
    {
        $size = $radio * 2;
        $shape = $slide->createRichTextShape();
        $shape->setWidth($size);
        $shape->setHeight($size);
        $shape->setOffsetX($x - $radio);
        $shape->setOffsetY($y - $radio);

        $shape->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID);
        $color = $esPresidente ? 'FF800000' : 'FFE0E0E0';
        $shape->getFill()->setStartColor(new \PhpOffice\PhpPresentation\Style\Color($color));

        $texto = $nombre . "\n" . $cargo;
        $paragraph = $shape->getActiveParagraph();
        $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER)
                               ->setVertical(\PhpOffice\PhpPresentation\Style\Alignment::VERTICAL_CENTER);
        $run = $paragraph->createTextRun($texto);
        $sizeFont = $esPresidente ? 12 : 9;
        $run->getFont()->setSize($sizeFont)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF000000'));
        $run->getFont()->setBold($esPresidente);
    }

    private function drawPresidiumTable($slide, $presidium)
    {
        $x = 900;
        $y = 260;
        $altoFila = 28;
        $ancho = 600;
        $anchoCol = 200; // dos columnas

        // Cabecera
        $shape = $slide->createRichTextShape()
                       ->setHeight($altoFila)
                       ->setWidth($ancho)
                       ->setOffsetX($x)
                       ->setOffsetY($y);
        $shape->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)
              ->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FFE0E0E0'));
        $paragraph = $shape->getActiveParagraph();
        $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_LEFT);
        $run = $paragraph->createTextRun(sprintf("%-10s %-20s", "Pos.", "Nombre"));
        $run->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF800000'));
        $y += $altoFila + 4;

        // Ordenar: primero el presidente (*) y luego los miembros en el orden que aparecen
        $ordered = [];
        $presidente = null;
        foreach ($presidium as $p) {
            if (strpos($p['nombre_invitado'] ?? '', 'Ricardo Moreno Bastida') !== false) {
                $presidente = $p;
            } else {
                $ordered[] = $p;
            }
        }
        // Insertar presidente al inicio
        if ($presidente) {
            array_unshift($ordered, $presidente);
        }

        // Asignar posiciones: * para presidente, 1,2,3...
        $pos = '*';
        foreach ($ordered as $idx => $miembro) {
            if ($idx === 0) {
                $pos = '*';
            } else {
                $pos = $idx;
            }
            $nombre = $miembro['nombre_invitado'] ?? '';
            $shape = $slide->createRichTextShape()
                           ->setHeight($altoFila)
                           ->setWidth($ancho)
                           ->setOffsetX($x)
                           ->setOffsetY($y);
            $paragraph = $shape->getActiveParagraph();
            $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_LEFT);
            $run = $paragraph->createTextRun(sprintf("%-10s %-20s", $pos, $nombre));
            $run->getFont()->setSize(12)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF000000'));
            $y += $altoFila + 4;
            if ($y > 850) break;
        }
    }
}