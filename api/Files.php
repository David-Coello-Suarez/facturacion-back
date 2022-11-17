<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');

header("Content-Type: application/json; charset=UTF-8");

require_once "../util/system/Funciones.php";

$archivo;

if (isset($_FILES['compan_firma'])) {
    $archivo = $_FILES['compan_firma'];
} else if (isset($_FILES['sucurs_logsuc'])) {
    $archivo = $_FILES['sucurs_logsuc'];
}

$name = uniqid() . $archivo['name'];
$tipo = $archivo['type'];
$tmpname = $archivo['tmp_name'];

$extension = explode(".", $name);
$extension = $extension[count($extension) - 1];

$extenciones = array();

if (isset($_FILES['compan_firma'])) {
    $extenciones = array('jks', 'p12');
} else if (isset($_FILES['sucurs_logsuc'])) {
    $extenciones = array("p12", 'png', 'jpg', 'jpeg');
}

$ruta = "/tempfile/";

if (!in_array($extension, $extenciones)) return print_r(json_encode(Funciones::RespuestaJson(1, "Formato de extención no permitida", $archivo)));

if (!file_exists($ruta)) mkdir($ruta, 0777, true);

$ruta = $ruta . "-" . $name;

if (move_uploaded_file($tmpname, $ruta)) {
    $file['archivo'] = $name;
    $file['path'] = $ruta;

    return print_r(json_encode(Funciones::RespuestaJson(1, "", $file)));
}

return print_r(json_encode(Funciones::RespuestaJson(2, "Error al carga el archivo")));
