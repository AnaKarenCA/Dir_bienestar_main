<?php

class EventosController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }
    }

    // ============================================================
    // 1. LISTADO DE EVENTOS
    // ============================================================
    public function index()
    {
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        $unidadId = $usuario['unidad_administrativa_id'] ?? null;
        $rolId = $usuario['rol_id'] ?? null;
        $esAdmin = ($rolId == 1);

        $tipoEntregableModel = $this->model('TipoEntregable');
        $tipos = $tipoEntregableModel->obtenerTodos();

        $registroModel = $this->model('RegistroActividad');
        $tipoSeleccionado = isset($_GET['tipo']) ? (int)$_GET['tipo'] : null;

        $registros = [];
        if ($tipoSeleccionado) {
            if ($esAdmin) {
                $registros = $registroModel->obtenerPorTipoEntregable($tipoSeleccionado);
            } else {
                $registros = $registroModel->obtenerPorTipoEntregableYUnidad($tipoSeleccionado, $unidadId);
            }
        }

        $conteos = [];
        foreach ($tipos as $tipo) {
            if ($esAdmin) {
                $conteos[$tipo['id']] = $registroModel->contarPorTipoEntregable($tipo['id']);
            } else {
                $conteos[$tipo['id']] = $registroModel->contarPorTipoEntregableYUnidad($tipo['id'], $unidadId);
            }
        }

        $this->view('eventos/index', [
            'tipos'            => $tipos,
            'conteos'          => $conteos,
            'tipoSeleccionado' => $tipoSeleccionado,
            'registros'        => $registros,
            'unidadId'         => $unidadId,
            'esAdmin'          => $esAdmin,
            'usuario'          => $usuario
        ]);
    }

    // ============================================================
    // 2. EDICIÓN DE CARPETA
    // ============================================================
    public function editar_carpeta()
    {
        $idRegistro = $_GET['id_registro'] ?? 0;
        if (!$idRegistro) {
            die("No se especificó registro.");
        }

        $registroModel = $this->model('RegistroActividad');
        $registro = $registroModel->obtenerRegistroCompletoPorId($idRegistro);
        if (!$registro) {
            die("Registro no encontrado.");
        }

        $carpetaModel = $this->model('Carpeta');
        $carpeta = $carpetaModel->obtenerConDatosCompletos($idRegistro);
        if (!$carpeta) {
            $carpeta = [
                'id'                         => null,
                'registro_actividad_id'      => $idRegistro,
                'logo_toluca'                => null,
                'direccion_entrega'          => null,
                'link_mapa'                  => null,
                'fecha_entrega'              => date('Y-m-d'),
                'realizo_id'                 => $_SESSION['usuario_id'],
                'autorizado_por_id'          => null,
                'firma'                      => null,
                'estado'                     => 'pendiente',
                'justificacion_fuera_tiempo' => null
            ];
        }

        $eventoModel = $this->model('EventoDetalle');
        $evento = $eventoModel->obtenerPorCarpetaIdCompleto($carpeta['id'] ?? null);
        if (!$evento) {
            $evento = [
                'id'                     => null,
                'carpeta_id'             => $carpeta['id'],
                'nombre_evento'          => $registro['actividad_desc'] ?? '',
                'fecha_evento'           => $registro['fecha_inicio'],
                'objetivo'               => $registro['unidad_objetivo'] ?? '',
                'objetivo_evento'        => '',
                'justificacion'          => '',
                'vestimenta'             => '',
                'imagen_diseno'          => null,
                'descripcion_meta'       => $registro['actividad_desc'] ?? '',
                'link_mapa'              => '',
                'imagen_fondo'           => null,
                'imagen_lugar'           => null,
                'imagen_maps'            => null,
                'imagen_croquis'         => null,
                'aprobado_por'           => 'MTRA. ANDREA MA. DEL ROCÍO MERLOS NÁJERA',
                'responsable_evento'     => '',
                'coordinacion_evento'    => '',
                'maestra_ceremonias'     => '',
                'num_spots'              => 5,
                'invitados_especiales'   => '[]',
                'modulos_jornada'        => '[]',
                'requerimientos_internos'=> '[]',
                'requerimientos_externos'=> '[]',
                'comunicacion_social'    => '',
                'delegacion_admin_resumen' => '',
                'fecha_entrega'          => date('Y-m-d'),
                'firma'                  => null
            ];
        } else {
            $evento['invitados_especiales'] = json_decode($evento['invitados_especiales'] ?? '[]', true);
            $evento['modulos_jornada']      = json_decode($evento['modulos_jornada'] ?? '[]', true);
            $evento['requerimientos_internos'] = json_decode($evento['requerimientos_internos'] ?? '[]', true);
            $evento['requerimientos_externos'] = json_decode($evento['requerimientos_externos'] ?? '[]', true);
            $evento['coordinacion_evento']  = $evento['coordinacion_evento'] ?? '';
            $evento['objetivo_evento']      = $evento['objetivo'] ?? '';
            $evento['maestra_ceremonias']   = $evento['maestra_ceremonias'] ?? '';
            $evento['num_spots']            = $evento['num_spots'] ?? 5;
        }

        $invitadosList = $evento['invitados_especiales'] ?? [];
        $modulosList   = $evento['modulos_jornada'] ?? [];
        $internos      = $evento['requerimientos_internos'] ?? [];
        $externos      = $evento['requerimientos_externos'] ?? [];

        $ordenModel = $this->model('OrdenDelDia');
        $ordenes = $ordenModel->obtenerPorEventoDetalleId($evento['id'] ?? 0);

        // Calcular duración para cada orden (hora_fin - hora_inicio)
        foreach ($ordenes as &$o) {
            if (!empty($o['hora_inicio']) && !empty($o['hora_fin'])) {
                $horaInicio = strtotime($o['hora_inicio']);
                $horaFin = strtotime($o['hora_fin']);
                $o['duracion_calculada'] = round(($horaFin - $horaInicio) / 60);
            } else {
                $o['duracion_calculada'] = 15;
            }
        }
        unset($o);

        $presidiumModel = $this->model('PresidiumAsistente');
        $presidium = $presidiumModel->obtenerPorEventoDetalleId($evento['id'] ?? 0);

        $tipoPresidiumModel = $this->model('TipoPresidium');
        $tiposPresidium = $tipoPresidiumModel->obtenerTodos();
        $tipoPresidiumSeleccionado = '';
        if (!empty($presidium)) {
            $tipoId = $presidium[0]['tipo_presidium_id'] ?? null;
            foreach ($tiposPresidium as $tp) {
                if ($tp['id'] == $tipoId) {
                    $tipoPresidiumSeleccionado = $tp['nombre_tipo'];
                    break;
                }
            }
        }

        $inventarioModel = $this->model('InventarioInsumo');
        $insumos = $inventarioModel->obtenerActivos();
        $insumosInternos = array_filter($insumos, function($i) { return $i['tipo'] == 'Interno'; });
        $insumosExternos = array_filter($insumos, function($i) { return $i['tipo'] == 'Externo'; });

        $unidadJefeModel = $this->model('UnidadJefe');
        $jefe = $unidadJefeModel->obtenerJefeActual($registro['unidad_administrativa_id']);
        $responsableEvento = $jefe ? $jefe['nombre'] . ' - ' . $jefe['puesto'] : ($registro['usuario_nombre'] ?? '');
        $evento['responsable_evento'] = $responsableEvento;

        $objetivo_pbrm = '';
        if (!empty($registro['unidad_administrativa_id'])) {
            $unidadAdminModel = $this->model('UnidadAdministrativa');
            $unidad = $unidadAdminModel->obtenerPorId($registro['unidad_administrativa_id']);
            $objetivo_pbrm = $unidad['objetivo'] ?? '';
        }

        $responsable_solo_nombre = $jefe ? $jefe['nombre'] : ($registro['usuario_nombre'] ?? '');

        $direccion = '';
        if (!empty($registro['calle']) || !empty($registro['numero_exterior'])) {
            $direccion = trim(
                ($registro['calle'] ?? '') . ' ' .
                ($registro['numero_exterior'] ?? '') .
                ($registro['numero_interior'] ? ' Int. ' . $registro['numero_interior'] : '') .
                ($registro['codigo_postal'] ? ' CP ' . $registro['codigo_postal'] : '')
            );
        }
        if (empty($carpeta['direccion_entrega']) && !empty($direccion)) {
            $carpeta['direccion_entrega'] = $direccion;
        }

        $usuarioModel = $this->model('Usuario');
        $usuarios = $usuarioModel->obtenerTodos();

        $this->view('eventos/editar_carpeta', [
            'registro'                => $registro,
            'carpeta'                 => $carpeta,
            'evento'                  => $evento,
            'ordenes'                 => $ordenes,
            'presidium'               => $presidium,
            'invitadosList'           => $invitadosList,
            'modulosList'             => $modulosList,
            'internos'                => $internos,
            'externos'                => $externos,
            'tiposPresidium'          => $tiposPresidium,
            'tipoPresidiumSeleccionado' => $tipoPresidiumSeleccionado,
            'insumosInternos'         => $insumosInternos,
            'insumosExternos'         => $insumosExternos,
            'responsableEvento'       => $responsableEvento,
            'usuario'                 => $usuarioModel->obtenerPorId($_SESSION['usuario_id']),
            'usuarios'                => $usuarios,
            'objetivo_pbrm'           => $objetivo_pbrm,
            'responsable_solo_nombre' => $responsable_solo_nombre
        ]);
    }

    // ============================================================
    // 3. GUARDAR CARPETA
    // ============================================================
    public function guardar_carpeta()
    {
        header('Content-Type: application/json');
        $data = $_POST;

        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            return;
        }

        $data['objetivo'] = $data['objetivo_evento'] ?? ($data['objetivo'] ?? '');
        $data['invitados_especiales'] = $data['invitados'] ?? [];
        $data['modulos_jornada'] = $data['modulos'] ?? [];

        // Tipo de presídium
        $tipoPresidiumNombre = $data['tipo_presidium_seleccionado'] ?? 'lineal';
        $tipoPresidiumModel = $this->model('TipoPresidium');
        $tipoPresidiumId = 1;
        $tipos = $tipoPresidiumModel->obtenerTodos();
        foreach ($tipos as $tp) {
            if (strtolower($tp['nombre_tipo']) == strtolower($tipoPresidiumNombre)) {
                $tipoPresidiumId = $tp['id'];
                break;
            }
        }
        $data['tipo_presidium'] = $tipoPresidiumId;

        // Decodificar presidium_data
        $data['presidium'] = isset($data['presidium_data'])
            ? (json_decode($data['presidium_data'], true) ?: [])
            : [];

        if (empty($data['nombre_evento'])) {
            echo json_encode(['success' => false, 'error' => 'El nombre del evento es obligatorio']);
            return;
        }

        $carpetaModel  = $this->model('Carpeta');
        $eventoModel   = $this->model('EventoDetalle');
        $ordenModel    = $this->model('OrdenDelDia');
        $presidiumModel = $this->model('PresidiumAsistente');

        $carpetaId = $data['carpeta_id'] ?? null;
        $registroId = $data['registro_actividad_id'] ?? 0;
        $eventoId = $data['evento_id'] ?? null;

        $estado = $data['estado'] ?? 'pendiente';
        $usuario = $this->model('Usuario')->obtenerPorId($_SESSION['usuario_id']);
        $rolId = $usuario['rol_id'];
        $esAdmin = ($rolId == 1);
        $esJefe = ($rolId == 2 || $rolId == 5);

        $carpetaExistente = null;
        if ($carpetaId) {
            $carpetaExistente = $carpetaModel->obtenerPorId($carpetaId);
        }
        $esCreador = ($carpetaExistente && $carpetaExistente['realizo_id'] == $_SESSION['usuario_id']) || !$carpetaId;

        if (!$esAdmin) {
            if ($esCreador && !in_array($estado, ['pendiente', 'entregado'])) {
                echo json_encode(['success' => false, 'error' => 'Solo puedes marcar como "Pendiente" o "Entregado"']);
                return;
            }
            if ($esJefe && !$esCreador && !in_array($estado, ['revisado', 'aprobado', 'fuera_tiempo'])) {
                echo json_encode(['success' => false, 'error' => 'No tienes permiso para ese estado']);
                return;
            }
            if ($estado == 'fuera_tiempo' && empty($data['justificacion_fuera_tiempo'])) {
                echo json_encode(['success' => false, 'error' => 'Debes proporcionar una justificación']);
                return;
            }
            if (!$esCreador && !$esJefe) {
                echo json_encode(['success' => false, 'error' => 'No tienes permiso para modificar esta carpeta']);
                return;
            }
        }

        // Subida de archivos
        $uploadDir = 'uploads/eventos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        function subirArchivo($campo, $dir, $nombreBase) {
            if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION);
                $nombre = $nombreBase . '_' . time() . '.' . $ext;
                $ruta = $dir . $nombre;
                if (move_uploaded_file($_FILES[$campo]['tmp_name'], $ruta)) {
                    return $ruta;
                }
            }
            return null;
        }

        $imagenFondo = subirArchivo('imagen_fondo', $uploadDir, 'fondo');
        $imagenLugar = subirArchivo('imagen_lugar', $uploadDir, 'lugar');
        $imagenMaps = subirArchivo('imagen_maps', $uploadDir, 'maps');
        $imagenCroquis = subirArchivo('imagen_croquis', $uploadDir, 'croquis');
        $logo = subirArchivo('logo_toluca', $uploadDir, 'logo');

        // Guardar carpeta
        $carpetaDatos = [
            'id'                         => $carpetaId,
            'registro_actividad_id'      => $registroId,
            'logo_toluca'                => $logo ?? $data['logo_toluca'] ?? null,
            'direccion_entrega'          => $data['direccion_entrega'] ?? null,
            'link_mapa'                  => $data['link_mapa'] ?? null,
            'fecha_entrega'              => $data['fecha_entrega'] ?? date('Y-m-d'),
            'realizo_id'                 => $_SESSION['usuario_id'],
            'autorizado_por_id'          => $data['autorizado_por_id'] ?? null,
            'firma'                      => $data['firma'] ?? null,
            'estado'                     => $estado,
            'justificacion_fuera_tiempo' => $data['justificacion_fuera_tiempo'] ?? null
        ];
        $nuevoId = $carpetaModel->guardar($carpetaDatos);
        if (!$nuevoId) {
            echo json_encode(['success' => false, 'error' => 'Error al guardar carpeta']);
            return;
        }
        if (!$carpetaId) {
            $carpetaId = $nuevoId;
        }

        // Guardar/actualizar evento_detalle
        if (!$eventoId && $carpetaId) {
            $existente = $eventoModel->obtenerPorCarpetaIdCompleto($carpetaId);
            if ($existente) {
                $eventoId = $existente['id'];
            }
        }

        $eventoDatos = [
            'id'                     => $eventoId,
            'carpeta_id'             => $carpetaId,
            'nombre_evento'          => $data['nombre_evento'] ?? '',
            'fecha_evento'           => $data['fecha_evento'] ?? date('Y-m-d'),
            'objetivo'               => $data['objetivo'] ?? '',
            'justificacion'          => $data['justificacion'] ?? '',
            'vestimenta'             => $data['vestimenta'] ?? '',
            'imagen_diseno'          => $data['imagen_diseno'] ?? null,
            'descripcion_meta'       => $data['descripcion_meta'] ?? '',
            'link_mapa'              => $data['link_mapa'] ?? null,
            'imagen_fondo'           => $imagenFondo ?? $data['imagen_fondo_actual'] ?? null,
            'imagen_lugar'           => $imagenLugar ?? $data['imagen_lugar_actual'] ?? null,
            'imagen_maps'            => $imagenMaps ?? $data['imagen_maps_actual'] ?? null,
            'imagen_croquis'         => $imagenCroquis ?? $data['imagen_croquis_actual'] ?? null,
            'aprobado_por'           => $data['aprobado_por'] ?? 'MTRA. ANDREA MA. DEL ROCÍO MERLOS NÁJERA',
            'responsable_evento'     => $data['responsable_evento'] ?? '',
            'coordinacion_evento'    => $data['coordinacion_evento'] ?? '',
            'maestra_ceremonias'     => $data['maestra_ceremonias'] ?? '',
            'num_spots'              => (int)($data['num_spots'] ?? 5),
            'invitados_especiales'   => json_encode($data['invitados_especiales'] ?? []),
            'modulos_jornada'        => json_encode($data['modulos_jornada'] ?? []),
            'requerimientos_internos'=> json_encode($data['req_internos'] ?? []),
            'requerimientos_externos'=> json_encode($data['req_externos'] ?? []),
            'comunicacion_social'    => $data['comunicacion_social'] ?? '',
            'delegacion_admin_resumen' => $data['delegacion_admin_resumen'] ?? '',
            'fecha_entrega'          => $data['fecha_entrega'] ?? date('Y-m-d'),
            'firma'                  => $data['firma'] ?? null
        ];

        $eventoGuardado = $eventoModel->guardar($eventoDatos);
        if (!$eventoGuardado) {
            echo json_encode(['success' => false, 'error' => 'Error al guardar evento']);
            return;
        }
        if (!$eventoId) {
            $eventoId = $eventoGuardado;
        }

        // Guardar orden del día
        $ordenes = [];
        if (isset($data['orden']) && is_array($data['orden'])) {
            foreach ($data['orden'] as $o) {
                if (isset($o['hora_inicio']) && isset($o['duracion'])) {
                    $horaInicio = $o['hora_inicio'];
                    $duracion = (int)$o['duracion'];
                    $horaFin = date('H:i:s', strtotime("$horaInicio + $duracion minutes"));

                    $responsableId = null;
                    $otroResponsable = null;
                    if (isset($o['responsable_id'])) {
                        if ($o['responsable_id'] === 'otro' || $o['responsable_id'] === '') {
                            $otroResponsable = $o['otro_responsable'] ?? '';
                        } else {
                            $responsableId = (int)$o['responsable_id'] ?: null;
                        }
                    }

                    $ordenes[] = [
                        'hora_inicio'      => $horaInicio,
                        'hora_fin'         => $horaFin,
                        'actividad'        => $o['actividad'] ?? '',
                        'responsable_id'   => $responsableId,
                        'otro_responsable' => $otroResponsable
                    ];
                }
            }
        }
        $ordenModel->guardarMultiple($eventoId, $ordenes);

        // Guardar presídium (solo con nombre)
        $presidiumList = [];
        if (isset($data['presidium']) && is_array($data['presidium'])) {
            foreach ($data['presidium'] as $p) {
                if ($p['orden'] !== '*' && !empty($p['nombre'])) {
                    $presidiumList[] = [
                        'tipo_presidium_id' => $tipoPresidiumId,
                        'nombre_invitado'   => $p['nombre'],
                        'cargo_invitado'    => $p['cargo'] ?? ''
                    ];
                }
            }
        }
        $presidiumModel->guardarMultiple($eventoId, $presidiumList);

        // Historial
        $historialModel = $this->model('HistorialCarpeta');
        $historialModel->guardar([
            'carpeta_id'     => $carpetaId,
            'usuario_id'     => $_SESSION['usuario_id'],
            'accion'         => 'guardar',
            'estado_anterior'=> $carpetaExistente ? $carpetaExistente['estado'] : null,
            'estado_nuevo'   => $estado,
            'comentario'     => 'Carpeta guardada/modificada'
        ]);

        echo json_encode([
            'success'    => true,
            'carpeta_id' => $carpetaId,
            'evento_id'  => $eventoId
        ]);
    }

    // ============================================================
    // 4. REVISIÓN DE CARPETAS
    // ============================================================
    public function revision()
    {
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        $unidadId = $usuario['unidad_administrativa_id'] ?? null;
        
        $carpetaModel = $this->model('Carpeta');
        $carpetas = $carpetaModel->obtenerPendientesRevision($unidadId);
        
        $this->view('eventos/revision', [
            'carpetas' => $carpetas,
            'usuario'  => $usuario
        ]);
    }

    // ============================================================
    // 5. VER DETALLE DE CARPETA
    // ============================================================
    public function ver_carpeta($id = null)
    {
        if ($id === null) {
            $id = $_GET['id_registro'] ?? null;
        }
        if (!$id) {
            die('ID de carpeta no especificado');
        }
        
        $carpetaModel = $this->model('Carpeta');
        $carpeta = $carpetaModel->obtenerConTodo($id);
        if (!$carpeta) {
            die('Carpeta no encontrada');
        }
        
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        $rolId = $usuario['rol_id'];
        $esAdmin = ($rolId == 1);
        $esJefe = ($rolId == 2 || $rolId == 5);
        
        if (!$esAdmin && !$esJefe) {
            die('No tienes permiso para ver esta carpeta');
        }
        
        if ($esJefe && $usuario['unidad_administrativa_id'] != $carpeta['unidad_administrativa_id']) {
            die('No tienes permiso para ver esta carpeta');
        }
        
        $this->view('eventos/revision_detalle', [
            'carpeta' => $carpeta,
            'usuario' => $usuario
        ]);
    }

    // ============================================================
    // 6. SUBIR EVIDENCIA
    // ============================================================
    public function subir_evidencia()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        $registroId = $_POST['registro_id'] ?? 0;
        if (!$registroId) {
            echo json_encode(['success' => false, 'error' => 'ID de registro no proporcionado']);
            return;
        }

        $estadoActual = $_POST['estado_actual'] ?? 'pendiente';
        $justificacion = $_POST['justificacion'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;

        $registroModel = $this->model('RegistroActividad');
        $registro = $registroModel->obtenerRegistroCompletoPorId($registroId);
        if (!$registro) {
            echo json_encode(['success' => false, 'error' => 'Registro no encontrado']);
            return;
        }

        $hoy = new DateTime();
        $fechaFinObj = new DateTime($registro['fecha_fin'] ?? $registro['fecha_inicio']);
        $diff = $hoy->diff($fechaFinObj);
        $dias = (int)$diff->format('%r%a');
        $esFueraTiempo = ($dias > 3 && $hoy > $fechaFinObj);

        $carpetaModel = $this->model('Carpeta');
        $carpeta = $carpetaModel->obtenerPorRegistroActividadId($registroId);
        $carpetaId = $carpeta['id'] ?? null;

        if ($esFueraTiempo && ($estadoActual != 'justificado' && $estadoActual != 'entregado')) {
            if (empty($justificacion)) {
                echo json_encode(['success' => false, 'error' => 'Debes escribir una justificación para la entrega fuera de tiempo']);
                return;
            }
            
            $datosCarpeta = [
                'id' => $carpetaId,
                'registro_actividad_id' => $registroId,
                'logo_toluca' => $carpeta['logo_toluca'] ?? null,
                'direccion_entrega' => $carpeta['direccion_entrega'] ?? null,
                'link_mapa' => $carpeta['link_mapa'] ?? null,
                'fecha_entrega' => date('Y-m-d'),
                'realizo_id' => $_SESSION['usuario_id'],
                'autorizado_por_id' => null,
                'firma' => $carpeta['firma'] ?? null,
                'estado' => 'fuera_tiempo',
                'justificacion_fuera_tiempo' => $justificacion
            ];
            
            $resultadoGuardado = $carpetaModel->guardar($datosCarpeta);
            if ($resultadoGuardado) {
                $carpetaId = $carpetaId ?: $resultadoGuardado;
                $historialModel = $this->model('HistorialCarpeta');
                $historialModel->guardar([
                    'carpeta_id' => $carpetaId,
                    'usuario_id' => $_SESSION['usuario_id'],
                    'accion' => 'justificacion_fuera_tiempo',
                    'estado_nuevo' => 'fuera_tiempo',
                    'comentario' => $justificacion
                ]);

                $unidadId = $registro['unidad_administrativa_id'] ?? null;
                if ($unidadId) {
                    $unidadJefeModel = $this->model('UnidadJefe');
                    $jefe = $unidadJefeModel->obtenerJefeActual($unidadId);
                    if ($jefe && !empty($jefe['usuario_id'])) {
                        $notifModel = $this->model('Notificacion');
                        $notifModel->crear([
                            'usuario_id' => $jefe['usuario_id'],
                            'carpeta_id' => $carpetaId,
                            'mensaje' => "El empleado {$registro['usuario_nombre']} ha subido una justificación fuera de tiempo para la actividad '{$registro['actividad_desc']}'."
                        ]);
                    }
                }

                echo json_encode(['success' => true, 'mensaje' => 'Justificación guardada. Espera la validación de tu jefe.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al guardar la justificación']);
            }
            return;
        }

        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'Debes seleccionar un archivo']);
            return;
        }

        $archivo = $_FILES['archivo'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'evidencia_' . $registroId . '_' . time() . '.' . $extension;
        $rutaDestino = 'uploads/evidencias/' . $nombreArchivo;

        if (!is_dir('uploads/evidencias')) {
            mkdir('uploads/evidencias', 0777, true);
        }

        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']);
            return;
        }

        $nuevoEstado = 'entregado';
        if ($estadoActual === 'revisado') {
            $nuevoEstado = 'entregado';
        }

        $datosCarpeta = [
            'id' => $carpetaId,
            'registro_actividad_id' => $registroId,
            'logo_toluca' => $carpeta['logo_toluca'] ?? null,
            'direccion_entrega' => $carpeta['direccion_entrega'] ?? null,
            'link_mapa' => $carpeta['link_mapa'] ?? null,
            'fecha_entrega' => date('Y-m-d'),
            'realizo_id' => $_SESSION['usuario_id'],
            'autorizado_por_id' => null,
            'firma' => $rutaDestino,
            'estado' => $nuevoEstado,
            'justificacion_fuera_tiempo' => $carpeta['justificacion_fuera_tiempo'] ?? null
        ];

        $resultadoGuardado = $carpetaModel->guardar($datosCarpeta);
        if ($resultadoGuardado) {
            $carpetaId = $carpetaId ?: $resultadoGuardado;
            $historialModel = $this->model('HistorialCarpeta');
            $historialModel->guardar([
                'carpeta_id' => $carpetaId,
                'usuario_id' => $_SESSION['usuario_id'],
                'accion' => 'subida_archivo',
                'estado_nuevo' => $nuevoEstado,
                'comentario' => 'Archivo subido: ' . $nombreArchivo
            ]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al guardar en la base de datos']);
        }
    }

    // ============================================================
    // 7. CAMBIAR ESTADO (AJAX)
    // ============================================================
    public function cambiar_estado()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['carpeta_id']) || empty($data['estado'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $carpetaId = $data['carpeta_id'];
        $estado = $data['estado'];
        $observaciones = $data['observaciones'] ?? null;
        $motivo = $data['motivo'] ?? null;

        $carpetaModel = $this->model('Carpeta');
        $carpeta = $carpetaModel->obtenerPorId($carpetaId);
        if (!$carpeta) {
            echo json_encode(['success' => false, 'error' => 'Carpeta no encontrada']);
            return;
        }

        $result = $carpetaModel->actualizarEstado($carpetaId, $estado);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
        }
    }

    // ============================================================
    // 8. APROBAR CARPETA
    // ============================================================
    public function aprobar_carpeta($id)
    {
        $carpetaModel = $this->model('Carpeta');
        $carpeta = $carpetaModel->obtenerPorId($id);
        if (!$carpeta) {
            die('Carpeta no encontrada');
        }
        $estadoAnterior = $carpeta['estado'];
        $carpeta['estado'] = 'aprobado';
        $carpeta['autorizado_por_id'] = $_SESSION['usuario_id'];

        if ($carpetaModel->guardar($carpeta)) {
            $historialModel = $this->model('HistorialCarpeta');
            $historialModel->guardar([
                'carpeta_id' => $id,
                'usuario_id' => $_SESSION['usuario_id'],
                'accion' => 'aprobar',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'aprobado'
            ]);
            header('Location: /Dir_bienestar/eventos/revision?mensaje=aprobado');
            exit;
        } else {
            die('Error al aprobar la carpeta');
        }
    }

    // ============================================================
    // 9. VALIDAR JUSTIFICACIÓN
    // ============================================================
    public function validar_justificacion($id)
    {
        $carpetaModel = $this->model('Carpeta');
        $carpeta = $carpetaModel->obtenerPorId($id);
        if (!$carpeta) {
            die('Carpeta no encontrada');
        }
        
        if (empty($carpeta['justificacion_fuera_tiempo'])) {
            die('Esta carpeta no tiene justificación para validar');
        }
        
        $estadoAnterior = $carpeta['estado'];
        $carpeta['estado'] = 'justificado';
        $carpeta['autorizado_por_id'] = $_SESSION['usuario_id'];
        
        if ($carpetaModel->guardar($carpeta)) {
            $historialModel = $this->model('HistorialCarpeta');
            $historialModel->guardar([
                'carpeta_id' => $id,
                'usuario_id' => $_SESSION['usuario_id'],
                'accion' => 'validar_justificacion',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'justificado'
            ]);
            header('Location: /Dir_bienestar/eventos/revision?mensaje=justificacion_validada');
            exit;
        } else {
            die('Error al validar la justificación');
        }
    }

    // ============================================================
    // 10. SOLICITAR CORRECCIONES
    // ============================================================
    public function solicitar_correcciones()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $carpetaId = $_POST['carpeta_id'] ?? null;
            $diapositiva = $_POST['diapositiva'] ?? '';
            $comentario = $_POST['comentario'] ?? '';
        } else {
            $carpetaId = func_get_arg(0) ?? $_GET['id'] ?? null;
            $diapositiva = '';
            $comentario = '';
        }

        if (!$carpetaId) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['success' => false, 'error' => 'ID de carpeta no proporcionado']);
            } else {
                die('ID de carpeta no especificado');
            }
            return;
        }

        $carpetaModel = $this->model('Carpeta');
        $carpeta = $carpetaModel->obtenerPorId($carpetaId);
        if (!$carpeta) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['success' => false, 'error' => 'Carpeta no encontrada']);
            } else {
                die('Carpeta no encontrada');
            }
            return;
        }

        $estadoAnterior = $carpeta['estado'];
        $carpeta['estado'] = 'revisado';

        if ($carpetaModel->guardar($carpeta)) {
            $historialModel = $this->model('HistorialCarpeta');
            $historialModel->guardar([
                'carpeta_id' => $carpetaId,
                'usuario_id' => $_SESSION['usuario_id'],
                'accion' => 'solicitar_correcciones',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'revisado',
                'comentario' => $comentario,
                'diapositiva' => $diapositiva
            ]);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['success' => true]);
            } else {
                header('Location: /Dir_bienestar/eventos/revision?mensaje=correcciones');
                exit;
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['success' => false, 'error' => 'Error al solicitar correcciones']);
            } else {
                die('Error al solicitar correcciones');
            }
        }
    }

    // ============================================================
    // 11. RECHAZAR CARPETA
    // ============================================================
    public function rechazar_carpeta($id)
    {
        $carpetaModel = $this->model('Carpeta');
        $carpeta = $carpetaModel->obtenerPorId($id);
        if (!$carpeta) {
            die('Carpeta no encontrada');
        }
        $estadoAnterior = $carpeta['estado'];
        $carpeta['estado'] = 'fuera_tiempo';

        if ($carpetaModel->guardar($carpeta)) {
            $historialModel = $this->model('HistorialCarpeta');
            $historialModel->guardar([
                'carpeta_id' => $id,
                'usuario_id' => $_SESSION['usuario_id'],
                'accion' => 'rechazar',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'fuera_tiempo'
            ]);
            header('Location: /Dir_bienestar/eventos/revision?mensaje=rechazado');
            exit;
        } else {
            die('Error al rechazar la carpeta');
        }
    }

    // ============================================================
    // 12. OBTENER HISTORIAL (JSON)
    // ============================================================
    public function historial($carpetaId)
    {
        header('Content-Type: application/json');
        $historialModel = $this->model('HistorialCarpeta');
        $registros = $historialModel->obtenerPorCarpeta($carpetaId);
        echo json_encode($registros);
    }
}