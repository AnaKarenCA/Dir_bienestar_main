<?php

class AuthController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = $this->model('Usuario');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $correo = trim($_POST['correo']);
            $password = $_POST['password'];

            $usuario = $this->usuarioModel->buscarPorCorreo($correo);

            if (!$usuario)
            {
                $error = "Usuario no encontrado";
                $this->view('auth/login', compact('error'));
                return;
            }

            if ($usuario['estatus'] !== 'Activo')
            {
                $error = "Usuario inactivo o bloqueado";
                $this->view('auth/login', compact('error'));
                return;
            }

            if (!password_verify($password, $usuario['clave']))
            {
                $error = "Contraseña incorrecta";
                $this->view('auth/login', compact('error'));
                return;
            }

            // ===== SESIÓN COMPLETA =====
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_puesto'] = $usuario['puesto'] ?? '';
            $_SESSION['usuario_rol_id'] = $usuario['rol_id'];
            $_SESSION['usuario_unidad_id'] = $usuario['unidad_administrativa_id']; // <--- CLAVE

            header('Location: /Dir_bienestar/dashboard/index');
            exit;
        }

        $this->view('auth/login');
    }

    public function logout()
    {
        session_destroy();
        header('Location: /Dir_bienestar/auth/login');
        exit;
    }

    public function dashboard()
    {
        echo "<h1>Dashboard</h1>";
        echo "<p>Bienvenido " . $_SESSION['usuario_nombre'] . "</p>";
        echo "<a href='/Dir_bienestar/auth/logout'>Cerrar sesión</a>";
    }
}