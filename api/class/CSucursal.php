<?php

class Sucursal extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        parent::DBConexion();

        date_default_timezone_set("America/Guayaquil");
    }

    public function CrearSucursal($data)
    {
        try {
            // return Funciones::RespuestaJson(1, "", $data);

            if (!isset($data['sucurs_docume'])) throw new Exception("Debe establecer el número de documento", 1);
            if (!isset($data['sucurs_nombre'])) throw new Exception("Debe establecer el nombre de la sucursal", 1);
            if (!isset($data['sucurs_email'])) throw new Exception("Debe eestablecer el correo electrónico", 1);
            if (!isset($data['sucurs_telefo'])) throw new Exception("Debe establecer el número de teléfono", 1);
            if (!isset($data['sucurs_direcc'])) throw new Exception("Debe establecer la dirección", 1);
            if (!isset($data['sucurs_compan'])) throw new Exception("Debe seleccionar la compañia", 1);
            if (!isset($data['sucurs_numser'])) throw new Exception("Debe establecer el número de serie", 1);

            $docume = trim($data['sucurs_docume']);
            $nombre = utf8_decode(trim($data['sucurs_nombre']));
            $email = utf8_decode(trim($data['sucurs_email']));
            $telefo = trim($data['sucurs_telefo']);
            $direcc = utf8_decode(trim($data['sucurs_direcc']));
            $compan = intval($data['sucurs_compan']);
            $serie = trim($data['sucurs_numser']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return Funciones::RespuestaJson(2, "Formato de correo no válido");

            $sqlExiste = "SELECT * FROM tb_sucurs WHERE sucurs_docume = '$docume' AND sucurs_compan = $compan";

            $exec = $this->DBConsulta($sqlExiste);

            if (count($exec) > 0) return Funciones::RespuestaJson(2, "Ya existe sucursal con ese documento");

            $sqlOrden = "SELECT MAX(sucurs_ordvis) as orden FROM tb_sucurs WHERE sucurs_compan = $compan";

            $exec = $this->DBConsulta($sqlOrden);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "Error al obtener el orden");

            $orden = intval($exec[0]->orden) + 1;
            // , sucurs_numncr, sucurs_numfac
            $sql = "INSERT INTO tb_sucurs (sucurs_compan, sucurs_docume, sucurs_nombre, sucurs_email, sucurs_direcc, sucurs_telefo, sucurs_ordvis)
                                VALUES ($compan, '$docume', '$nombre', '$email', '$direcc', '$telefo', $orden)";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al guardar la sucursal");

            $sql = "SELECT sucurs_sucurs FROM tb_sucurs WHERE sucurs_docume = '$docume'";

            $exec = $this->DBConsulta($sql);

            $id = intval($exec[0]->sucurs_sucurs);

            $nSucurs = $serie;
            // $nFacturero = Funciones::zero_fill(1);
            $nSecuencia = Funciones::zero_fill(0, 9, STR_PAD_RIGHT);

            $nFacIni = $nSucurs . $nSecuencia;

            $sql = "UPDATE tb_sucurs SET
                sucurs_numncr = '$nFacIni',
                sucurs_numfac = '$nFacIni'
                WHERE sucurs_sucurs = $id
            ";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al guardar secuencia de facturas");

            $sql = "SELECT * FROM tb_sucurs WHERE sucurs_docume = '$docume'";

            $exec = $this->DBConsulta($sql);

            $exec[0]->sucurs_direcc = utf8_encode($exec[0]->sucurs_direcc);
            $exec[0]->sucurs_nombre = utf8_encode($exec[0]->sucurs_nombre);
            $exec[0]->sucurs_email = utf8_encode($exec[0]->sucurs_email);

            return Funciones::RespuestaJson(1, "Éxito al guardar sucursal", array("sucursal" => $exec[0]));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);

                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ObtenerSucursalCompan($data)
    {
        try {
            if (!isset($data['compan'])) throw new Exception("Debe estabelcer la compañia a buscar", 1);

            $id = intval(trim($data['compan']));

            $sql = "SELECT * FROM tb_sucurs WHERE sucurs_compan = $id";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("No hay datos para mostrar", 1);

            $items = array();

            foreach ($exec as $item) {

                $item->sucurs_direcc = utf8_encode($item->sucurs_direcc);
                $item->sucurs_nombre = utf8_encode($item->sucurs_nombre);
                $item->sucurs_email = utf8_encode($item->sucurs_email);
                $item->sucurs_numser = trim($item->sucurs_numfac);

                $items[] = $item;
            }

            return Funciones::RespuestaJson(1, "", array("sucursales" => $items));
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

            if (!isset($data['sucurs_sucurs'])) throw new Exception("Debe establecer el ID de sucursal", 1);
            if (!isset($data['sucurs_estado'])) throw new Exception("Debe establecer el nuevo estado de la sucursal", 1);

            $sucurs = intval(trim($data['sucurs_sucurs']));
            $estado = intval(trim($data['sucurs_estado']));

            $sql = "UPDATE tb_sucurs SET sucurs_estado = '$estado' WHERE sucurs_sucurs = $sucurs";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al actualizar", $sql);

            $sql = "SELECT *  FROM tb_sucurs WHERE sucurs_sucurs = $sucurs";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "Error al obtener la sucursal");

            $exec[0]->sucurs_direcc = utf8_encode($exec[0]->sucurs_direcc);
            $exec[0]->sucurs_nombre = utf8_encode($exec[0]->sucurs_nombre);
            $exec[0]->sucurs_email = utf8_encode($exec[0]->sucurs_email);

            return Funciones::RespuestaJson(1, "Actualizado con éxito", array("sucursal" => $exec[0]));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function UpdateSucursal($data)
    {
        try {
            if (!isset($data['sucurs_sucurs'])) throw new Exception("Debe establecer el número de sucursal", 1);
            // if (!isset($data['sucurs_docume'])) throw new Exception("Debe establecer el número de documento", 1);
            if (!isset($data['sucurs_nombre'])) throw new Exception("Debe establecer el nombre de la sucursal", 1);
            if (!isset($data['sucurs_email'])) throw new Exception("Debe eestablecer el correo electrónico", 1);
            if (!isset($data['sucurs_telefo'])) throw new Exception("Debe establecer el número de teléfono", 1);
            if (!isset($data['sucurs_direcc'])) throw new Exception("Debe establecer la dirección", 1);
            if (!isset($data['sucurs_compan'])) throw new Exception("Debe seleccionar la compañia", 1);
            if (!isset($data['sucurs_numser'])) throw new Exception("Debe establecer el número de serie", 1);

            $id = intval(trim($data['sucurs_sucurs']));
            $nombre = utf8_decode(trim($data['sucurs_nombre']));
            $email = utf8_decode(trim($data['sucurs_email']));
            $telefo = trim($data['sucurs_telefo']);
            $direcc = utf8_decode(trim($data['sucurs_direcc']));
            $compan = intval($data['sucurs_compan']);
            $serie = trim($data['sucurs_numser']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Formato de correo no válido", 1);

            $cambioProvincia = false;

            $sqlProvincia = "SELECT * FROM tb_sucurs WHERE sucurs_sucurs = $id and sucurs_compan = $compan";

            $exec = $this->DBConsulta($sqlProvincia);

            $sucursal = $exec[0];

            if (count($exec) > 0) {

                if ($compan != $sucursal->sucurs_compan) {
                    $cambioProvincia = true;
                }
            }

            $factur = $serie . substr($sucursal->sucurs_numfac, 6);
            $notCre = $serie . substr($sucursal->sucurs_numncr, 6);

            $sqlActualizar = "UPDATE tb_sucurs SET 
                sucurs_nombre = '$nombre',
                sucurs_email = '$email',
                sucurs_direcc = '$direcc',
                sucurs_telefo = '$telefo',
                sucurs_numncr = '$notCre',
                sucurs_numfac = '$factur'
                WHERE sucurs_sucurs = $id
            ";

            $exec = $this->DBConsulta($sqlActualizar, true);

            if (!$exec) throw new Exception("Error al actualizar la sucursal", 1);

            $sqlProvincia = "SELECT * FROM tb_sucurs WHERE sucurs_sucurs = $id";

            $exec = $this->DBConsulta($sqlProvincia);

            if (count($exec) == 0) throw new Exception("Error al obtener la sucursal", 1);

            $sucur = $exec[0];

            $sucur->sucurs_direcc = utf8_encode($exec[0]->sucurs_direcc);
            $sucur->sucurs_nombre = utf8_encode($exec[0]->sucurs_nombre);
            $sucur->sucurs_email = utf8_encode($exec[0]->sucurs_email);

            return Funciones::RespuestaJson(1, "Actualizado con éxito", array("sucursal" => $sucur, "cambioProvincia" => $cambioProvincia));
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
