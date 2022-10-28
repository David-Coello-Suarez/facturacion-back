<?php

require_once "ConfigCab.php";

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";

    require_once "class/CCliente.php";

    $cliente = new Cliente();

    $metodo = $data['metodo'];

    unset($data['metodo']);

    switch ($metodo) {
        case 'BUSCAR_CLIENTE':
            return print_r(json_encode($cliente->ObtenerCliente($data)));

        case 'OBTENER_CLIENTES':
            return print_r(json_encode($cliente->ObtenerClientes($data)));

        case 'CHAMGE_ESTADO':
            return print_r(json_encode($cliente->changeStatus($data)));

        case 'ACTUALIZAR_DATOS':
            return print_r(json_encode($cliente->UpdateDate($data)));

        case 'GUARDAR_DATOS':
            return print_r(json_encode($cliente->GuardarData($data)));


        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar")));
}
