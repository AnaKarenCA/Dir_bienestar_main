<?php

class EvidenciasController extends Controller
{
    private $uploadDir;
    private $maxFileSize = 5 * 1024 * 1024; // 5 MB
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    private $precisionMaxima = 140; // metros

    public function __construct()
    {
        if (method_exists(get_parent_class($this), '__construct')) {
            parent::__construct();
        }

        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /Dir_bienestar/auth/login');
            exit;
        }

        if (!defined('PUBLIC_PATH')) {
            define('PUBLIC_PATH', dirname(__DIR__, 2) . '/public');
        }

        $this->uploadDir = PUBLIC_PATH . '/uploads/evidencias/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    /**
     * Comprime y redimensiona una imagen (requiere extensión GD)
     */
    private function compressImage($sourcePath, $destPath, $maxWidth = 1200, $quality = 80)
    {
        if (!extension_loaded('gd')) {
            // Si no hay GD, copiar el archivo original
            return copy($sourcePath, $destPath);
        }

        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        list($width, $height, $type) = $imageInfo;

        // Calcular nuevas dimensiones
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = intval($height * ($maxWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Crear recurso de imagen
        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }

        if (!$src) {
            return false;
        }

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        // Mantener transparencia para PNG
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Guardar
        $success = false;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $success = imagejpeg($dst, $destPath, $quality);
                break;
            case IMAGETYPE_PNG:
                $pngQuality = intval(($quality / 100) * 9);
                $success = imagepng($dst, $destPath, $pngQuality);
                break;
            case IMAGETYPE_GIF:
                $success = imagegif($dst, $destPath);
                break;
        }

        imagedestroy($src);
        imagedestroy($dst);

        return $success;
    }

    /**
     * Procesa la subida de una imagen con compresión automática
     */
    private function processImageUpload($file, $registroId, $tipo)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo (código ' . $file['error'] . ')');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new Exception('Formato de imagen no permitido. Usa JPG, PNG o GIF.');
        }

        $nombreArchivo = 'evidencia_' . $registroId . '_' . $tipo . '_' . time() . '.' . $extension;
        $rutaDestino = 'uploads/evidencias/' . $nombreArchivo;
        $rutaFisica = $this->uploadDir . $nombreArchivo;

        // Siempre comprimir
        if ($this->compressImage($file['tmp_name'], $rutaFisica, 1200, 75)) {
            return $rutaDestino;
        } else {
            // Fallback: mover el original
            if (move_uploaded_file($file['tmp_name'], $rutaFisica)) {
                return $rutaDestino;
            }
            throw new Exception('No se pudo guardar la imagen.');
        }
    }

    // ============================================================
    // MÉTODOS PÚBLICOS
    // ============================================================

    public function index()
    {
        $usuarioId = $_SESSION['usuario_id'];
        $evidenciaModel = $this->model('Evidencia');
        $actividades = $evidenciaModel->obtenerActividadesConEvidencias($usuarioId);

        foreach ($actividades as &$act) {
            $total = (int)$act['evidencias_count'];
            if ($total == 0) {
                $act['estado_texto'] = 'Pendiente';
                $act['estado_clase'] = 'pendiente';
            } elseif ($total < 3) {
                $act['estado_texto'] = 'En proceso';
                $act['estado_clase'] = 'proceso';
            } else {
                $act['estado_texto'] = 'Completada';
                $act['estado_clase'] = 'completada';
            }
        }

        $this->view('evidencias/index', [
            'actividades' => $actividades,
            'usuario' => $this->model('Usuario')->obtenerPorId($usuarioId)
        ]);
    }

    public function detalle($id)
    {
        $usuarioId = $_SESSION['usuario_id'];

        $registroModel = $this->model('RegistroActividad');
        $actividad = $registroModel->obtenerRegistroCompletoPorId($id);

        if (!$actividad || $actividad['usuario_id'] != $usuarioId) {
            die('No tienes permiso para ver esta actividad.');
        }

        $evidenciaModel = $this->model('Evidencia');
        $evidencias = $evidenciaModel->obtenerPorRegistro($id);

        $evidenciasPorTipo = [];
        foreach ($evidencias as $e) {
            $evidenciasPorTipo[$e['tipo']] = $e;
        }

        $totalEvidencias = count($evidencias);

        $this->view('evidencias/detalle', [
            'actividad' => $actividad,
            'evidenciasPorTipo' => $evidenciasPorTipo,
            'totalEvidencias' => $totalEvidencias,
            'usuario' => $this->model('Usuario')->obtenerPorId($usuarioId)
        ]);
    }

    public function registrar($registroId, $tipo = null)
    {
        $usuarioId = $_SESSION['usuario_id'];

        if ($tipo === null) {
            $tipo = $_GET['tipo'] ?? null;
        }

        if (!$tipo || !in_array($tipo, ['llegada', 'durante', 'finalizacion'])) {
            die('Tipo de evidencia inválido.');
        }

        $registroModel = $this->model('RegistroActividad');
        $actividad = $registroModel->obtenerRegistroCompletoPorId($registroId);

        if (!$actividad || $actividad['usuario_id'] != $usuarioId) {
            die('No tienes permiso para registrar evidencias de esta actividad.');
        }

        $evidenciaModel = $this->model('Evidencia');
        $evidencia = $evidenciaModel->obtenerPorTipo($registroId, $tipo);

        $tiposNombres = [
            'llegada' => 'Llegada',
            'durante' => 'Durante la actividad',
            'finalizacion' => 'Finalización'
        ];

        $this->view('evidencias/registrar', [
            'registroId' => $registroId,
            'tipo' => $tipo,
            'tipoNombre' => $tiposNombres[$tipo],
            'evidencia' => $evidencia,
            'actividad' => $actividad,
            'usuario' => $this->model('Usuario')->obtenerPorId($usuarioId)
        ]);
    }

    public function guardar()
    {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $registroId = (int) ($_POST['registro_id'] ?? 0);
            $tipo = $_POST['tipo'] ?? null;
            $comentarios = trim($_POST['comentarios'] ?? '');
            $latitud = $_POST['latitud'] ?? null;
            $longitud = $_POST['longitud'] ?? null;
            $precision = $_POST['precision'] ?? null;

            if (!$registroId || !$tipo || !in_array($tipo, ['llegada', 'durante', 'finalizacion'])) {
                throw new Exception('Datos incompletos o inválidos.');
            }

            $registroModel = $this->model('RegistroActividad');
            $actividad = $registroModel->obtenerRegistroCompletoPorId($registroId);
            if (!$actividad || $actividad['usuario_id'] != $_SESSION['usuario_id']) {
                throw new Exception('No tienes permiso.');
            }

            // Validar precisión (≤ 140 m)
            if ($precision !== null && $precision > $this->precisionMaxima) {
                throw new Exception('La precisión de la ubicación es de ' . round($precision) . ' m. Debe ser menor a ' . $this->precisionMaxima . ' m. Vuelve a obtener la ubicación.');
            }

            $evidenciaModel = $this->model('Evidencia');
            $existente = $evidenciaModel->obtenerPorTipo($registroId, $tipo);
            $rutaImagen = null;
            $existeEvidenciaPrevia = false;

            if ($existente && !empty($existente['fotografia'])) {
                $rutaImagen = $existente['fotografia'];
                $existeEvidenciaPrevia = true;
            }

            // Procesar nueva imagen si se subió
            if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK) {
                try {
                    $rutaImagen = $this->processImageUpload($_FILES['fotografia'], $registroId, $tipo);
                } catch (Exception $e) {
                    throw new Exception('Error al procesar la imagen: ' . $e->getMessage());
                }
            } else {
                if (!$existeEvidenciaPrevia) {
                    throw new Exception('Debes seleccionar una fotografía.');
                }
            }

            // Preparar datos para guardar
            $datos = [
                'registro_actividad_id' => $registroId,
                'tipo' => $tipo,
                'fotografia' => $rutaImagen,
                'latitud' => $latitud,
                'longitud' => $longitud,
                'precision' => $precision,
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'comentarios' => $comentarios
            ];

            $resultado = $evidenciaModel->guardar($datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'mensaje' => 'Evidencia guardada correctamente.',
                    'redirect' => '/Dir_bienestar/evidencias/detalle/' . $registroId
                ]);
            } else {
                throw new Exception('Error al guardar la evidencia en la base de datos.');
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function ver($registroId)
    {
        $usuarioId = $_SESSION['usuario_id'];

        $registroModel = $this->model('RegistroActividad');
        $actividad = $registroModel->obtenerRegistroCompletoPorId($registroId);

        if (!$actividad || $actividad['usuario_id'] != $usuarioId) {
            die('No tienes permiso para ver estas evidencias.');
        }

        $evidenciaModel = $this->model('Evidencia');
        $evidencias = $evidenciaModel->obtenerPorRegistro($registroId);

        $this->view('evidencias/ver', [
            'actividad' => $actividad,
            'evidencias' => $evidencias,
            'usuario' => $this->model('Usuario')->obtenerPorId($usuarioId)
        ]);
    }

    public function eliminar($id)
    {
        header('Content-Type: application/json');

        $evidenciaModel = $this->model('Evidencia');
        $evidencia = $evidenciaModel->obtenerPorId($id);

        if (!$evidencia) {
            echo json_encode(['success' => false, 'error' => 'Evidencia no encontrada.']);
            return;
        }

        $registroModel = $this->model('RegistroActividad');
        $actividad = $registroModel->obtenerRegistroCompletoPorId($evidencia['registro_actividad_id']);
        if (!$actividad || $actividad['usuario_id'] != $_SESSION['usuario_id']) {
            echo json_encode(['success' => false, 'error' => 'No tienes permiso.']);
            return;
        }

        if ($evidenciaModel->eliminar($id)) {
            echo json_encode(['success' => true, 'mensaje' => 'Evidencia eliminada.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al eliminar.']);
        }
    }
}