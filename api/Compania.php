<?php
require_once "ConfigCab.php";

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";

    require_once "class/CCompania.php";

    $compania = new Compania();

    $metodo = $data['metodo'];

    unset($data['metodo']);

    if (!isset($data['x-token'])) return print_r(json_encode(Funciones::RespuestaJson(2, "Debe establecer el token de acceso")));

    $token = trim($data['x-token']);

    $metodoPermitidos = array("LISTAR_COMPAN_SUCURS");

    if (in_array(trim($metodo), $metodoPermitidos)) {

        $sql = "SELECT * FROM TB_USUARI WHERE usuari_tokens = '$token' AND usuari_supadm = 0";

        $conexion = new Conexion();
        $conexion->DBConexion();
        $exec = $conexion->DBConsulta($sql);

        $usuario = 0;
        $tipoUsuario = 0;

        if (count($exec) > 0) {
            $item = $exec[0];
            $tipoUsuario = intval($item->usuari_supadm);
            $usuario = intval($item->usuari_usuari);
        }

        $data['usuemp_compan'] = $usuario;
        $data['usuemp_supadm'] = $tipoUsuario;
    }

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
            return print_r(json_encode($compania->ListarCompanSucurs($data)));

        case 'ACTUALIZAR_AMBIENTE_FACTURA':
            return print_r(json_encode($compania->ChangeModoFactura($data)));

        case 'CHECK_COMPAN':
            return print_r(json_encode($compania->CheckCompan($data)));

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar")));
}
