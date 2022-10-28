<?php

class FormaPago extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        parent::DBConexion();

        date_default_timezone_set("America/Guayaquil");
    }

    public function ListarFormasPago($data)
    {
        try {
            if (!isset($data['forpag_compan'])) throw new Exception("Debe establecer la empresa", 1);

            $compan = intval(trim($data['forpag_compan']));

            $sql = "SELECT * FROM tb_forpag WHERE forpag_compan = $compan AND forpag_estado IN('1', '0') ORDER BY forpag_nombre";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("No hay datos para mostrar", 1);

            $items = array();
            $cont = 1;

            foreach ($exec as $item) {

                $item->forpag_valtot = "";

                $item->forpag_contad = $cont;

                $item->forpag_nombre = ucfirst(utf8_encode($item->forpag_nombre));

                $items[]  = $item;

                $cont++;
            }

            return Funciones::RespuestaJson(1, "", array("formaspago" => $items));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function CrearForma($data)
    {
        try {
            if (!isset($data['forpag_nombre'])) throw new Exception("Debe establecer el nombre de la forma de pago", 1);
            if (!isset($data['forpag_compan'])) throw new Exception("Debe establecer la empresa", 1);

            $compan = intval($data['forpag_compan']);
            $nombre = utf8_decode(trim($data['forpag_nombre']));

            $sql = "SELECT * FROM tb_forpag WHERE LOWER(forpag_nombre) = '" . strtolower($nombre) . "' AND forpag_compan = $compan AND forpag_estado IN ('1','0')";

            $exec = $this->DBConsulta($sql);

            if (count($exec) > 0) throw new Exception("La forma de pago ya éxiste", 1);

            $save = "INSERT INTO tb_forpag (forpag_compan, forpag_nombre) VALUES ($compan, '$nombre')";

            $execSave = $this->DBConsulta($save, true);

            if (!$execSave) throw new Exception("Error al guardar la nueva forma de pago", 1);

            $sql = "SELECT * FROM tb_forpag WHERE LOWER(forpag_nombre) = '" . strtolower($nombre) . "' AND forpag_compan = $compan";

            $execQuery = $this->DBConsulta($sql);

            if (count($execQuery) == 0) throw new Exception("Error al obtener la forma de pago", 1);

            $item = $execQuery[0];

            $item->forpag_nombre = ucfirst(utf8_encode($item->forpag_nombre));

            $item->forpag_contad = $this->TotalForma($compan);

            if (strlen($data['forpag_valtot']) > 0) {
                $item->forpag_valtot = number_format($data['forpag_valtot'], 2);
            }

            return Funciones::RespuestaJson(1, "Creado con éxito", array("formapago" => $item));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ActualizarForma($data)
    {
        try {
            if (!isset($data['forpag_forpag'])) throw new Exception("Debe establecer el ID de la forma de pago", 1);
            if (!isset($data['forpag_nombre'])) throw new Exception("Debe establecer el nombre de la forma de pago", 1);
            if (!isset($data['forpag_compan'])) throw new Exception("Debe establecer la empresa", 1);

            $compan = intval($data['forpag_compan']);
            $formapago = intval($data['forpag_forpag']);
            $nombre = utf8_decode(trim($data['forpag_nombre']));

            $sql = "SELECT * FROM tb_forpag WHERE LOWER(forpag_nombre) = '" . strtolower($nombre) . "' AND forpag_compan = $compan";

            $exec = $this->DBConsulta($sql);

            if (count($exec) > 0) throw new Exception("La forma de pago ya éxiste", 1);

            $actualizar = "UPDATE tb_forpag SET 
                    forpag_nombre = '$nombre',
                    forpag_compan = $compan
                    WHERE forpag_forpag = $formapago
                ";

            $exec = $this->DBConsulta($actualizar, true);

            if (!$exec) throw new Exception("Error al actualizar la forma de pago", 1);

            $sqlObtener = "SELECT * FROM tb_forpag WHERE forpag_forpag = $formapago";

            $execObtener = $this->DBConsulta($sqlObtener);

            $item = $execObtener[0];

            $item->forpag_nombre = ucfirst(utf8_decode($item->forpag_nombre));

            $item->forpag_contad = intval($data['forpag_contad']);

            return Funciones::RespuestaJson(1, "Actualizado con éxito", array("formapago" => $item));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function EstadoForma($data)
    {
        try {
            if (!isset($data['forpag_forpag'])) throw new Exception("Debe establecer el ID de la forma de pago", 1);
            if (!isset($data['forpag_estado'])) throw new Exception("Debe establecer el estado", 1);

            $estado = intval($data['forpag_estado']);
            $formapago = intval($data['forpag_forpag']);

            $sql = "UPDATE tb_forpag SET forpag_estado = '$estado' WHERE forpag_forpag = $formapago";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) throw new Exception("Error al atualizar la forma de pago", 1);

            $sqlObtener = "SELECT * FROM tb_forpag WHERE forpag_forpag = $formapago";

            $execObtener = $this->DBConsulta($sqlObtener);

            $item = $execObtener[0];

            $item->forpag_nombre = ucfirst(utf8_decode($item->forpag_nombre));

            $item->forpag_contad = intval($data['forpag_contad']);

if( $data['forpag_valtot'] != "" ){
    $item->forpag_valtot = number_format($data['forpag_valtot'], 2);
}

            return Funciones::RespuestaJson(1, "Actualizado con éxito", array("formapago" => $item));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }


    private function TotalForma($compan)
    {
        $sql = "SELECT COUNT(*) AS total FROM tb_forpag WHERE forpag_compan = $compan";

        $exec = $this->DBConsulta($sql);

        $total = intval($exec[0]->total);

        return $total;
    }
}
