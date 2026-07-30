<?php

define('APPROOT', dirname(__FILE__));

require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/core/Model.php';
require_once APPROOT . '/core/Controller.php';
require_once APPROOT . '/core/App.php';

require_once APPROOT . '/models/Usuario.php';
require_once APPROOT . '/models/UnidadAdministrativa.php';
require_once APPROOT . '/models/Delegacion.php';
require_once APPROOT . '/models/Subdelegacion.php';
require_once APPROOT . '/models/Lugar.php';
require_once APPROOT . '/models/UnidadMedida.php';
require_once APPROOT . '/models/TipoEntregable.php';
require_once APPROOT . '/models/RegistroActividad.php';

require_once APPROOT . '/models/InventarioInsumo.php';

require_once APPROOT . '/models/Usuario.php';
require_once APPROOT . '/models/Rol.php';
require_once APPROOT . '/models/UnidadAdministrativa.php';
require_once APPROOT . '/models/InventarioInsumo.php';

require_once APPROOT . '/models/Evidencia.php';
require_once APPROOT . '/models/HistorialCarpeta.php';