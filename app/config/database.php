<?php

return [
    'host' => 'localhost',
    'port' => '3307',
    'database' => 'sistemaactividades',
    'username' => 'root',
    'password' => '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
];