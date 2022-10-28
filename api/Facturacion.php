<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

header("Content-Type: application/json; charset=UTF-8");

require_once "../util/system/Funciones.php";

$data = json_decode(file_get_contents("php://input"), true);

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

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar")));
}
