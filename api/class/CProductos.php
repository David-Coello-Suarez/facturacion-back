<?php

class Producto  extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        parent::DBConexion();

        date_default_timezone_set("America/Guayaquil");
    }

    public function ProductoSolicitados($data)
    {
        try {
            $empresa = intval($data);

            $sql = "SELECT * FROM tb_produc WHERE produc_compan = $empresa AND produc_estado IN('0','1') AND produc_isbotn IN ('1')";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "No hay datos para mostrar", $sql);

            $items = array();

            foreach ($exec as $item) {

                $item->produc_cantid = 1;

                $item->produc_precio = number_format($item->produc_precio, 2);

                $item->produc_nombre = utf8_encode($item->produc_nombre);

                $items[] = $item;
            }

            $result['productos'] = $items;

            return Funciones::RespuestaJson(1, "", $result);
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ObtenerProducto($data)
    {
        try {
            $codigo = strtolower(trim($data['codigo']));
            $nombre = strtolower(trim($data['nombre']));

            $empresa = intval($data['compania']);
            $sucursal = intval($data['sucursal']);

            $sqlWhere = "";

            if ($codigo != "") {
                $sqlWhere = "WHERE LOWER(produc_codigo) LIKE '%$codigo%'";
            } else {
                $sqlWhere = "WHERE LOWER(produc_nombre) LIKE '%$nombre%'";
            }

            $sql = "SELECT * FROM TB_PRODUC $sqlWhere AND produc_compan = $empresa AND produc_sucurs = $sucursal ORDER BY produc_produc";

            $execItem = $this->DBConsulta($sql);

            if (count($execItem) == 0) return Funciones::RespuestaJson(2, "No hay datos para mostrar",  $sql);

            $items = array();

            foreach ($execItem as $item) {

                $item->produc_cantid = 1;

                $item->produc_precio = number_format($item->produc_precio, 2);

                $item->produc_nombre = utf8_encode($item->produc_nombre);

                $items[] = $item;
            }

            $respuesta['productos'] = $items;

            return Funciones::RespuestaJson(1, "", $respuesta);
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ListarProductos($data)
    {
        try {
            $empresa = intval($data);

            $sql = "SELECT * FROM tb_produc WHERE produc_compan = $empresa AND produc_estado IN('0','1') ORDER BY produc_produc ASC";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "No hay datos para mostrar");

            $items = array();

            $cont = 1;

            foreach ($exec as $item) {

                $item->produc_posici = $cont;

                $item->produc_precio = number_format($item->produc_precio, 2);

                $item->produc_nombre = utf8_encode($item->produc_nombre);

                $cont++;

                $items[] = $item;
            }

            return Funciones::RespuestaJson(1, "", array("productos" => $items));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ChangeEstado($data)
    {
        try {
            $idProducto = intval($data['produc_produc']);
            $estado = intval($data['produc_isbotn']);

            $sql = "UPDATE tb_produc SET produc_isbotn = '$estado' WHERE produc_produc = $idProducto";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al procesar datos ");

            return Funciones::RespuestaJson(1, "", array("producto" => $data));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function CrearProducto($data)
    {
        try {
            $codigo = trim($data['codigo']);
            $nombre = utf8_decode(trim($data['nombre']));
            $precio = floatval($data['precio']);
            $iva = intval($data['iva']);
            $compania = isset($data['compania']) ? intval($data['compania']) : 1;
            // $sucursal = isset($data['sucursal']) ? intval($data['sucursal']) : 1;

            $sql = "SELECT * FROM tb_produc WHERE produc_codigo = '$codigo' AND produc_compan = $compania  AND produc_estado IN ('0','1')";

            $exec = $this->DBConsulta($sql);

            if (count($exec) > 0) return Funciones::RespuestaJson(2, "Código de producto ya existe");

            $sqlGuardar = "INSERT INTO tb_produc (produc_compan,  produc_codigo, produc_nombre, produc_precio, produc_poriva )
                                                VALUES ($compania,  '$codigo', '$nombre', '$precio', '$iva')";

            $exec = $this->DBConsulta($sqlGuardar, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al guardar " . $sqlGuardar);

            $sqlRec = "SELECT * FROM tb_produc WHERE produc_codigo = '$codigo' AND produc_compan = $compania AND produc_estado IN ('0','1')";

            $exec1 = $this->DBConsulta($sqlRec);

            if (count($exec1) == 0) return Funciones::RespuestaJson(2, "Error al recuperar el producto");

            $exec1[0]->produc_nombre = utf8_encode($exec1[0]->produc_nombre);

            $exec1[0]->produc_precio = number_format($exec1[0]->produc_precio, 2);

            return Funciones::RespuestaJson(1, "", array("producto" => $exec1[0]));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ActualizarProducto($data)
    {

        try {
            $id = intval(trim($data['idproducto']));
            $codigo = trim($data['codigo']);
            $nombre = trim($data['nombre']);
            $precio = floatval($data['precio']);
            $iva = intval($data['iva']);
            $compania = isset($data['compania']) ? intval($data['compania']) : 1;
            $sucursal = isset($data['sucursal']) ? intval($data['sucursal']) : 1;

            // BUSQUEDA DE PRODUCTO POR ID
            $sql = "SELECT * FROM tb_produc WHERE produc_produc = '$id' AND produc_compan = $compania AND produc_estado IN ('0','1')";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "Producto no existe");

            // BUSQUEDAD DE PRODUCTO POR CODIGO
            $sql = "SELECT * FROM tb_produc WHERE produc_codigo = '$codigo' AND produc_compan = $compania AND produc_estado IN ('0','1')";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "Código de producto ya existe");

            $sql = "UPDATE tb_produc SET 
            produc_codigo = '$codigo',
            produc_nombre = '$nombre',
            produc_precio = '$precio',
            produc_poriva = '$iva'
            WHERE produc_produc = '$id'            
            ";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al actualizar producto");

            $sql = "SELECT * FROM tb_produc WHERE produc_produc = '$id' AND produc_estado IN ('0','1')";

            $exec = $this->DBConsulta($sql);

            return Funciones::RespuestaJson(1, "", array("producto" => $exec[0]));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ProductoTipo($data)
    {
        try {
            $id = intval(trim($data['produc_produc']));
            $tipo = strtoupper(trim($data['produc_proser']));

            $sqlUpdate = "UPDATE tb_produc SET produc_proser = '$tipo' WHERE produc_produc = $id";

            $exec = $this->DBConsulta($sqlUpdate, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al actualizar");

            $sql = "SELECT * FROM tb_produc WHERE produc_produc = '$id'";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "Error al obtener la sucursal");

            return Funciones::RespuestaJson(1, "Actualizado con éxito", array("producto" => $exec[0]));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ProductoEditable($data)
    {
        try {
            $id = intval(trim($data['produc_produc']));
            $tipo = strtoupper(trim($data['produc_isedit']));

            $sqlUpdate = "UPDATE tb_produc SET produc_isedit = '$tipo' WHERE produc_produc = $id";

            $exec = $this->DBConsulta($sqlUpdate, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al actualizar");

            $sql = "SELECT * FROM tb_produc WHERE produc_produc = '$id'";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "Error al obtener la sucursal");

            return Funciones::RespuestaJson(1, "Actualizado con éxito", array("producto" => $exec[0]));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }
}
