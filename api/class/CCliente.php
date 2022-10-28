<?php

class Cliente extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        parent::DBConexion();

        date_default_timezone_set("America/Guayaquil");
    }

    public function ObtenerCliente($data)
    {
        try {
            if (!isset($data['query'])) throw new Exception("No se ha establecido el parametro de busqueda", 1);
            if (!isset($data['compan'])) throw new Exception("No se ha establecido la compañia", 1);

            $condicion = "";

            $query = trim($data['query']);
            $compan = intval(trim($data['compan']));

            if (is_numeric($query)) {

                if (strlen($query) < 10) throw new Exception("Longitud de carácteres no permitido", 1);

                $condicion = "client_cedcli = '$query'";
            } else {
                $query = strtolower($query);
                $condicion = "LOWER(client_apenom) LIKE '%$query%'";
            }

            $sql = "SELECT * FROM tb_client WHERE $condicion AND client_empres = $compan";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) {
                $tipo = "";

                if (is_numeric($query)) {
                    $tipo = "client_cedcli";
                } else {
                    $tipo = "client_apenom";
                }

                $usuario["usuario"][$tipo] = $query;
                return Funciones::RespuestaJson(2, "Usuario no encontrado", $usuario);
            } else {

                $items = array();

                foreach ($exec as $item) {
                    $item->clpv_cli_clpv = $item->clpv_nom_clpv . " " . $item->clpv_ape_clpv;

                    $items[] = $item;
                }

                return Funciones::RespuestaJson(1, "Usuarios encontrado", array("clientes" => $items));
            }
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ObtenerClientes($data)
    {

        try {
            if (!isset($data['compan'])) throw new Exception("Debe establecer el ID de compañia", 1);

            $id = intval(trim($data['compan']));

            $sql = "SELECT * FROM tb_client WHERE client_empres = $id";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("No hay datos para mostrar", 1);

            $items = array();

            foreach ($exec as $item) {
                $item->client_apelli = utf8_encode($item->client_apelli);
                $item->client_nombre = utf8_encode($item->client_nombre);
                $item->client_apenom = utf8_encode($item->client_apenom);
                $item->client_climai = utf8_encode($item->client_climai);

                $items[] = $item;
            }

            return Funciones::RespuestaJson(1, "", array("clientes" => $items));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function changeStatus($data)
    {
        try {
            if (!isset($data['client_codigo'])) throw new Exception("Debe establecer el ID del cliente", 1);
            if (!isset($data['client_estado'])) throw new Exception("Debe establecer el nuevoe estado", 1);

            $id = intval(trim($data['client_codigo']));
            $estado = intval(trim($data['client_estado']));

            $update = "UPDATE tb_client SET client_estado = $estado WHERE client_codigo = $id";

            $exec = $this->DBConsulta($update, true);

            if (!$exec) throw new Exception("Error al actualizar el estado", 1);

            $obtener = "SELECT * FROM tb_client WHERE client_codigo = $id";

            $exec = $this->DBConsulta($obtener);

            if (count($exec) == 0) throw new Exception("Error al obtener el cliente", 1);

            $item = $exec[0];

            $item->client_apelli = utf8_encode($item->client_apelli);
            $item->client_nombre = utf8_encode($item->client_nombre);
            $item->client_apenom = utf8_encode($item->client_apenom);
            $item->client_climai = utf8_encode($item->client_climai);

            return Funciones::RespuestaJson(1, "Actualizado con éxito", array("usuario" => $item));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function UpdateDate($data)
    {
        try {
            if (!isset($data['client_codigo'])) throw new Exception("Debe establecer el ID del cliente", 1);
            if (!isset($data['client_cedcli'])) throw new Exception("Debe establecer la cédula del cliente", 1);
            if (!isset($data['client_nombre'])) throw new Exception("Debe establecer los nombres del cliente", 1);
            if (!isset($data['client_apelli'])) throw new Exception("Debe establecer los apellidos del cliente", 1);
            if (!isset($data['client_climai'])) throw new Exception("Debe establecer el correo eléctronico del cliente", 1);
            if (!isset($data['client_clitlf'])) throw new Exception("Debe establecer el teléfono del cliente", 1);
            // if (!isset($data['client_empres'])) throw new Exception("Debe establecer la empresa", 1);
            // if (!isset($data['client_sucurs'])) throw new Exception("Debe establecer la sucursal", 1);

            $id = intval(trim($data['client_codigo']));
            $cedula = trim($data['client_cedcli']);
            $nombres = utf8_decode(trim($data['client_nombre']));
            $apellidos = utf8_decode(trim(($data['client_apelli'])));
            $correo = trim($data['client_climai']);
            $telefono = trim($data['client_clitlf']);
            // $compan = intval(trim($data['client_empres']));
            // $sucurs = intval(trim($data['client_sucurs']));

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) throw new Exception("Formato de correo no válido", 1);

            $sqlUpdate = "UPDATE tb_client SET
                        client_cedcli = '$cedula',
                        client_nombre = '$nombres',
                        client_apelli = '$apellidos',
                        client_climai = '$correo',
                        client_clitlf = '$telefono'
                        WHERE client_codigo = $id
                        ";

            $exec = $this->DBConsulta($sqlUpdate, true);

            if (!$exec) throw new Exception("Error al actualizar lo datos", 1);

            $sqlObtner = "SELECT * FROM tb_client WHERE client_codigo = $id";

            $exec = $this->DBConsulta($sqlObtner);

            if (count($exec) == 0) throw new Exception("Error al obtener datos del cliente", 1);

            $item = $exec[0];

            $item->client_apelli = utf8_encode($item->client_apelli);
            $item->client_nombre = utf8_encode($item->client_nombre);
            $item->client_apenom = utf8_encode($item->client_apenom);
            $item->client_climai = utf8_encode($item->client_climai);

            return Funciones::RespuestaJson(1, "Éxito al actualizar", array("usuario" => $item));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function GuardarData($data)
    {
        try {
            if (!isset($data['client_cedcli'])) throw new Exception("Debe establecer la cédula del cliente", 1);
            if (!isset($data['client_nombre'])) throw new Exception("Debe establecer los nombres del cliente", 1);
            if (!isset($data['client_apelli'])) throw new Exception("Debe establecer los apellidos del cliente", 1);
            if (!isset($data['client_climai'])) throw new Exception("Debe establecer el correo eléctronico del cliente", 1);
            if (!isset($data['client_clitlf'])) throw new Exception("Debe establecer el teléfono del cliente", 1);
            if (!isset($data['client_empres'])) throw new Exception("Debe establecer la empresa " . $data['client_empres'], 1);
            // if (!isset($data['client_sucurs'])) throw new Exception("Debe establecer la sucursal", 1);

            $cedula = trim($data['client_cedcli']);
            $nombres = utf8_decode(trim($data['client_nombre']));
            $apellidos = utf8_decode(trim(($data['client_apelli'])));
            $correo = trim($data['client_climai']);
            $telefono = trim($data['client_clitlf']);
            $compan = intval(trim($data['client_empres']));
            $sucurs = 0;

            $apenom = $apellidos . " " . $nombres;

            $sqlExiste = "SELECT * FROM tb_client WHERE client_cedcli = '$cedula'";

            $exec = $this->DBConsulta($sqlExiste);

            if (count($exec) > 0) throw new Exception("Ya existe cliente con ese número de documento", 1);

            $sqlInsert = "INSERT INTO tb_client (client_cedcli, client_nombre, client_apelli, client_climai, client_clitlf, client_empres, client_sucurs, client_apenom)
                                        VALUES ('$cedula', '$nombres', '$apellidos', '$correo', '$telefono', '$compan', '$sucurs', '$apenom')";

            $exec = $this->DBConsulta($sqlInsert, true);

            if (!$exec) throw new Exception("Error al guardar los datos del cliente", 1);

            $sqlObtner = "SELECT * FROM tb_client WHERE client_cedcli = '$cedula'";

            $exec = $this->DBConsulta($sqlObtner);

            if (count($exec) == 0) throw new Exception("Error al obtener datos del cliente", 1);

            $item = $exec[0];

            $item->client_apelli = utf8_encode($item->client_apelli);
            $item->client_nombre = utf8_encode($item->client_nombre);
            $item->client_apenom = utf8_encode($item->client_apenom);
            $item->client_climai = utf8_encode($item->client_climai);
            $item->apenom = utf8_encode($item->apenom);

            return Funciones::RespuestaJson(1, "Éxito al actualizar", array("usuario" => $item));
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
