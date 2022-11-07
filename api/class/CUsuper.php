<?php

class UsuarioPermiso extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        parent::DBConexion();

        date_default_timezone_set("America/Guayaquil");
    }

    public function ListarUsuarios($data)
    {
        try {
            if (!isset($data['compan_compan'])) throw new Exception("Debe establecer la compañia", 1);

            if (!isset($data['sucurs_compan'])) throw new Exception("Debe establcer la sucursal", 1);

            $compania = intval(trim($data['compan_compan']));
            $sucursal = intval(trim($data['sucurs_compan']));

            $sql = "SELECT * FROM tb_usuari WHERE usuari_compan = $compania ";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("No hya datos para mostrar $sql", 1);

            $items = array();

            foreach ($exec as $item) {

                $item->usuari_correo = utf8_encode($item->usuari_correo);
                $item->usuari_nomusu = utf8_encode($item->usuari_nomusu);
                $item->usuari_apeusu = utf8_encode($item->usuari_apeusu);

                $items[] = $item;
            }

            return Funciones::RespuestaJson(1, "", array("usuarios" => $items));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function changeStatus($data)
    {
        try {
            if (!isset($data['usuari_usuari'])) throw new Exception("Debe establecer el ID de usuario", 1);

            if (!isset($data['usuari_estado'])) throw new Exception("Debe establcer el nuevo estado del usuario", 1);

            $usuario = intval(trim($data['usuari_usuari']));
            $estado = intval(trim($data['usuari_estado']));

            $sql = "UPDATE tb_usuari SET usuari_estado = $estado WHERE usuari_usuari = $usuario";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) throw new Exception("Error al actualizar datos", 1);

            $sql = "SELECT * FROM tb_usuari WHERE usuari_usuari = $usuario";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("Error al obtener el usuario", 1);

            $item = $exec[0];

            $item->usuari_correo = utf8_encode($item->usuari_correo);
            $item->usuari_nomusu = utf8_encode($item->usuari_nomusu);
            $item->usuari_apeusu = utf8_encode($item->usuari_apeusu);

            return Funciones::RespuestaJson(1, "", array("usuario" => $item));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function UpdateUsuario($data)
    {
        try {
            if (!isset($data['usuari_usuari'])) throw new Exception("Debe establecer el id de usuario", 1);
            if (!isset($data['usuari_cedula'])) throw new Exception("Debe establecer la cédula", 1);
            if (!isset($data['usuari_nomusu'])) throw new Exception("Debe establecer los nombres", 1);
            if (!isset($data['usuari_apeusu'])) throw new Exception("Debe establecer los apellidos", 1);
            if (!isset($data['usuari_correo'])) throw new Exception("Debe establecer el correo eléctronico", 1);
            if (!isset($data['usuari_supadm'])) throw new Exception("Debe establecer el rol del usuario", 1);

            $id = intval(trim($data['usuari_usuari']));
            $cedula = (trim($data['usuari_cedula']));
            $nombres = utf8_decode(trim($data['usuari_nomusu']));
            $apellidos = utf8_decode(trim($data['usuari_apeusu']));
            $correo = (trim($data['usuari_correo']));
            $supadm = intval($data['usuari_supadm']);

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) throw new Exception("El formateo de correo no es válido", 1);

            $update = "UPDATE tb_usuari SET 
                usuari_cedula = '$cedula',
                usuari_nomusu = '$nombres',
                usuari_apeusu = '$apellidos',
                usuari_correo = '$correo',
                usuari_supadm = $supadm
                WHERE usuari_usuari = $id
            ";

            $exec = $this->DBConsulta($update, true);

            if (!$exec) throw new Exception("Error al actualizar " . $update, 1);

            $sql = "SELECT * FROM tb_usuari WHERE usuari_usuari = $id";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("Error al obtener el usuario", 1);

            $item = $exec[0];

            $item->usuari_correo = utf8_encode($item->usuari_correo);
            $item->usuari_nomusu = utf8_encode($item->usuari_nomusu);
            $item->usuari_apeusu = utf8_encode($item->usuari_apeusu);

            return Funciones::RespuestaJson(1, "Actualizado con éxito", array("usuario" => $item));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function CrearUsuario($data)
    {
        try {
            if (!isset($data['usuari_cedula'])) throw new Exception("Debe establecer la cédula", 1);
            if (!isset($data['usuari_nomusu'])) throw new Exception("Debe establecer los nombres", 1);
            if (!isset($data['usuari_apeusu'])) throw new Exception("Debe establecer los apellidos", 1);
            if (!isset($data['usuari_correo'])) throw new Exception("Debe establecer el correo eléctronico", 1);
            if (!isset($data['usuari_compan'])) throw new Exception("Debe establecer la empresa", 1);
            if (!isset($data['usuari_supadm'])) throw new Exception("Debe establecer el rol del usuario", 1);

            $cedula = (trim($data['usuari_cedula']));
            $nombres = utf8_decode(trim($data['usuari_nomusu']));
            $apellidos = utf8_decode(trim($data['usuari_apeusu']));
            $correo = (trim($data['usuari_correo']));
            $compan = intval(trim($data['usuari_compan']));
            $supadm = intval($data['usuari_supadm']);

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) throw new Exception("El formateo de correo no es válido", 1);

            $buscar = "SELECT * FROM tb_usuari WHERE usuari_cedula = '$cedula'";

            $exec = $this->DBConsulta($buscar);

            if (count($exec) > 0) throw new Exception("Ya existe usuario con ese documento de identidad", 1);

            $password = sha1($cedula . "-" . KEYPASS);

            $insert = "INSERT INTO tb_usuari (usuari_compan, usuari_cedula, usuari_correo, usuari_nomusu, usuari_apeusu, usuari_passwor, usuari_codusu, usuari_clausu, usuari_estusu, usuari_supadm)
                                    VALUES ($compan, '$cedula', '$correo', '$nombres', '$apellidos', '$password', '', '', '1', $supadm )";

            $exec = $this->DBConsulta($insert, true);

            if (!$exec) throw new Exception("Error al crear el usuario", 1);

            $sql = "SELECT * FROM tb_usuari WHERE usuari_cedula = '$cedula'";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("Error al obtener el usuario", 1);

            $item = $exec[0];

            $item->usuari_correo = utf8_encode($item->usuari_correo);
            $item->usuari_nomusu = utf8_encode($item->usuari_nomusu);
            $item->usuari_apeusu = utf8_encode($item->usuari_apeusu);

            return Funciones::RespuestaJson(1, "Creado con éxito", array("usuario" => $item));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);

                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function GuardarAcceso($data)
    {
        try {
            // return Funciones::RespuestaJson(2, "", $data);
            if (!isset($data['usuari_usuari'])) throw new Exception("Debe establecer el id de usuario", 1);
            if (!isset($data['menu'])) throw new Exception("Debe establecer el menú", 1);

            $id = intval(trim($data['usuari_usuari']));

            $sql = "DELETE FROM tb_acceso WHERE acceso_usuari = $id";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) throw new Exception($sql, 1);

            $menuAcces = $data['menu'];

            $total = count($menuAcces);

            $exeSave = 0;

            foreach ($menuAcces as $item) {

                $idpadre = intval($item['idpadre']);
                $idhijo = intval($item['idhijo']);

                $guardar = "INSERT INTO tb_acceso (acceso_usuari, acceso_idpadr, acceso_idmenu, acceso_compan) VALUES ($id, $idpadre, $idhijo, 0)";

                $exec = $this->DBConsulta($guardar, true);

                if ($exec) {
                    $exeSave++;
                }
            }

            if ($total != $exeSave) throw new Exception("Error al guardar los accesos", 1);

            return Funciones::RespuestaJson(1, "Accesos guardados con éxito");
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function UpdateUsuarioRol($data)
    {
        try {
            if (!isset($data['usuari_usuari'])) throw new Exception("Debe establecer el id de usuario", 1);
            if (!isset($data['usuari_supadm'])) throw new Exception("Debe establecer el nuevo rol", 1);

            $id = intval(trim($data['usuari_usuari']));
            $tipousuario = (trim($data['usuari_supadm']));

            $update = "UPDATE tb_usuari SET 
                usuari_supadm = '$tipousuario'
                WHERE usuari_usuari = $id
            ";

            $exec = $this->DBConsulta($update, true);

            if (!$exec) throw new Exception("Error al actualizar ", 1);

            $sql = "SELECT * FROM tb_usuari WHERE usuari_usuari = $id";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("Error al obtener el usuario", 1);

            $item = $exec[0];

            $item->usuari_correo = utf8_encode($item->usuari_correo);
            $item->usuari_nomusu = utf8_encode($item->usuari_nomusu);
            $item->usuari_apeusu = utf8_encode($item->usuari_apeusu);

            return Funciones::RespuestaJson(1, "Actualizado con éxito", array("usuario" => $item));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function GuardarAccessCompan($data)
    {
        try {
            $usuario = intval($data['usuario']);
            $compans = $data['compan'];

            $sqlTot = "SELECT * FROM TB_USUEMP WHERE usuemp_usuario = $usuario";

            $execTot = $this->DBConsulta($sqlTot);

            if (count($execTot) > 0) {

                $sql = "DELETE FROM TB_USUEMP WHERE usuemp_usuario = $usuario";

                $exec = $this->DBConsulta($sql, true);

                if (!$exec) throw new Exception("Error al procesar las companias", 1);
            }

            $tot = 0;

            foreach ($compans as $item) {
                $compan = intval($item['compan_compan']);

                $sql = "INSERT INTO TB_USUEMP (usuemp_usuario, usuemp_compan) VALUES ($usuario, $compan)";

                $exec = $this->DBConsulta($sql, true);

                if ($exec) {
                    $tot++;
                }
            }

            if ($tot != count($compans)) throw new Exception("Error al asignar las companias", 1);

            return Funciones::RespuestaJson(1, "Asignados con éxito");
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
