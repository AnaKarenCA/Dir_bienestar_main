<?php

class UsuarioController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }
    }

    public function perfil()
    {
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);

        $this->view('usuario/perfil', ['usuario' => $usuario]);
    }

    public function configuracion()
    {
        $this->view('usuario/configuracion');
    }

    public function actualizarPerfil()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar campos
        if (empty($data['nombre']) || empty($data['correo'])) {
            echo json_encode(['success' => false, 'error' => 'Nombre y correo son obligatorios']);
            return;
        }

        // Validar formato de correo
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Correo electrónico inválido']);
            return;
        }

        $usuarioModel = $this->model('Usuario');
        $result = $usuarioModel->actualizar($_SESSION['usuario_id'], $data);

        if ($result) {
            // Actualizar sesión con el nuevo nombre
            $_SESSION['usuario_nombre'] = $data['nombre'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar perfil']);
        }
    }


    /**
     * Cambiar contraseña del usuario
     */
    public function cambiarContrasena()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar campos
        if (empty($data['actual']) || empty($data['nueva']) || empty($data['confirmar'])) {
            echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
            return;
        }

        if ($data['nueva'] !== $data['confirmar']) {
            echo json_encode(['success' => false, 'error' => 'Las contraseñas no coinciden']);
            return;
        }

        if (strlen($data['nueva']) < 8) {
            echo json_encode(['success' => false, 'error' => 'La nueva contraseña debe tener al menos 8 caracteres']);
            return;
        }

        // Verificar contraseña actual
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        
        if (!password_verify($data['actual'], $usuario['clave'])) {
            echo json_encode(['success' => false, 'error' => 'Contraseña actual incorrecta']);
            return;
        }

        // Cambiar contraseña
        $result = $usuarioModel->cambiarContrasena($_SESSION['usuario_id'], $data['nueva']);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al cambiar contraseña']);
        }
    }
}