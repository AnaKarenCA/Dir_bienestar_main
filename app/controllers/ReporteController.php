<?php

// Incluir el helper de permisos
require_once APPROOT . '/helpers/PermissionHelper.php';

class ReporteController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }
    }

    /**
     * Vista principal de reportes.
     * Muestra solo las unidades administrativas a las que el usuario tiene acceso.
     */
    public function index()
    {
        // Obtener usuario
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        if (!$usuario) {
            // Redirigir o mostrar error
            die('Usuario no encontrado');
        }

        // Obtener conexión a la base de datos desde el modelo Usuario
        $db = $usuarioModel->getDb();

        // Obtener unidades accesibles
        $unidadesPermitidas = PermissionHelper::getUnidadesAccesibles($db, $usuario);

        $unidadModel = $this->model('UnidadAdministrativa');
        if ($unidadesPermitidas === null) {
            // Admin o Coordinador: ven todas las unidades
            $unidades = $unidadModel->obtenerTodas();
        } else {
            // Personal o Jefe: solo las unidades permitidas
            $unidades = empty($unidadesPermitidas) 
                ? [] 
                : $unidadModel->obtenerPorIds($unidadesPermitidas);
        }

        $this->view('reportes/index', [
            'unidades' => $unidades
        ]);
    }

    /**
     * Endpoint AJAX para obtener los datos del reporte.
     * Aplica filtros de permisos según el rol del usuario.
     */
    public function data()
    {
        header('Content-Type: application/json');

        $anio = $_GET['anio'] ?? date('Y');
        $periodoTipo = $_GET['periodo_tipo'] ?? 'mensual';
        $periodoValor = $_GET['periodo_valor'] ?? date('n');
        $unidadId = $_GET['unidad_id'] ?? null;

        // Obtener el usuario actual
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        if (!$usuario) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            return;
        }

        // Obtener conexión a la base de datos
        $db = $usuarioModel->getDb();

        // Obtener unidades accesibles usando el helper
        $unidadesPermitidas = PermissionHelper::getUnidadesAccesibles($db, $usuario);

        // ============================================================
        // APLICAR FILTROS DE PERMISOS
        // ============================================================
        if ($unidadesPermitidas === null) {
            // Admin (rol=1) o Coordinador (rol=5): acceso total
            if (!$unidadId) {
                echo json_encode([]);
                return;
            }
            // Cualquier unidad es válida para admin/coordinador
        } else {
            // Personal (rol=3) o Jefe (rol=2): solo unidades permitidas
            if (!$unidadId || !in_array((int)$unidadId, $unidadesPermitidas)) {
                echo json_encode([]);
                return;
            }
            // Si es Personal (rol=3), además debe ser su propia unidad
            if ($usuario['rol_id'] == 3) {
                if ((int)$unidadId != (int)$usuario['unidad_administrativa_id']) {
                    echo json_encode([]);
                    return;
                }
            }
        }

        // ============================================================
        // OBTENER ACTIVIDADES DE LA UNIDAD SELECCIONADA
        // ============================================================
        $actividadModel = $this->model('ActividadProgramada');
        $actividades = $actividadModel->obtenerPorUnidad($unidadId);
        if (empty($actividades)) {
            echo json_encode([]);
            return;
        }

        // Calcular rango de fechas según el período
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
                'actividad'    => $act['descripcion'],
                'meta'         => $meta,
                'registrado'   => $registrado,
                'diferencia'   => $diferencia,
                'avance'       => $avance
            ];
        }

        echo json_encode($resultado);
    }

    /**
     * Calcula fecha de inicio y fin según el tipo de período.
     *
     * @param int    $anio
     * @param string $periodoTipo  mensual|trimestral|semestral|anual
     * @param int    $periodoValor 1-12 para mensual, 1-4 para trimestral, 1-2 para semestral, 1 para anual
     * @return array ['inicio' => 'YYYY-MM-DD', 'fin' => 'YYYY-MM-DD']
     */
    private function calcularRangoFechas($anio, $periodoTipo, $periodoValor)
    {
        $inicio = null;
        $fin = null;

        if ($periodoTipo === 'mensual') {
            $mes = (int)$periodoValor;
            $inicio = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
            $fin = date("Y-m-t", strtotime($inicio));
        } elseif ($periodoTipo === 'trimestral') {
            $trimestre = (int)$periodoValor;
            $mesInicio = ($trimestre - 1) * 3 + 1;
            $inicio = "$anio-" . str_pad($mesInicio, 2, '0', STR_PAD_LEFT) . "-01";
            $fin = date("Y-m-t", strtotime("$anio-" . str_pad($mesInicio + 2, 2, '0', STR_PAD_LEFT) . "-01"));
        } elseif ($periodoTipo === 'semestral') {
            $semestre = (int)$periodoValor;
            $mesInicio = ($semestre - 1) * 6 + 1;
            $inicio = "$anio-" . str_pad($mesInicio, 2, '0', STR_PAD_LEFT) . "-01";
            $fin = date("Y-m-t", strtotime("$anio-" . str_pad($mesInicio + 5, 2, '0', STR_PAD_LEFT) . "-01"));
        } elseif ($periodoTipo === 'anual') {
            $inicio = "$anio-01-01";
            $fin = "$anio-12-31";
        }

        return ['inicio' => $inicio, 'fin' => $fin];
    }
}