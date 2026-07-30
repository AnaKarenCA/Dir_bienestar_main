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
        $idRegistro = $_GET['id_registro'] ?? $_POST['id_registro'] ?? 0;
        if (!$idRegistro) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No se seleccionó registro.']);
            exit;
        }

        // Obtener registro completo
        $registroModel = $this->model('RegistroActividad');
        $registro = $registroModel->obtenerRegistroCompletoPorId($idRegistro);
        if (!$registro) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Registro no encontrado.']);
            exit;
        }

        // Obtener carpeta y evento asociado
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

        // Consultar tablas relacionadas usando el ID real del evento
        $ordenModel = $this->model('OrdenDelDia');
        $ordenes = $ordenModel->obtenerPorEventoDetalleId($eventoId);

        $presidiumModel = $this->model('PresidiumAsistente');
        $presidium = $presidiumModel->obtenerPorEventoDetalleId($eventoId);

        // Invitados, módulos y requerimientos (si tienes los modelos, descomenta)
        $invitados = [];
        $modulos = [];
        $reqDelegacion = [];
        $reqComunicacion = [];
        $reqAdministracion = [];

        // Calcular duración total
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
        $duracionTotalStr = trim(($horas > 0 ? $horas . ' hora(s) ' : '') . ($minutos > 0 ? $minutos . ' minuto(s)' : ''));

        // Preparar datos para el PPT
        $datosPPT = [
            'direccion'             => $registro['unidad_nombre'] ?? 'Dirección General de Bienestar',
            'evento_nombre'         => $evento['nombre_evento'] ?? $registro['actividad_desc'] ?? '',
            'aprobado_por'          => $evento['aprobado_por'] ?? 'MTRA. ANDREA MA. DEL ROCÍO MERLOS NÁJERA',
            'responsable_por'       => $evento['responsable_evento'] ?? (($registro['usuario_nombre'] ?? '') . ' - ' . ($registro['usuario_puesto'] ?? '')),
            'fecha_entrega'         => $carpeta['fecha_entrega'] ?? date('Y-m-d'),
            'realizo'               => $registro['usuario_nombre'] ?? '',
            'firma_nombre'          => $registro['usuario_nombre'] ?? '',
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
            'agenda'                => $ordenes,
            'evento_protocolario'   => '',
            'duracion_total_evento' => $duracionTotalStr,
            'presidium'             => $presidium,
            'presidium_tipo'        => $presidium[0]['tipo_presidium_id'] ?? null,
            'invitados'             => $invitados,
            'modulos'               => $modulos,
            'req_delegacion'        => $reqDelegacion,
            'req_comunicacion'      => $reqComunicacion,
            'req_administracion'    => $reqAdministracion,
            'firma1'                => 'Mtro. Omar Ruiz Castillo - Coordinador de Apoyo Técnico',
            'firma2'                => 'Lcdo. Marco Antonio Guadarrama López - Delegado Administrativo',
            'evento_dia'            => $evento['fecha_evento'] ?? $registro['fecha_inicio'],
            'evento_horario'        => $registro['hora_inicio'] . ' - ' . $registro['hora_fin'],
            'evento_ubicacion'      => $carpeta['direccion_entrega'] ?? '',
            'croquis_pantalla'      => $evento['imagen_croquis'] ?? '',
        ];

        // Incluir PhpPresentation (ajusta la ruta si es necesario)
        require_once APPROOT . '/vendor/autoload.php';

        $ppt = new \PhpOffice\PhpPresentation\PhpPresentation();

        // Diapositiva de ejemplo (debes personalizar con tus plantillas)
        $slide = $ppt->getActiveSlide();
        $shape = $slide->createRichTextShape()
                      ->setHeight(300)
                      ->setWidth(600)
                      ->setOffsetX(170)
                      ->setOffsetY(180);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun('Presentación generada para: ' . ($datosPPT['evento_nombre'] ?? 'Evento'));
        $textRun->getFont()->setBold(true)->setSize(28)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF800000'));

        // Guardar archivo
        $nombreArchivo = 'presentacion_' . $idRegistro . '_' . time() . '.pptx';
        $rutaRelativa = 'uploads/ppts/' . $nombreArchivo;
        $rutaAbsoluta = PUBLIC_PATH . '/' . $rutaRelativa;

        if (!is_dir(dirname($rutaAbsoluta))) {
            mkdir(dirname($rutaAbsoluta), 0777, true);
        }

        $writer = \PhpOffice\PhpPresentation\IOFactory::createWriter($ppt, 'PowerPoint2007');
        $writer->save($rutaAbsoluta);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'url' => '/' . $rutaRelativa]);
        exit;
    }
}