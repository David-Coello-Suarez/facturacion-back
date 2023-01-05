<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: *');
header("Access-Control-Allow-Headers: *");

header("Content-Type: application/json; charset=UTF-8");

require_once "../util/system/Funciones.php";

$data = json_decode(file_get_contents("php://input"), true);
