<?php

class CalendarioController extends Controller
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
        // Cargar datos para los filtros (unidades, lugares, delegaciones, actividades)
        $unidadModel = $this->model('UnidadAdministrativa');
        $lugarModel = $this->model('Lugar');
        $delegacionModel = $this->model('Delegacion');
        $actividadModel = $this->model('ActividadProgramada');

        $unidades = $unidadModel->obtenerTodas();
        $lugares = $lugarModel->obtenerTodos();
        $delegaciones = $delegacionModel->obtenerTodas();
        $actividades = $actividadModel->obtenerTodasConCodigo();

        $this->view('calendario/index', [
            'unidades' => $unidades,
            'lugares' => $lugares,
            'delegaciones' => $delegaciones,
            'actividades' => $actividades
        ]);
    }

    /**
     * Endpoint AJAX: devuelve actividades filtradas (JSON)
     * Parámetros vía GET: año, mes, filtro_responsable, filtro_unidad, filtro_lugar, filtro_delegacion, filtro_actividad, filtro_domicilio, fecha_dia (opcional)
     */
    public function datos()
    {
        header('Content-Type: application/json');
        
        $model = $this->model('RegistroActividad');
        
        $filters = [
            'year' => $_GET['year'] ?? null,
            'month' => $_GET['month'] ?? null,
            'responsable' => $_GET['filtro_responsable'] ?? null,
            'unidad_id' => $_GET['filtro_unidad'] ?? null,
            'lugar_id' => $_GET['filtro_lugar'] ?? null,
            'delegacion_id' => $_GET['filtro_delegacion'] ?? null,
            'actividad_id' => $_GET['filtro_actividad'] ?? null,
            'domicilio' => $_GET['filtro_domicilio'] ?? null,
            'fecha_dia' => $_GET['fecha_dia'] ?? null
        ];
        
        $actividades = $model->obtenerConFiltros($filters);
        echo json_encode($actividades);
    }
}