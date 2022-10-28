<?php
require_once "ConfigCab.php";

if (isset($data['metodo'])) {

    require_once "../config.php";
    require_once "../util/system/conexion.php";

    require_once "class/CProductos.php";

    $producto = new Producto();

    $metodo = $data['metodo'];

    unset($data['metodo']);

    switch ($metodo) {
        case 'PRO_MAS_SOLI':
            return print_r(json_encode($producto->ProductoSolicitados($data['data'])));

        case 'OBTENER_PROD':
            return print_r(json_encode($producto->ObtenerProducto($data)));

        case "LISTAR_PRODUCTOS":
            return print_r(json_encode($producto->ListarProductos($data['data'])));

        case 'CAMBIAR_ESTADO':
            return print_r(json_encode($producto->ChangeEstado($data)));

        case 'AGREGAR_PRODUCTO':
            return print_r(json_encode($producto->CrearProducto($data['producto'])));

        case 'ACTUALIZAR_PRODUCTO':
            return print_r(json_encode($producto->ActualizarProducto($data['producto'])));

        case 'ACTUALIZAR_TIPO':
            return print_r(json_encode($producto->ProductoTipo($data)));

        case 'ACTUALIZAR_EDITABLE':
            return print_r(json_encode($producto->ProductoEditable($data)));

        default:
            return print_r(json_encode(Funciones::RespuestaJson(2, "Metodo no encontrado")));
    }
} else {
    print_r(json_encode(Funciones::RespuestaJson(3, "Debe establer el metodo a utilizar", $data)));
}
