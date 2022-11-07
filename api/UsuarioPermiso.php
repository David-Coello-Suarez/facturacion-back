<?php

require_once "ConfigCab.php";

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";

    require_once "class/CUsuper.php";

    $usuarioPermiso = new UsuarioPermiso();

    $metodo = $data['metodo'];

    unset($data['metodo']);

    switch ($metodo) {

        case 'BUSCAR_USUARIOS':
            return print_r(json_encode($usuarioPermiso->ListarUsuarios($data)));

        case 'CHANGE_STATUS':
            return print_r(json_encode($usuarioPermiso->changeStatus($data)));

        case 'ACTUALIZAR_USUARIO':
            return print_r(json_encode($usuarioPermiso->UpdateUsuario($data)));

        case 'CREAR_USUARIO':
            return print_r(json_encode($usuarioPermiso->CrearUsuario($data)));

        case "GUARDAR_PERMISOS":
            return print_r(json_encode($usuarioPermiso->GuardarAcceso($data)));

        case 'CAMBIO_TIPO_USUARIO':
            return print_r(json_encode($usuarioPermiso->UpdateUsuarioRol($data)));

        case 'SAVE_COMPAN_ACCESS':
            return print_r(json_encode($usuarioPermiso->GuardarAccessCompan($data)));

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar")));
}
