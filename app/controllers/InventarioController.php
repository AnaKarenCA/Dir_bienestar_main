<?php

class InventarioController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }
        $rol = $_SESSION['usuario_rol_id'];
        if (!in_array($rol, [2, 5])) {
            die('Acceso denegado.');
        }
    }

    public function index()
    {
        $inventarioModel = $this->model('InventarioInsumo');
        $insumos = $inventarioModel->obtenerTodos();
        $this->view('inventario/index', ['insumos' => $insumos]);
    }

    public function prestamos()
    {
        // mostrar préstamos activos
    }
}