<?php

require_once "ConfigCab.php";

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";

    require_once "class/CFacturacion.php";

    $facturacion = new Facturacion();

    $metodo = $data['metodo'];

    unset($data['metodo']);

    switch ($metodo) {
        case 'GUARDAR_FACTURA':
            return print_r(json_encode($facturacion->GuardarFactura($data)));

        case 'REPORTE_FACTURA':
            return print_r(json_encode($facturacion->ListarFactura($data)));

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar")));
}
