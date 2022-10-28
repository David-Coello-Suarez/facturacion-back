<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

header("Content-Type: application/json; charset=UTF-8");

require_once "../util/system/Funciones.php";

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";

    require_once "class/CMenu.php";

    $menu = new Menu();

    $metodo = strtoupper(trim($data['metodo']));

    unset($data['metodo']);

    switch ($metodo) {

        case "LISTAR_MENU":
            return print_r(json_encode($menu->ObtenerMenu($data)));

        case "ACTUALIZAR_ESTADO":
            return print_r(json_encode($menu->CambiarEstado($data)));

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar", $data)));
}
