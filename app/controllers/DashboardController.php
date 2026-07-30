<?php

class DashboardController extends Controller
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
        // Modelos necesarios
        $unidadModel = $this->model('UnidadAdministrativa');
        $delegacionModel = $this->model('Delegacion');
        $lugarModel = $this->model('Lugar');
        $tipoEntregableModel = $this->model('TipoEntregable');
        $usuarioModel = $this->model('Usuario');

        // Datos para selects
        $unidades = $unidadModel->obtenerTodas();
        $delegaciones = $delegacionModel->obtenerTodas();
        $lugares = $lugarModel->obtenerTodos();
        $tiposEntregable = $tipoEntregableModel->obtenerTodos();

        // Responsable: nombre + puesto
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        $nombre = $usuario['nombre'] ?? $_SESSION['usuario_nombre'];
        $puesto = $usuario['puesto'] ?? '';
        $responsable = $puesto ?: 'Usuario';

        // Pasar todo a la vista
        $this->view('dashboard/index', [
            'unidades' => $unidades,
            'delegaciones' => $delegaciones,
            'lugares' => $lugares,
            'tiposEntregable' => $tiposEntregable,
            'responsable' => $responsable
        ]);
    }

    // Obtiene subdelegaciones de una delegación (ya existía)
    public function subdelegaciones($delegacionId)
    {
        header('Content-Type: application/json');
        $model = $this->model('Subdelegacion');
        echo json_encode($model->obtenerPorDelegacion($delegacionId));
    }

    // Obtiene actividades de una unidad administrativa
    public function actividadesPorUnidad($unidadId)
    {
        header('Content-Type: application/json');
        $model = $this->model('ActividadProgramada');
        echo json_encode($model->obtenerPorUnidad($unidadId));
    }

    // Obtiene códigos postales de una subdelegación
    public function codigosPostalesPorSubdelegacion($subdelegacionId)
    {
        header('Content-Type: application/json');
        $model = $this->model('CodigoPostal');
        echo json_encode($model->obtenerPorSubdelegacion($subdelegacionId));
    }
    public function codigosPostalesPorDelegacion($delegacionId)
{
    header('Content-Type: application/json');
    $model = $this->model('CodigoPostal');
    echo json_encode($model->obtenerPorDelegacion($delegacionId));
}
public function obtenerTodasConCodigo()
{
    $stmt = $this->db->query("SELECT id, descripcion, codigo FROM Actividad_programada ORDER BY codigo");
    $stmt->execute();
    return $stmt->fetchAll();
}
}