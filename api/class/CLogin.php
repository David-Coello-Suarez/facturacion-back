<?php

class Login extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        parent::DBConexion();

        date_default_timezone_set("America/Guayaquil");
    }

    public function Login($data)
    {
        try {
            $usuario = strtoupper(trim($data['usuario']));
            $contrasena = strtoupper(trim($data['contrasena']));

            $sql = "SELECT * FROM tb_usuari WHERE usuari_codusu = '$usuario'";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("Usuario - contraseña incorrectos 1", 1);

            $item = $exec[0];

            if (intval($item->usuari_estado) == 0) throw new Exception("Usuario no se encuentra activo", 1);

            $has = sha1($contrasena . "-" . KEYPASS);

            if (trim($item->usuari_passwor) != trim($has)) throw new Exception("Usuario - contraseña incorrecto", 1);

            $usuarioSuccess['usuario'] = array(
                "id" => intval($item->usuari_usuari),
                "usuario" => utf8_encode($item->usuari_nomusu) . " " . utf8_encode($item->usuari_apeusu),
                "tipousuario" => $item->usuari_supadm
            );

            $token = md5(uniqid(mt_rand(), true)) . "." . $has;

            $sql = "UPDATE tb_usuari SET usuari_tokens = '$token' WHERE usuari_cedula = '$usuario'";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) throw new Exception("Error al generar credenciales de autenticación", 1);

            $usuarioSuccess['token'] = $token;

            return Funciones::RespuestaJson(1, "", array("usuario" => $usuarioSuccess));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }
}
