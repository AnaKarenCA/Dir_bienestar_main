<?php

// Incluir el helper de permisos
require_once APPROOT . '/helpers/PermissionHelper.php';

class ActividadController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }
    }

    /**
     * Guarda una o varias actividades (un registro por día)
     * 
     * @return void (JSON)
     */
    public function guardar()
    {
        header('Content-Type: application/json');

        // Obtener datos de la solicitud
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Si no hay datos o JSON inválido
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos o JSON mal formado']);
            return;
        }

        // ============================================================
        // OBTENER USUARIO Y CONEXIÓN
        // ============================================================
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        
        if (!$usuario) {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            return;
        }

        // Obtener conexión PDO desde el modelo (debe tener método getDb)
        $db = $usuarioModel->getDb();

        // ============================================================
        // VALIDAR PERMISOS DE UNIDAD
        // ============================================================
        $unidadesPermitidas = PermissionHelper::getUnidadesAccesibles($db, $usuario);
        if ($unidadesPermitidas !== null) {
            // Si el usuario no es admin/coordinador, validar que la unidad seleccionada esté permitida
            $unidadSeleccionada = isset($data['unidad_administrativa_id']) ? (int)$data['unidad_administrativa_id'] : 0;
            if (!in_array($unidadSeleccionada, $unidadesPermitidas)) {
                echo json_encode(['success' => false, 'error' => 'No tiene permiso para seleccionar esa unidad.']);
                return;
            }
        }

        // Si es Personal (rol=3), forzar su unidad
        if ($usuario['rol_id'] == 3) {
            $data['unidad_administrativa_id'] = $usuario['unidad_administrativa_id'];
        } 
        // Si es Admin, Jefe o Coordinador y no envió unidad, usar la suya (opcional)
        elseif (empty($data['unidad_administrativa_id']) && in_array($usuario['rol_id'], [1, 2, 5])) {
            $data['unidad_administrativa_id'] = $usuario['unidad_administrativa_id'];
        }

        // ============================================================
        // VALIDACIONES DE CAMPOS OBLIGATORIOS
        // ============================================================
        $required = [
            'unidad_administrativa_id', 'actividad_programada_id', 'unidad_medida_id',
            'lugar_id', 'delegacion_id', 'calle', 'numero_exterior',
            'beneficiarios_asistentes', 'tipo_entregable_id', 'descripcion', 'dias'
        ];
        foreach ($required as $field) {
            if (!isset($data[$field]) || (empty($data[$field]) && $data[$field] !== '0')) {
                echo json_encode(['success' => false, 'error' => "Campo $field es obligatorio"]);
                return;
            }
        }

        // Validar que dias sea un arreglo y tenga al menos un elemento
        if (!is_array($data['dias']) || count($data['dias']) === 0) {
            echo json_encode(['success' => false, 'error' => 'Debe agregar al menos un día con su horario']);
            return;
        }

        // Validar cada día
        foreach ($data['dias'] as $index => $dia) {
            if (empty($dia['fecha']) || empty($dia['hora_inicio']) || empty($dia['hora_fin'])) {
                echo json_encode(['success' => false, 'error' => "El día " . ($index+1) . " tiene campos incompletos"]);
                return;
            }
            if ($dia['hora_fin'] <= $dia['hora_inicio']) {
                echo json_encode(['success' => false, 'error' => "En el día " . ($index+1) . " (fecha {$dia['fecha']}), la hora fin debe ser mayor que la hora inicio"]);
                return;
            }
        }

        // Validar beneficiarios >= 1
        if ((int)$data['beneficiarios_asistentes'] < 1) {
            echo json_encode(['success' => false, 'error' => 'Los beneficiarios deben ser al menos 1']);
            return;
        }

        // Validar CP si se seleccionó subdelegación
        if (!empty($data['subdelegacion_id']) && empty($data['cp'])) {
            echo json_encode(['success' => false, 'error' => 'Debe seleccionar un código postal para la subdelegación']);
            return;
        }

        // ============================================================
        // MANEJO DE LUGAR "Otro"
        // ============================================================
        $lugar_id = (int)$data['lugar_id'];
        if ($lugar_id === 0) {
            if (empty($data['otro_lugar'])) {
                echo json_encode(['success' => false, 'error' => 'Debe especificar el nombre del nuevo lugar']);
                return;
            }
            $lugarModel = $this->model('Lugar');
            if (!$lugarModel) {
                echo json_encode(['success' => false, 'error' => 'Modelo Lugar no encontrado']);
                return;
            }
            if (!method_exists($lugarModel, 'obtenerPorNombre')) {
                echo json_encode(['success' => false, 'error' => 'Método obtenerPorNombre no existe en Lugar']);
                return;
            }
            $existente = $lugarModel->obtenerPorNombre($data['otro_lugar']);
            if ($existente) {
                $lugar_id = $existente['id'];
            } else {
                if (!method_exists($lugarModel, 'crear')) {
                    echo json_encode(['success' => false, 'error' => 'Método crear no existe en Lugar']);
                    return;
                }
                $lugar_id = $lugarModel->crear($data['otro_lugar']);
            }
        }

        // ============================================================
        // CREAR DOMICILIO
        // ============================================================
        $domicilioModel = $this->model('Domicilio');
        if (!$domicilioModel) {
            echo json_encode(['success' => false, 'error' => 'Modelo Domicilio no encontrado']);
            return;
        }
        $domicilio_id = $domicilioModel->crear([
            'calle' => $data['calle'],
            'numero_exterior' => $data['numero_exterior'],
            'numero_interior' => $data['numero_interior'] ?? null,
            'codigo_postal_id' => !empty($data['cp']) ? $data['cp'] : null
        ]);

        if (!$domicilio_id) {
            echo json_encode(['success' => false, 'error' => 'Error al guardar domicilio']);
            return;
        }

        // ============================================================
        // GUARDAR REGISTROS POR CADA DÍA
        // ============================================================
        $registroModel = $this->model('RegistroActividad');
        if (!$registroModel) {
            echo json_encode(['success' => false, 'error' => 'Modelo RegistroActividad no encontrado']);
            return;
        }

        $registrosGuardados = 0;
        foreach ($data['dias'] as $dia) {
            $datosRegistro = [
                'usuario_id' => $_SESSION['usuario_id'],
                'unidad_administrativa_id' => $data['unidad_administrativa_id'],
                'fecha_inicio' => $dia['fecha'],
                'fecha_fin' => $dia['fecha'],
                'hora_inicio' => $dia['hora_inicio'],
                'hora_fin' => $dia['hora_fin'],
                'lugar_id' => $lugar_id,
                'domicilio_id' => $domicilio_id,
                'unidad_medida_id' => $data['unidad_medida_id'],
                'beneficiarios_asistentes' => $data['beneficiarios_asistentes'],
                'descripcion' => $data['descripcion'],
                'tipo_entregable_id' => $data['tipo_entregable_id'],
                'actividad_programada_id' => $data['actividad_programada_id']
            ];
            if ($registroModel->guardar($datosRegistro)) {
                $registrosGuardados++;
            }
        }

        if ($registrosGuardados > 0) {
            echo json_encode(['success' => true, 'registros' => $registrosGuardados]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al guardar actividad(es)']);
        }
    }

    /**
     * Muestra las actividades programadas de la unidad del usuario
     * con filtros de permisos.
     */
    public function misActividades()
    {
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        if (!$usuario) {
            die('Usuario no encontrado');
        }

        // Obtener conexión para el helper (aunque no se usa directamente aquí,
        // se pasa al modelo RegistroActividad que a su vez usa el helper)
        $db = $usuarioModel->getDb();

        // Obtener actividades con filtros y permisos
        $filtros = []; // Se pueden agregar filtros adicionales si se desea
        $registroModel = $this->model('RegistroActividad');
        $actividades = $registroModel->obtenerConFiltros($filtros, $usuario);

        // Transformar datos para la vista (agregar estado, conteo de evidencias, etc.)
        // Por ahora, pasar directamente
        $this->view('actividades/mis_actividades', [
            'actividades' => $actividades,
            'usuario' => $usuario
        ]);
    }
}