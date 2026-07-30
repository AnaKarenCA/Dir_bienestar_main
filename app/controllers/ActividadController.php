<?php

class ActividadController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }
    }

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

        // Validar campos requeridos
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

        // --- Manejo de lugar "Otro" ---
        $lugar_id = (int)$data['lugar_id'];
        if ($lugar_id === 0) {
            if (empty($data['otro_lugar'])) {
                echo json_encode(['success' => false, 'error' => 'Debe especificar el nombre del nuevo lugar']);
                return;
            }
            $lugarModel = $this->model('Lugar');
            // Verificar si el modelo existe
            if (!$lugarModel) {
                echo json_encode(['success' => false, 'error' => 'Modelo Lugar no encontrado']);
                return;
            }
            // Método obtenerPorNombre
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

        // --- Crear domicilio ---
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

        // --- Guardar registros por cada día ---
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
    public function misActividades()
{
    $unidadId = $_SESSION['usuario_unidad_id'] ?? 0;
    $actividadModel = $this->model('ActividadProgramada');
    $actividades = $actividadModel->obtenerPorUnidad($unidadId);
    $this->view('actividades/mis_actividades', ['actividades' => $actividades]);
}
}