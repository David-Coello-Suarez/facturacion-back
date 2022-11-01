<?php
require_once "ConfigCab.php";

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";

    require_once "class/CCompania.php";

    $compania = new Compania();

    $metodo = $data['metodo'];

    unset($data['metodo']);

    switch ($metodo) {

        case 'LISTAR_COMPANIA':
            return print_r(json_encode($compania->ListarCompanias()));

        case 'LISTAR_COMPANIA_MAN':
            return print_r(json_encode($compania->ListarCompaniasMan()));

        case 'CHANGE_STATUS':
            return print_r(json_encode($compania->CambiarStatus($data)));

        case 'GUARDAR_COMPAN':
            return print_r(json_encode($compania->CrearCompania($data)));

        case 'ACTUALIZAR_COMPAN':
            return print_r(json_encode($compania->ActualizarCompan($data)));

        case 'LISTAR_COMPAN_SUCURS':
            return print_r(json_encode($compania->ListarCompanSucurs()));

        case 'ACTUALIZAR_AMBIENTE_FACTURA':
            return print_r(json_encode($compania->ChangeModoFactura($data)));

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar", $data)));
}
