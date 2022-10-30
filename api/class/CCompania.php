<?php

class Compania extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        parent::DBConexion();

        date_default_timezone_set("America/Guayaquil");
    }

    public function ListarCompanias()
    {
        try {
            $sql = "SELECT * FROM tb_compan WHERE compan_estado = '1'";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "No hay datos para mostrar");

            $items = array();

            foreach ($exec as $item) {

                $items[] = $item;
            }

            $data['companias'] = $items;

            return Funciones::RespuestaJson(1, "", $data);
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ListarCompaniasMan()
    {
        try {
            $sql = "SELECT * FROM tb_compan ORDER BY compan_nombre";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("No hay datos para mostrar ", 1);

            $items = array();

            foreach ($exec as $item) {

                $item->compan_nombre = utf8_encode($item->compan_nombre);

                $item->compan_feccad = date("Y-m-d", strtotime($item->compan_feccad));

                if($item->compan_ordvis == '' || $item->compan_ordvis == 0  ){
$item->ordvis = '';                    
                }

                $items[] = $item;
            }

            return Funciones::RespuestaJson(1, "", array("companias" => $items));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function CrearCompania($data)
    {
        try {
            // return Funciones::RespuestaJson(10, "", $data);

            if (!isset($data['compan_docume'])) throw new Exception("Debe establecer el número de documento", 1);
            if (!isset($data['compan_nombre'])) throw new Exception("Debe establecer el nombre de la empresa", 1);
            if (!isset($data['compan_telefo'])) throw new Exception("Debe establecer el número de telefono", 1);
            if (!isset($data['compan_email'])) throw new Exception("Debe establecer la dirección de correo", 1);
            // if (!isset($data['compan_resolu'])) throw new Exception("Debe establecer el número de resolución", 1);
            // if (!isset($data['compan_contri'])) throw new Exception("Debe establecer el número de contribuyente", 1);
            // if (!isset($data['compan_oblcon'])) throw new Exception("", 1);
            if (!isset($data['compan_firma'])) throw new Exception("Debe establecer la firma", 1);
            if (!isset($data['compan_feccad'])) throw new Exception("Debe establecer la fehca de caducidad", 1);
            if (!isset($data['compan_direcc'])) throw new Exception("Debe establecer la dirección", 1);
            if (!isset($data['compan_clave'])) throw new Exception("debe establecer la clave de acceso", 1);

            $documento = trim($data['compan_docume']);
            $nombre = utf8_decode(trim($data['compan_nombre']));
            $telefono = trim($data['compan_telefo']);
            $email = trim($data['compan_email']);
            $resulucion = utf8_decode(trim($data['compan_resolu']));
            $contribuyente = utf8_decode(trim($data['compan_contri']));
            $firma = trim($data['compan_firma']);
            $fechaCad = trim($data['compan_feccad']);
            $direccion = utf8_decode(trim($data['compan_direcc']));
            $clave = trim($data['compan_clave']);
            $oblCont = intval($data['compan_oblcon']);

            // $fechaCad = date("d/sm/Y", strtotime($fechaCad));
            $fechaCad = date("m/d/Y", strtotime($fechaCad));
            // return Funciones::RespuestaJson(1, "", $fechaCad);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Formato de correo electrónico no válido", 1);

            $sqlExiste = "SELECT * FROM tb_compan WHERE compan_docume = '$documento'";

            $exec = $this->DBConsulta($sqlExiste);

            if (count($exec) > 0) throw new Exception("Compañia con ese número de documento ya existe", 1);

            $sqlMax = "SELECT MAX(compan_ordvis) as maximo FROM tb_compan";

            $exec = $this->DBConsulta($sqlMax);

            if (count($exec) == 0) throw new Exception("Error al obtener el máximo items",  1);

            $max = intval($exec[0]->maximo) + 1;

            $sql = "INSERT INTO tb_compan (compan_docume, compan_contri, compan_oblcon, compan_resolu, compan_clave, compan_nombre, compan_direcc, compan_telefo, compan_email, compan_ordvis, compan_firma, compan_feccad) 
                                    VALUES ('$documento', '$contribuyente', '$oblCont', '$resulucion', '$clave', '$nombre', '$direccion', '$telefono', '$email', $max, '$firma', '$fechaCad')";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al guardar la nueva compañia - $sql");

            $sqlExiste = "SELECT * FROM tb_compan WHERE compan_docume = '$documento'";

            $exec = $this->DBConsulta($sqlExiste);

            $item = $exec[0];

            $rutamove = "/firmas/" . intval($item->compan_compan) . "/";

            if (!file_exists($rutamove)) mkdir($rutamove, 0777, true);

            $rutalOld = $data['compan_rutfil'];

            if (!copy($rutalOld, $rutamove . $data['compan_firma'])) throw new Exception("Error al subir el archivo ", 1);

            unlink($rutalOld);

            return Funciones::RespuestaJson(1, "Éxito al guardar", array("compania" => $item));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ActualizarCompan($data)
    {
        try {
            // return Funciones::RespuestaJson(1, "", $data);
            if (!isset($data['compan_nombre'])) throw new Exception("Debe establecer el nombre de la empresa", 1);
            if (!isset($data['compan_telefo'])) throw new Exception("Debe establecer el número de telefono", 1);
            if (!isset($data['compan_email'])) throw new Exception("Debe establecer la dirección de correo", 1);
            // if (!isset($data['compan_resolu'])) throw new Exception("Debe establecer el número de resolución", 1);
            // if (!isset($data['compan_contri'])) throw new Exception("Debe establecer el número de contribuyente", 1);
            // if (!isset($data['compan_oblcon'])) throw new Exception("", 1);
            if (!isset($data['compan_firma'])) throw new Exception("Debe establecer la firma", 1);
            if (!isset($data['compan_feccad'])) throw new Exception("Debe establecer la fehca de caducidad", 1);
            if (!isset($data['compan_direcc'])) throw new Exception("Debe establecer la dirección", 1);
            if (!isset($data['compan_clave'])) throw new Exception("debe establecer la clave de acceso", 1);

            $compan = intval($data['compan_compan']);
            $nombre = utf8_decode(trim($data['compan_nombre']));
            $telefono = trim($data['compan_telefo']);
            $email = trim($data['compan_email']);
            $resulucion = utf8_decode(trim($data['compan_resolu']));
            $contribuyente = utf8_decode(trim($data['compan_contri']));
            $firma = trim($data['compan_firma']);
            $fechaCad = trim($data['compan_feccad']);
            $direccion = utf8_decode(trim($data['compan_direcc']));
            $clave = trim($data['compan_clave']);
            $oblCont = intval($data['compan_oblcon']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Formato de correo electrónico no válido", 1);

            $fechaCad = date("m/d/Y", strtotime($fechaCad));

            $sql = "
                UPDATE tb_compan SET
                compan_contri = '$contribuyente',
                compan_oblcon = '$oblCont',
                compan_resolu = '$resulucion',
                compan_firma = '$firma',
                compan_clave = '$clave',
                compan_feccad = '$fechaCad',
                compan_nombre = '$nombre',
                compan_direcc = '$direccion',
                compan_telefo = '$telefono',
                compan_email = '$email'
                WHERE compan_compan = $compan
            ";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al actualizar compañia $sql");

            $sqlExiste = "SELECT * FROM tb_compan WHERE compan_compan = '$compan'";

            $exec = $this->DBConsulta($sqlExiste);

            return Funciones::RespuestaJson(1, "Éxito al actualizar compañia", array("compania" => $exec[0]));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function CambiarStatus($data)
    {
        try {
            if (!isset($data['compan_compan'])) throw new Exception("Debe establecer el ID de la compañia", 1);
            if (intval($data['compan_compan']) == 0) throw new Exception("EL ID de la compañia debe ser mayor a cero", 1);

            if (!isset($data['compan_estado'])) throw new Exception("Debe establecer el nuevo estado de la compañia", 1);

            $id = intval($data['compan_compan']);
            $estado = intval($data['compan_estado']);

            $sql = "UPDATE tb_compan SET compan_estado = $estado WHERE compan_compan = $id";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) throw new Exception("Error al actualizar la compañia - $sql", 1);

            $sql = "SELECT * FROM tb_compan WHERE compan_compan = $id";

            $obt = $this->DBConsulta($sql);

            if (!$obt) throw new Exception("Error al obtener la compañia", 1);

            $obt[0]->compan_nombre = utf8_encode($obt[0]->compan_nombre);

            return Funciones::RespuestaJson(1, "", array("compania" => $obt[0]));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ListarCompanSucurs()
    {
        try {
            $sqlEmpresas = "SELECT * FROM tb_compan WHERE compan_estado = '1'";

            $exec = $this->DBConsulta($sqlEmpresas);

            if (count($exec) === 0) throw new Exception("No hay datos para mostrar", 1);

            $items = array();

            foreach ($exec as $item) {

                $id = intval($item->compan_compan);

                $item->sucurs_direcc = utf8_encode($item->sucurs_direcc);
                $item->sucurs_nombre = utf8_encode($item->sucurs_nombre);
                $item->sucurs_email = utf8_encode($item->sucurs_email);

                $sqlSucursal = "SELECT * FROM tb_sucurs WHERE sucurs_compan = $id";

                $execSucurs = $this->DBConsulta($sqlSucursal);

                // if (count($execSucurs) > 0) {
                    $item->compan_sucurs = $execSucurs;

                    $items[] = $item;
                // }
            }

            return Funciones::RespuestaJson(1, "", array("compansucurs" => $items));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }
}
