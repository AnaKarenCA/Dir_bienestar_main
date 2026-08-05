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

    /**
     * Vista principal del calendario
     */
    public function index()
    {
        // Obtener usuario actual para pasar a la vista (opcional)
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);

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
            'unidades'      => $unidades,
            'lugares'       => $lugares,
            'delegaciones'  => $delegaciones,
            'actividades'   => $actividades,
            'usuario'       => $usuario // Opcional: para usar en JS si se necesita
        ]);
    }

    /**
     * Endpoint AJAX: devuelve actividades filtradas (JSON)
     * Parámetros vía GET: 
     *   - year, month (obligatorios)
     *   - filtro_responsable, filtro_unidad, filtro_lugar, filtro_delegacion, 
     *     filtro_actividad, filtro_domicilio, fecha_dia (opcionales)
     * 
     * Aplica permisos según el rol del usuario:
     *   - Administrador (rol=1) y Coordinador (rol=5): ven todo
     *   - Jefe de área (rol=2): ve su unidad + hijas según jerarquía
     *   - Personal (rol=3): solo sus propias actividades
     */
    public function datos()
    {
        header('Content-Type: application/json');
        
        // Obtener usuario actual para aplicar permisos
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        
        // Construir array de filtros
        $filters = [
            'year'          => $_GET['year'] ?? null,
            'month'         => $_GET['month'] ?? null,
            'responsable'   => $_GET['filtro_responsable'] ?? null,
            'unidad_id'     => $_GET['filtro_unidad'] ?? null,
            'lugar_id'      => $_GET['filtro_lugar'] ?? null,
            'delegacion_id' => $_GET['filtro_delegacion'] ?? null,
            'actividad_id'  => $_GET['filtro_actividad'] ?? null,
            'domicilio'     => $_GET['filtro_domicilio'] ?? null,
            'fecha_dia'     => $_GET['fecha_dia'] ?? null
        ];
        
        // Obtener actividades con filtros y permisos
        $model = $this->model('RegistroActividad');
        $actividades = $model->obtenerConFiltros($filters, $usuario);
        
        echo json_encode($actividades);
    }
}