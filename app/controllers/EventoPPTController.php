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

    public function index()
    {
        $this->view('evidencias/ppt_selector', [
            'registros' => $this->model('RegistroActividad')->obtenerRegistrosConCarpeta()
        ]);
    }

    /** Genera la carpeta usando la plantilla elegida al editar el evento. */
    public function generar()
    {
        try {
            $registroId = (int) ($_GET['id_registro'] ?? $_POST['id_registro'] ?? 0);
            if (!$registroId) {
                throw new Exception('No se seleccionó registro.');
            }

            $registroModel = $this->model('RegistroActividad');
            $carpetaModel = $this->model('Carpeta');
            $eventoModel = $this->model('EventoDetalle');

            $registro = $registroModel->obtenerRegistroCompletoPorId($registroId);
            $carpeta = $carpetaModel->obtenerPorRegistroActividadId($registroId);

            if (!$registro || !$carpeta) {
                throw new Exception('No se encontró la carpeta del evento.');
            }

            $evento = $eventoModel->obtenerPorCarpetaIdCompleto($carpeta['id']);
            if (!$evento) {
                throw new Exception('El evento aún no tiene información para generar la presentación.');
            }

            $eventoId = (int) $evento['id'];
            $carpetaId = (int) $carpeta['id'];

            // Obtener presidium
            $presidiumModel = $this->model('PresidiumAsistente');
            $presidium = $presidiumModel->obtenerPorEventoDetalleId($eventoId);

            // Obtener fotos del evento
            $fotosEvento = $this->model('EventoFotos')->obtenerPorEventoDetalleId($eventoId);

            // Obtener firmas
            $firmas = $carpetaModel->obtenerFirmasPorId($carpetaId);

            // Obtener tipo de presidium
            $tipo = null;
            if ($presidium && !empty($presidium[0]['tipo_presidium_id'])) {
                $tipo = $this->model('TipoPresidium')->obtenerPorId($presidium[0]['tipo_presidium_id']);
            }

            $root = dirname(APPROOT);
            require_once $root . '/vendor/autoload.php';
            require_once APPROOT . '/templates/ppt/plantilla_evento.php';
            require_once APPROOT . '/services/EventoPptGenerator.php';

            $portadaTemplate = $this->resolveTemplate($root, 'public/img/fondo_pptx/portada.png', 'public/img/fondo_pptx_portada.png');
            $slideTemplate = $this->resolveTemplate($root, 'public/img/fondo_pptx/sin_img.png', 'public/img/fondo_pptx_sin_img.png');
            $fondoEvento = $this->loadEventImage($root, $evento['imagen_fondo'] ?? null);
            $logo = $this->loadEventImage($root, $carpeta['logo_toluca'] ?? null);
            $presidiumBackground = $this->loadEventImage($root, $tipo['imagen_editable'] ?? null);
            $imagenLugar = $this->loadEventImage($root, $this->rutaFoto($fotosEvento, ['lugar', 'sitio', 'venue']) ?: ($evento['imagen_lugar'] ?? null));
            $imagenMaps = $this->loadEventImage($root, $this->rutaFoto($fotosEvento, ['maps', 'mapa', 'google']) ?: ($evento['imagen_maps'] ?? null));

            $duracion = $this->duracion($registro['hora_inicio'] ?? null, $registro['hora_fin'] ?? null);
            $objetivoPbRm = $registro['unidad_objetivo'] ?? '';

            // Preparar datos para el generador
            $data = [
                'direccion'                => $registro['unidad_nombre'] ?? '',
                'evento_nombre'            => $evento['nombre_evento'] ?? 'Evento',
                'fecha_evento'             => $evento['fecha_evento'] ?? '',
                'responsable_por'          => $evento['responsable_evento'] ?? '',
                'director'                 => $evento['aprobado_por'] ?? '',
                'linea_accion'             => $evento['descripcion_meta'] ?? '',
                'objetivo_pbrm'            => $objetivoPbRm,
                'objetivo_evento'          => $evento['objetivo'] ?? '',
                'num_beneficiarios'        => $registro['beneficiarios_asistentes'] ?? 0,
                'justificacion'            => $evento['justificacion'] ?? '',
                'evento_horario'           => trim(($registro['hora_inicio'] ?? '') . ' - ' . ($registro['hora_fin'] ?? '')),
                'duracion'                 => $duracion,
                'ubicacion'                => $carpeta['direccion_entrega'] ?? ($registro['lugar_nombre'] ?? ''),
                'vestimenta'               => $evento['vestimenta'] ?? '',
                'coordinacion'             => $evento['coordinacion_evento'] ?? '',
                'maestra_ceremonias'       => $evento['maestra_ceremonias'] ?? '',
                'link_mapa'                => $evento['link_mapa'] ?? '',
                'portada_fondo'            => $fondoEvento,
                'portada_template'         => $portadaTemplate,
                'slide_template'           => $slideTemplate,
                'imagen_evento'            => $imagenLugar,
                'imagen_lugar'             => $imagenLugar,
                'imagen_maps'              => $imagenMaps,
                'firma_responsable_nombre' => $firmas['realizo_nombre'] ?? '',
                'firma_responsable_cargo'  => $firmas['realizo_puesto'] ?? '',
                'firma_delegado_nombre'    => $firmas['delegado_nombre'] ?? '',
                'firma_delegado_cargo'     => $firmas['delegado_puesto'] ?? '',
                'agenda'                   => $this->model('OrdenDelDia')->obtenerPorEventoDetalleId($eventoId),
                'presidium'                => $presidium,
                'presidium_background'     => $presidiumBackground,
                'invitados'                => $this->conRespaldo(
                    $this->model('OrdenInvitado')->obtenerPorEventoDetalleId($eventoId),
                    $evento['invitados_especiales'] ?? '[]'
                ),
                'modulos'                  => $this->conRespaldo(
                    $this->model('OrdenModulo')->obtenerPorEventoDetalleId($eventoId),
                    $evento['modulos_jornada'] ?? '[]'
                ),
                'req_internos'             => $this->enriquecerRequerimientos(
                    $this->conRespaldo(
                        $this->model('OrdenRequerimiento')->obtenerPorEventoDetalleIdYTipo($eventoId, 'interno'),
                        $evento['requerimientos_internos'] ?? '[]'
                    )
                ),
                'req_externos'             => $this->enriquecerRequerimientos(
                    $this->conRespaldo(
                        $this->model('OrdenRequerimiento')->obtenerPorEventoDetalleIdYTipo($eventoId, 'externo'),
                        $evento['requerimientos_externos'] ?? '[]'
                    )
                ),
                'req_comunicacion'         => $this->enriquecerRequerimientos(
                    $this->decodeJson($evento['requerimientos_comunicacion'] ?? '[]')
                ),
            ];

            $ppt = (new EventoPptGenerator($slideTemplate, $logo, $data['direccion']))->build($data);

            $dir = $root . '/public/uploads/ppts';
            if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
                throw new Exception('No se pudo crear el directorio de salida.');
            }

            $file = 'presentacion_' . $registroId . '_' . time() . '.pptx';
            \PhpOffice\PhpPresentation\IOFactory::createWriter($ppt, 'PowerPoint2007')
                ->save($dir . '/' . $file);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'url' => '/uploads/ppts/' . $file]);

        } catch (Throwable $e) {
            header('Content-Type: application/json', true, 422);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ===== MÉTODOS AUXILIARES =====

    private function loadEventImage($root, $storedPath)
    {
        if (!$storedPath) return null;
        $relative = ltrim($storedPath, '/\\');
        $paths = preg_match('#^[A-Za-z]:[\\\\/]#', $storedPath)
            ? [$storedPath]
            : [
                $root . '/' . $relative,
                $root . '/public/' . $relative,
                $root . '/public/img/' . $relative,
            ];
        $path = null;
        foreach ($paths as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                $path = $candidate;
                break;
            }
        }
        if (!$path) return null;

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'], true)) return null;

        $image = @getimagesize($path);
        return ($image && in_array($image[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF], true))
            ? $path
            : null;
    }

    private function resolveTemplate($root, $preferred, $legacy)
    {
        $preferredPath = $root . '/' . $preferred;
        return is_file($preferredPath) ? $preferredPath : $root . '/' . $legacy;
    }

    private function rutaFoto(array $fotos, array $tipos)
    {
        foreach ($fotos as $foto) {
            $tipo = strtolower((string) ($foto['tipo_foto'] ?? ''));
            foreach ($tipos as $coincidencia) {
                if (strpos($tipo, $coincidencia) !== false) {
                    return $foto['ruta_foto'] ?? null;
                }
            }
        }
        return null;
    }

    private function decodeJson($value)
    {
        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function conRespaldo(array $registros, $json)
    {
        return $registros ?: $this->decodeJson($json);
    }

    private function enriquecerRequerimientos(array $requerimientos)
    {
        $insumos = [];
        foreach ($requerimientos as &$requerimiento) {
            $id = (int) ($requerimiento['inventario_insumo_id'] ?? $requerimiento['insumo_id'] ?? 0);
            if (!$id) continue;
            if (!isset($insumos[$id])) {
                $insumos[$id] = $this->model('InventarioInsumo')->obtenerPorId($id) ?: [];
            }
            $insumo = $insumos[$id];
            $requerimiento['nombre_insumo'] = $requerimiento['nombre_insumo'] ?? ($insumo['nombre_insumo'] ?? '');
            $requerimiento['medida'] = $requerimiento['medida'] ?? ($insumo['medida'] ?? '');
            $requerimiento['unidad'] = $requerimiento['unidad'] ?? ($insumo['unidad'] ?? '');
        }
        unset($requerimiento);
        return $requerimientos;
    }

    private function duracion($inicio, $fin)
    {
        if (!$inicio || !$fin) return '';
        $segundos = strtotime($fin) - strtotime($inicio);
        if ($segundos <= 0) return '';
        $minutos = (int) ($segundos / 60);
        return floor($minutos / 60) . ' h ' . ($minutos % 60) . ' min';
    }
}