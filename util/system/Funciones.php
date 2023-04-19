<?php
class Funciones
{
    public static function RespuestaJson($estado = 3, $msj = "", $data = array())
    {
        $respuesta = new stdClass();
        $respuesta->estado = $estado;
        $respuesta->msj = $msj;
        $respuesta->data = $data;
        return $respuesta;
    }

    public static function escribirLogs($nombreLog, $mensaje)
    {
        $fechaLog = date("Y/m/d");

        if (!file_exists("../logs/" . $fechaLog)) mkdir("../logs/" . $fechaLog, 0777, true);

        $path = "../logs/" . $fechaLog . "/$nombreLog.txt";

        $mensaje = date("Y/m/d H:i:s") . " >>>> " . $mensaje . "\n\r";

        $archivo = fopen($path, "a+");
        fwrite($archivo, $mensaje);
        fclose($archivo);
    }

    public static function GenerarAutorizacion($numero_documento)
    {
        $numero_documento = trim($numero_documento);
        $numero_documento = str_replace(array("-", " "), "", $numero_documento);
        // print_r($numero_documento);
        $suma = 0;
        $factor = 2;

        $caracteres = str_split($numero_documento);

        $long = count($caracteres) - 1;

        for ($i = $long; $i > 0; $i--) {

            $suma += intval($caracteres[$i]) * $factor;

            if ($factor == 7) {
                $factor = 2;
            } else {
                $factor++;
            }
        }

        $modulo = 11 - ($suma % 11);

        if ($modulo == 10) {
            echo"modulo es igual a 1";
            return (string)"1";
        }

        if ($modulo < 10) {
            return (string)$modulo;
        }

        if ($modulo == 11) {
            return (string) "0";
        }

        return "";
    }

    public static function subirImg(array $img)
    {
        $nombre = $img['name'];
        $tipo = $img['type'];
        $tamano = $img['size'];
        $archivo = $img['tmp_name'];
        $error = $img['error'];
        $extension = explode(".", $nombre);
        $extension = $extension[count($extension) - 1];
        $extension = strtolower($extension);
        $extensiones = array("jpg", "jpeg", "png", "gif");
        $ruta = "../img/productos/";
        $nombre = time() . "." . $extension;
        $ruta = $ruta . $nombre;

        if (!in_array($extension, $extensiones))  return array('status' => false, 'msj' => "Formato de imagen no permitido");
        if (!file_exists($ruta)) mkdir($ruta, 0777, true);

        if (move_uploaded_file($archivo, $ruta)) return $nombre;
        return "Error al subir la imagen";
    }



    public static function zero_fill($valor, $long = 3, $dir = STR_PAD_LEFT)
    {
        return str_pad($valor, $long, '0', $dir);
    }
}
