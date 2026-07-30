<?php

class AdminController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }

        if ($_SESSION['usuario_rol_id'] != 1) {
            die('Acceso denegado. Solo administradores.');
        }
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function dashboard()
    {
        $adminModel = $this->model('Admin');

        $indicadores = $adminModel->obtenerIndicadores();
        $ultimosUsuarios = $adminModel->obtenerUltimosUsuarios();
        $ultimasActividades = $adminModel->obtenerUltimasActividades();
        $ultimosEventos = $adminModel->obtenerUltimosEventos();
        $inventarioBajo = $adminModel->obtenerInventarioBajo();

        $this->view('admin/dashboard', [
            'indicadores'        => $indicadores,
            'ultimosUsuarios'    => $ultimosUsuarios,
            'ultimasActividades' => $ultimasActividades,
            'ultimosEventos'     => $ultimosEventos,
            'inventarioBajo'     => $inventarioBajo
        ]);
    }

    // ==========================================
    // GESTIÓN DE USUARIOS (con AJAX y filtros)
    // ==========================================

    public function usuarios()
    {
        $rolModel = $this->model('Rol');
        $unidadModel = $this->model('UnidadAdministrativa');

        $roles = $rolModel->obtenerTodos();
        $unidades = $unidadModel->obtenerTodas();

        $this->view('admin/usuarios', [
            'roles' => $roles,
            'unidades' => $unidades,
            'estatuses' => ['Activo', 'Inactivo', 'Bloqueado']
        ]);
    }

public function usuarios_data()
{
    header('Content-Type: application/json');

    try {
        $filtros = [
            'busqueda'  => $_GET['busqueda'] ?? '',
            'rol_id'    => $_GET['rol_id'] ?? null,
            'unidad_id' => $_GET['unidad_id'] ?? null,
            'estatus'   => $_GET['estatus'] ?? null,
            'orden'     => $_GET['orden'] ?? 'u.nombre',
            'direccion' => $_GET['direccion'] ?? 'ASC',
            'limite'    => (int)($_GET['limite'] ?? 20),
            'offset'    => (int)($_GET['offset'] ?? 0)
        ];

        error_log('Filtros: ' . print_r($filtros, true));

        $usuarioModel = $this->model('Usuario');
        if (!$usuarioModel) {
            throw new Exception('Modelo Usuario no encontrado');
        }

        $usuarios = $usuarioModel->obtenerConFiltros($filtros);
        $total = $usuarioModel->contarConFiltros($filtros);

        error_log('Usuarios obtenidos: ' . print_r($usuarios, true));
        error_log('Total: ' . $total);

        echo json_encode([
            'data' => $usuarios,
            'total' => $total,
            'filtros' => $filtros
        ]);
    } catch (Exception $e) {
        error_log('Error en usuarios_data: ' . $e->getMessage());
        echo json_encode([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

    public function obtener_usuario($id)
    {
        header('Content-Type: application/json');
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($id);
        if (!$usuario) {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            return;
        }
        echo json_encode(['success' => true, 'usuario' => $usuario]);
    }

    public function crear_usuario()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            return;
        }

        if (empty($data['nombre']) || empty($data['correo']) || empty($data['clave'])) {
            echo json_encode(['success' => false, 'error' => 'Nombre, correo y contraseña son obligatorios']);
            return;
        }
        if ($data['clave'] !== $data['confirmar_clave']) {
            echo json_encode(['success' => false, 'error' => 'Las contraseñas no coinciden']);
            return;
        }
        if (strlen($data['clave']) < 6) {
            echo json_encode(['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }

        $usuarioModel = $this->model('Usuario');
        $result = $usuarioModel->crear($data);
        echo json_encode(['success' => $result]);
    }

    public function actualizar_usuario($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            return;
        }
        if (empty($data['nombre']) || empty($data['correo'])) {
            echo json_encode(['success' => false, 'error' => 'Nombre y correo son obligatorios']);
            return;
        }

        $usuarioModel = $this->model('Usuario');
        $result = $usuarioModel->actualizarUsuario($id, $data);
        echo json_encode(['success' => $result]);
    }

    public function cambiar_contrasena_usuario($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            return;
        }
        if (empty($data['nueva']) || empty($data['confirmar'])) {
            echo json_encode(['success' => false, 'error' => 'Ambos campos son obligatorios']);
            return;
        }
        if ($data['nueva'] !== $data['confirmar']) {
            echo json_encode(['success' => false, 'error' => 'Las contraseñas no coinciden']);
            return;
        }
        if (strlen($data['nueva']) < 6) {
            echo json_encode(['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }

        $usuarioModel = $this->model('Usuario');
        $result = $usuarioModel->cambiarContrasenaAdmin($id, $data['nueva']);
        echo json_encode(['success' => $result]);
    }

    public function bloquear_usuario($id)
    {
        header('Content-Type: application/json');
        $usuarioModel = $this->model('Usuario');
        $result = $usuarioModel->bloquear($id);
        echo json_encode(['success' => $result]);
    }

    public function desbloquear_usuario($id)
    {
        header('Content-Type: application/json');
        $usuarioModel = $this->model('Usuario');
        $result = $usuarioModel->desbloquear($id);
        echo json_encode(['success' => $result]);
    }

    // ==========================================
    // INVENTARIO (CRUD)
    // ==========================================

    public function inventario()
    {
        $inventarioModel = $this->model('InventarioInsumo');
        $insumos = $inventarioModel->obtenerTodos(true);
        $this->view('admin/inventario', ['insumos' => $insumos]);
    }

    public function agregar_inventario()
    {
        $this->view('admin/inventario_agregar');
    }

    public function guardar_inventario()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            return;
        }
        $model = $this->model('InventarioInsumo');
        $result = $model->agregar($data);
        echo json_encode(['success' => $result]);
    }

    public function editar_inventario($id)
    {
        $inventarioModel = $this->model('InventarioInsumo');
        $insumo = $inventarioModel->obtenerPorId($id);
        if (!$insumo) {
            die('Insumo no encontrado');
        }
        $this->view('admin/inventario_editar', ['insumo' => $insumo]);
    }

    public function actualizar_inventario($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            return;
        }
        $model = $this->model('InventarioInsumo');
        $result = $model->actualizar($id, $data);
        echo json_encode(['success' => $result]);
    }

    public function eliminar_inventario($id)
    {
        header('Content-Type: application/json');
        $model = $this->model('InventarioInsumo');
        $result = $model->eliminar($id);
        echo json_encode(['success' => $result]);
    }

    public function restaurar_inventario($id)
    {
        header('Content-Type: application/json');
        $model = $this->model('InventarioInsumo');
        $result = $model->restaurar($id);
        echo json_encode(['success' => $result]);
    }

    public function inventario_inactivos()
    {
        $inventarioModel = $this->model('InventarioInsumo');
        $insumos = $inventarioModel->obtenerTodos(false);
        $inactivos = array_filter($insumos, function ($i) {
            return $i['activo'] == 0;
        });
        $this->view('admin/inventario_inactivos', ['insumos' => $inactivos]);
    }
        // ==========================================
    // GESTIÓN DE TIPOS DE ENTREGABLE
    // ==========================================

    public function tipos_entregable()
    {
        $tipoModel = $this->model('TipoEntregable');
        $tipos = $tipoModel->obtenerTodos();

        // Contar usos de cada tipo
        foreach ($tipos as &$tipo) {
            $tipo['usos'] = $tipoModel->contarUsos($tipo['id']);
        }

        $this->view('admin/tipos_entregable/index', [
            'tipos' => $tipos
        ]);
    }

    public function agregar_tipo_entregable()
    {
        $this->view('admin/tipos_entregable/agregar');
    }

    public function guardar_tipo_entregable()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['nombre_entregable'])) {
            echo json_encode(['success' => false, 'error' => 'El nombre es obligatorio']);
            return;
        }

        $model = $this->model('TipoEntregable');
        $result = $model->agregar($data['nombre_entregable']);

        echo json_encode(['success' => $result]);
    }

    public function editar_tipo_entregable($id)
    {
        $tipoModel = $this->model('TipoEntregable');
        $tipo = $tipoModel->obtenerPorId($id);

        if (!$tipo) {
            die('Tipo de entregable no encontrado');
        }

        $this->view('admin/tipos_entregable/editar', [
            'tipo' => $tipo
        ]);
    }

    public function actualizar_tipo_entregable($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['nombre_entregable'])) {
            echo json_encode(['success' => false, 'error' => 'El nombre es obligatorio']);
            return;
        }

        // Verificar si ya existe otro con el mismo nombre
        $model = $this->model('TipoEntregable');
        $todos = $model->obtenerTodos();
        foreach ($todos as $t) {
            if ($t['id'] != $id && strtolower($t['nombre_entregable']) == strtolower($data['nombre_entregable'])) {
                echo json_encode(['success' => false, 'error' => 'Ya existe un tipo de entregable con ese nombre']);
                return;
            }
        }

        $result = $model->actualizar($id, $data['nombre_entregable']);
        echo json_encode(['success' => $result]);
    }

}