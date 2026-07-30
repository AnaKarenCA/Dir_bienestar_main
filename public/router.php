<?php

if (php_sapi_name() === 'cli-server') {

    $path = parse_url(
        $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
    );

    $file = __DIR__ . $path;

    if (is_file($file)) {
        return false;
    }

    $_GET['url'] =
        trim($path, '/');
}

require_once __DIR__ . '/index.php';