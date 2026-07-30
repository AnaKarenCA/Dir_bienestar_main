<?php

require_once APPROOT . '/controllers/AuthController.php';

class App
{
    protected $controller = 'AuthController';
    protected $method = 'login';
    protected $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        if (isset($url[0])) {
            switch (strtolower($url[0])) {
                case 'auth':
                    require_once APPROOT . '/controllers/AuthController.php';
                    $this->controller = 'AuthController';
                    break;
                case 'dashboard':
                    require_once APPROOT . '/controllers/DashboardController.php';
                    $this->controller = 'DashboardController';
                    break;
                case 'actividad':
                    require_once APPROOT . '/controllers/ActividadController.php';
                    $this->controller = 'ActividadController';
                    break;
                case 'calendario':
                    require_once APPROOT . '/controllers/CalendarioController.php';
                    $this->controller = 'CalendarioController';
                    break;
                case 'reporte':
                    require_once APPROOT . '/controllers/ReporteController.php';
                    $this->controller = 'ReporteController';
                    break;
                case 'eventos':
                    require_once APPROOT . '/controllers/EventosController.php';
                    $this->controller = 'EventosController';
                    break;
                case 'evento_ppt':
                    require_once APPROOT . '/controllers/EventoPPTController.php';
                    $this->controller = 'EventoPPTController';
                    break;
                case 'evidencias':
                    require_once APPROOT . '/controllers/EvidenciasController.php';
                    $this->controller = 'EvidenciasController';
                    break;
                case 'admin':
                    require_once APPROOT . '/controllers/AdminController.php';
                    $this->controller = 'AdminController';
                    break;
                case 'usuario':
                    require_once APPROOT . '/controllers/UsuarioController.php';
                    $this->controller = 'UsuarioController';
                    break;
                case 'empleados':
                    require_once APPROOT . '/controllers/EmpleadosController.php';
                    $this->controller = 'EmpleadosController';
                    break;
                case 'inventario':
    require_once APPROOT . '/controllers/InventarioController.php';
    $this->controller = 'InventarioController';
    break;
            }
        }

        // Instanciar el controlador
        $controller = new $this->controller();

        // Determinar método y parámetros
        if (isset($url[1]) && method_exists($controller, $url[1])) {
            // Si existe el método en la URL, usarlo
            $this->method = $url[1];
            unset($url[0], $url[1]);
            $this->params = $url ? array_values($url) : [];
        } else {
            // Si no se especificó método, usar uno por defecto
            if ($this->controller === 'AuthController') {
                // Para AuthController, el método por defecto es 'login'
                $this->method = 'login';
            } else {
                // Para cualquier otro controlador, usar 'index'
                $this->method = 'index';
            }
            // Si hay parámetros en la URL, se pueden pasar como params (opcional)
            // Pero normalmente no se usan sin método; los dejamos vacíos.
            $this->params = [];
        }

        // Ejecutar el método con los parámetros
        call_user_func_array([$controller, $this->method], $this->params);
    }

    private function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            // Eliminar el prefijo 'dir_bienestar' si existe
            if (isset($url[0]) && strtolower($url[0]) === 'dir_bienestar') {
                array_shift($url);
            }

            return $url;
        }
        return [];
    }
}