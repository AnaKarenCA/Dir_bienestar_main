<?php

if (php_sapi_name() === 'cli-server') {

    $path = parse_url(
        $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
    );

    // 1. Buscar primero en public (comportamiento original)
    $file = __DIR__ . $path;
    if (is_file($file)) {
        return false; // El servidor sirve el archivo
    }

    // 2. Si no existe, buscar en la raíz del proyecto (un nivel arriba)
    $file = dirname(__DIR__) . $path;
    if (is_file($file)) {
        // Para que el servidor sirva el archivo, debemos devolver el contenido manualmente
        // o podemos redirigir, pero lo mejor es usar readfile
        header('Content-Type: ' . mime_content_type($file));
        readfile($file);
        exit;
    }

    // Si no es un archivo, pasar a la aplicación
    $_GET['url'] = trim($path, '/');
}

require_once __DIR__ . '/index.php';