<?php

require_once "ConfigCab.php";

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";

    require_once "class/CFormapago.php";

    $formapago = new FormaPago();

    $metodo = $data['metodo'];

    unset($data['metodo']);

    switch ($metodo) {

        case "LISTAR_FORPAG":
            return print_r(json_encode($formapago->ListarFormasPago($data)));

        case "CREAR":
            return print_r(json_encode($formapago->CrearForma($data)));

        case 'ACTUALIZAR':
            return print_r(json_encode($formapago->ActualizarForma($data)));

        case 'ESTADO':
            return print_r(json_encode($formapago->EstadoForma($data)));

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar")));
}
