<?php

require_once "ConfigCab.php";

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";

    require_once "class/CSucursal.php";

    $sucursal = new Sucursal();

    $metodo = $data['metodo'];

    unset($data['metodo']);

    switch ($metodo) {

        case 'CREAR_SUCURSAL':
            return print_r(json_encode($sucursal->CrearSucursal($data)));

        case 'OBTENER_SUCURSAL_COMPAN':
            return print_r(json_encode($sucursal->ObtenerSucursalCompan($data)));

        case 'CAMBIAR_ESTADO':
            return print_r(json_encode($sucursal->changeStatus($data)));

        case 'CHANGE_SUCURSAL':
            return print_r(json_encode($sucursal->UpdateSucursal($data)));

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar", $data)));
}
