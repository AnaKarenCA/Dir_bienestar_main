<?php

class EmpleadosController extends Controller
{
    public function __construct()
    {
        // Verificar sesión
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }
        // Verificar rol (2 = Jefe, 5 = Coordinador)
        $rol = $_SESSION['usuario_rol_id'] ?? 0;
        if (!in_array($rol, [2, 5])) {
            die('Acceso denegado. Tu rol es: ' . $rol . '. Se requieren roles 2 o 5.');
        }
    }

    /**
     * Lista de empleados de la unidad del usuario logueado
     */
    public function index()
    {
        $unidadId = $_SESSION['usuario_unidad_id'] ?? 0;
        $usuarioModel = $this->model('Usuario');
        // Excluir al propio usuario (para que no aparezca en la lista)
        $empleados = $usuarioModel->obtenerPorUnidad($unidadId, $_SESSION['usuario_id']);
        $this->view('empleados/index', ['empleados' => $empleados]);
    }

    /**
     * Formulario para agregar empleado
     */
    public function agregar()
    {
        $rolModel = $this->model('Rol');
        $roles = $rolModel->obtenerTodos();
        $rolesPermitidos = $this->filtrarRolesPermitidos($roles);
        $this->view('empleados/agregar', ['roles' => $rolesPermitidos]);
    }

    /**
     * Guardar nuevo empleado
     */
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Dir_bienestar/empleados');
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $puesto = trim($_POST['puesto'] ?? '');
        $rol_id = (int)($_POST['rol_id'] ?? 0);
        $clave = $_POST['clave'] ?? '';
        $estatus = $_POST['estatus'] ?? 'Activo';

        $errores = [];
        if (empty($nombre)) $errores[] = 'El nombre es obligatorio.';
        if (empty($correo)) $errores[] = 'El correo es obligatorio.';
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'Correo inválido.';
        if ($rol_id <= 0) $errores[] = 'Debe seleccionar un rol.';
        if (empty($clave)) $errores[] = 'La contraseña es obligatoria.';
        if (strlen($clave) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres.';

        $usuarioModel = $this->model('Usuario');
        if ($usuarioModel->buscarPorCorreo($correo)) {
            $errores[] = 'El correo ya está registrado.';
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            header('Location: /Dir_bienestar/empleados/agregar');
            exit;
        }

        $unidadId = $_SESSION['usuario_unidad_id'] ?? null;
        $datos = [
            'nombre' => $nombre,
            'correo' => $correo,
            'clave' => $clave,
            'puesto' => $puesto,
            'rol_id' => $rol_id,
            'unidad_administrativa_id' => $unidadId,
            'estatus' => $estatus
        ];
        if ($usuarioModel->crear($datos)) {
            $_SESSION['mensaje'] = 'Empleado agregado correctamente.';
        } else {
            $_SESSION['errores'] = ['Error al guardar el empleado.'];
        }

        header('Location: /Dir_bienestar/empleados');
        exit;
    }

    /**
     * Formulario para editar empleado
     */
    public function editar($id)
    {
        $usuarioModel = $this->model('Usuario');
        $empleado = $usuarioModel->obtenerPorId($id);
        if (!$empleado || $empleado['unidad_administrativa_id'] != $_SESSION['usuario_unidad_id']) {
            die('No tienes permiso para editar este empleado.');
        }

        $rolModel = $this->model('Rol');
        $roles = $rolModel->obtenerTodos();
        $rolesPermitidos = $this->filtrarRolesPermitidos($roles);

        $this->view('empleados/editar', [
            'empleado' => $empleado,
            'roles' => $rolesPermitidos
        ]);
    }

    /**
     * Actualizar empleado
     */
    public function actualizar($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Dir_bienestar/empleados');
            exit;
        }

        $usuarioModel = $this->model('Usuario');
        $empleado = $usuarioModel->obtenerPorId($id);
        if (!$empleado || $empleado['unidad_administrativa_id'] != $_SESSION['usuario_unidad_id']) {
            die('No tienes permiso para editar este empleado.');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $puesto = trim($_POST['puesto'] ?? '');
        $rol_id = (int)($_POST['rol_id'] ?? 0);
        $estatus = $_POST['estatus'] ?? 'Activo';
        $nuevaClave = $_POST['nueva_clave'] ?? '';

        $errores = [];
        if (empty($nombre)) $errores[] = 'El nombre es obligatorio.';
        if (empty($correo)) $errores[] = 'El correo es obligatorio.';
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'Correo inválido.';
        if ($rol_id <= 0) $errores[] = 'Debe seleccionar un rol.';

        $existente = $usuarioModel->buscarPorCorreo($correo);
        if ($existente && $existente['id'] != $id) {
            $errores[] = 'El correo ya está registrado.';
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            header('Location: /Dir_bienestar/empleados/editar/' . $id);
            exit;
        }

        $datos = [
            'nombre' => $nombre,
            'correo' => $correo,
            'puesto' => $puesto,
            'rol_id' => $rol_id,
            'unidad_administrativa_id' => $_SESSION['usuario_unidad_id'],
            'estatus' => $estatus
        ];
        if ($usuarioModel->actualizarUsuario($id, $datos)) {
            if (!empty($nuevaClave) && strlen($nuevaClave) >= 6) {
                $usuarioModel->cambiarContrasenaAdmin($id, $nuevaClave);
            }
            $_SESSION['mensaje'] = 'Empleado actualizado correctamente.';
        } else {
            $_SESSION['errores'] = ['Error al actualizar.'];
        }

        header('Location: /Dir_bienestar/empleados');
        exit;
    }

    /**
     * Bloquear/Desbloquear empleado (toggle)
     */
    public function toggle($id)
    {
        $usuarioModel = $this->model('Usuario');
        $empleado = $usuarioModel->obtenerPorId($id);
        if (!$empleado || $empleado['unidad_administrativa_id'] != $_SESSION['usuario_unidad_id']) {
            die('No tienes permiso.');
        }

        if ($empleado['estatus'] === 'Activo') {
            $usuarioModel->bloquear($id);
            $_SESSION['mensaje'] = 'Empleado bloqueado.';
        } else {
            $usuarioModel->desbloquear($id);
            $_SESSION['mensaje'] = 'Empleado desbloqueado.';
        }

        header('Location: /Dir_bienestar/empleados');
        exit;
    }

    /**
     * Filtra roles que el usuario actual puede asignar
     */
    private function filtrarRolesPermitidos($roles)
    {
        $rolActual = $_SESSION['usuario_rol_id'];
        $superiores = [2, 5]; // Jefe y Coordinador no pueden asignar roles de su mismo nivel
        $permitidos = [];
        foreach ($roles as $rol) {
            // Permitir solo roles que no sean superiores (y que no sean el mismo rol del usuario)
            if (!in_array($rol['id'], $superiores) || $rol['id'] == $rolActual) {
                if ($rol['id'] == $rolActual) continue;
                $permitidos[] = $rol;
            }
        }
        return $permitidos;
    }
}