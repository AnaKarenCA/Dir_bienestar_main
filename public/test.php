<?php

require_once 'app/bootstrap.php';

try {

    $db = new Database();

    echo "Conexion OK";

} catch(Exception $e) {

    echo $e->getMessage();
}