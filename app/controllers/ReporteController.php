<?php

class ReporteController extends Controller
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
        // Cargar unidades para el filtro
        $unidadModel = $this->model('UnidadAdministrativa');
        $unidades = $unidadModel->obtenerTodas();

        $this->view('reportes/index', [
            'unidades' => $unidades
        ]);
    }

    /**
     * Endpoint AJAX para obtener datos del reporte
     */
    public function data()
    {
        header('Content-Type: application/json');

        $anio = $_GET['anio'] ?? date('Y');
        $periodoTipo = $_GET['periodo_tipo'] ?? 'mensual';
        $periodoValor = $_GET['periodo_valor'] ?? date('n');
        $unidadId = $_GET['unidad_id'] ?? null;

        // Si no hay unidad seleccionada, devolver vacío
        if (!$unidadId) {
            echo json_encode([]);
            return;
        }

        // Obtener actividades de la unidad
        $actividadModel = $this->model('ActividadProgramada');
        $actividades = $actividadModel->obtenerPorUnidad($unidadId);
        if (empty($actividades)) {
            echo json_encode([]);
            return;
        }

        // Calcular rango de fechas según período
        $fechas = $this->calcularRangoFechas($anio, $periodoTipo, $periodoValor);
        $fechaInicio = $fechas['inicio'];
        $fechaFin = $fechas['fin'];

        // Preparar respuesta
        $resultado = [];
        $metaModel = $this->model('MetaActividadPeriodo');
        $registroModel = $this->model('RegistroActividad');

        foreach ($actividades as $act) {
            $actividadId = $act['id'];
            $meta = $metaModel->obtenerMeta($actividadId, $unidadId, $anio, $periodoTipo, $periodoValor);
            $registrado = $registroModel->contarPorActividadYPeriodo($actividadId, $fechaInicio, $fechaFin);
            $diferencia = $meta - $registrado;
            $avance = ($meta > 0) ? round(($registrado / $meta) * 100, 2) : 0;

            $resultado[] = [
                'actividad_id' => $actividadId,
                'actividad' => $act['descripcion'],
                'meta' => $meta,
                'registrado' => $registrado,
                'diferencia' => $diferencia,
                'avance' => $avance
            ];
        }

        echo json_encode($resultado);
    }

    /**
     * Calcula fecha inicio y fin según el período
     */
    private function calcularRangoFechas($anio, $periodoTipo, $periodoValor)
    {
        $inicio = null;
        $fin = null;

        if ($periodoTipo === 'mensual') {
            $inicio = "$anio-" . str_pad($periodoValor, 2, '0', STR_PAD_LEFT) . "-01";
            $fin = date("Y-m-t", strtotime($inicio));
        } elseif ($periodoTipo === 'trimestral') {
            $mesInicio = ($periodoValor - 1) * 3 + 1;
            $inicio = "$anio-" . str_pad($mesInicio, 2, '0', STR_PAD_LEFT) . "-01";
            $fin = date("Y-m-t", strtotime("$anio-" . str_pad($mesInicio + 2, 2, '0', STR_PAD_LEFT) . "-01"));
        } elseif ($periodoTipo === 'semestral') {
            $mesInicio = ($periodoValor - 1) * 6 + 1;
            $inicio = "$anio-" . str_pad($mesInicio, 2, '0', STR_PAD_LEFT) . "-01";
            $fin = date("Y-m-t", strtotime("$anio-" . str_pad($mesInicio + 5, 2, '0', STR_PAD_LEFT) . "-01"));
        } elseif ($periodoTipo === 'anual') {
            $inicio = "$anio-01-01";
            $fin = "$anio-12-31";
        }

        return ['inicio' => $inicio, 'fin' => $fin];
    }
}