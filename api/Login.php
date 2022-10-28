<?php

require_once "ConfigCab.php";

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";
    require_once "class/CLogin.php";

    $acceso = new Login();

    $metodo = $data['metodo'];

    unset($data['metodo']);

    switch ($metodo) {

        case 'LOGIN':
            return print_r(json_encode($acceso->Login($data)));

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar")));
}
