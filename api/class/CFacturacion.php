<?php

class Facturacion extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        parent::DBConexion();

        date_default_timezone_set("America/Guayaquil");
    }

    public function GuardarFactura($data)
    {

        try {
            // return Funciones::RespuestaJson(2, "", $data);
            // && (trim($data['client_cedula']) === "0999999999999" || trim($data['client_cedula']) === "0999999999") && ((strlen(trim($data['client_cedula'])) === 13 || strlen(trim($data['client_cedula']))) === 10)

            if (floatval($data['total']) > 50 && (trim($data['client_cedula']) === "9999999999999" || trim($data['client_cedula']) === "0999999999")) return Funciones::RespuestaJson(2, "No debe superar los $200 en consumidor final");

            // return Funciones::RespuestaJson(9, "", $data);

            $id = 0;
            $codEmpresa  = isset($data['empresa']) ? intval($data['empresa']) : 0;
            $sucursal  = isset($data['sucursal']) ? intval($data['sucursal']) : 0;
            $tipoDocumento  = isset($data['tipoDoc']) ? ($data['tipoDoc']) : "";
            $fechaFac = date("m/d/Y", strtotime(trim($data['fechaFac'])));

            if ($codEmpresa == 0) throw new Exception("Debe establecer la empresa", 1);
            if ($sucursal == 0) throw new Exception("Debe establecer la sucursal", 1);

            $documento = trim($data['client_cedula']);
            $cliente = utf8_decode(trim($data['client_nombre']));
            $telefono = trim($data['client_clitlf']);
            $email = trim($data['client_correo']);
            $direccion = utf8_decode(trim($data['client_direcc']));

            if (intval($data['client_client']) > 0) {
                $id = intval($data['client_client']);

                $sqlUpdate = "UPDATE TB_CLIENT SET client_nombre = '$cliente', client_clitlf = '$telefono', client_correo = '$email', client_direcc = '$direccion' WHERE client_client = $id";

                $execUpd = $this->DBConsulta($sqlUpdate, true);
            } else {
                // SI NO EXISTE GUARDAR EL CLIENTE

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return Funciones::RespuestaJson(2, "Formato de correo electrónico no válido");

                $sql = "SELECT * FROM tb_client WHERE client_cedula = '$documento' AND client_empres = $codEmpresa";

                $result = $this->DBConsulta($sql);

                if (count($result) > 0) {
                    $id = intval($result[0]->client_client);

                    $sqlUpdate = "UPDATE TB_CLIENT SET client_nombre = '$cliente', client_clitlf = '$telefono', client_correo = '$email', client_direcc = '$direccion' WHERE client_client = $id";

                    $execUpd = $this->DBConsulta($sqlUpdate, true);
                } else {
                    $sql = "INSERT INTO tb_client (client_cedula, client_nombre, client_empres,  client_clitlf, client_correo, client_direcc) 
                                            VALUES ('$documento', '$cliente', $codEmpresa, '$telefono', '$email', '$direccion')";

                    $result = $this->DBConsulta($sql, true);

                    if (!$result) return Funciones::RespuestaJson(2, "Error al registrar usuario");

                    $sql = "SELECT * FROM tb_client WHERE client_cedula = '$documento' AND client_empres = $codEmpresa";

                    $result = $this->DBConsulta($sql);

                    if (count($result) == 0) return Funciones::RespuestaJson(2, "Usuario no encontrado");

                    $usuario = $result[0];

                    $id = $usuario->client_client;
                }
            }

            $iva = floatval($data['iva']);
            $total = floatval($data['total']);
            $subtotal = floatval($data['subtotal']);
            $observacion = utf8_encode(trim($data['observacion']));
            $porcDsct = intval($data['descuento']);
            $valDsct = $data['valDesc'];
            $obsFac = utf8_encode(trim($data['facweb_obsfac']));

            // GUARDAR CABECERA DE FACTURA
            // $fecha = date("d/n/Y");
            $sql = "INSERT INTO tb_facweb (facweb_obsfac, facweb_descue, facweb_valdesc, facweb_facfech, facweb_client, facweb_observ, facweb_subtot, facweb_valiva, facweb_totfac, facweb_compan, facweb_sucurs, facweb_tipdoc)
                                    VALUES( '$obsFac', $porcDsct, '$valDsct', '$fechaFac', $id, '$observacion', '$subtotal', '$iva', '" . ($total - $valDsct) . "', '$codEmpresa', $sucursal, '$tipoDocumento')";

            $exec = $this->DBConsulta($sql, TRUE);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al guardar la cabecera de la factura");

            // MAXIMO ID DE FACTURA
            $sql = "SELECT MAX(facweb_facweb) AS id FROM tb_facweb WHERE facweb_client = $id";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "Error al buscar la cabecera de la factura");

            $idFactura = intval($exec[0]->id);

            $detallePedido = $data['productos'];
            $i = 0;

            foreach ($detallePedido as $item) {
                $idProduc = intval($item['produc_produc']);
                $nombre = utf8_decode(trim($item['produc_nombre']));
                $cantidad = intval($item['produc_cantid']);
                $precio = number_format(str_replace(",", "", $item['produc_precio']), 2);
                $iva = intval($item['produc_poriva']);
                $codigo = $item['produc_codigo'];
                $observacion = isset($item['produc_observ']) ? utf8_decode($item['produc_observ']) : "";

                $valorIva = $iva / 100;
                $ivaTotal = $precio * $valorIva;

                $sqlGuardar = "INSERT INTO tb_detfac (detfac_facweb, detfac_produc, detfac_nombre, detfac_cantid, detfac_precio, detfac_valiva, detfac_valtot, detfac_codigo, detfac_detalle)
                                            VALUES ($idFactura, $idProduc, '$nombre', $cantidad, '$precio', '$ivaTotal', '" . ($precio + $ivaTotal) . "', '$codigo', '$observacion')";

                $exec =  $this->DBConsulta($sqlGuardar, true);

                if ($exec) {
                    $i++;
                }
            }

            if ($i != count($detallePedido)) return Funciones::RespuestaJson(2, "Error al guardar detalle de factura");

            $formasPago = $data['formasPagos'];
            $e = 0;

            foreach ($formasPago as $item) {
                $tipo = intval(trim($item['forpag_forpag']));
                $nombre = utf8_decode(trim($item['forpag_nombre']));
                $pago = number_format(trim(str_replace(",", "", $item['forpag_valtot'])), 2);

                $sqlGuardar = "INSERT INTO tb_pagweb (pagweb_facweb, pagweb_forPag, pagweb_descri, pagweb_valPag)
                                                    VALUES ($idFactura, '$tipo', '$nombre', '$pago')";

                $exec = $this->DBConsulta($sqlGuardar, true);

                if ($exec) {
                    $e++;
                }
            }

            if ($e != count($formasPago)) return Funciones::RespuestaJson(2, "Error al guardar las formas de pagos");

            // OBTENER LA SECUENCIA DE FACTURA
            $sql = "SELECT * FROM tb_sucurs WHERE sucurs_sucurs = $sucursal AND sucurs_compan = $codEmpresa";
            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "Error al obtener la secuencia de la factura ");

            $factura = $exec[0];

            $numero_documento = 0;
            $ambiente = 0;
            $tipo = "";
            $tipoDoc = "";

            if (strtoupper(trim($tipoDocumento)) == "F") {
                $tipo = "sucurs_numfac";
                $tipoDoc = "facweb_numfac";
                $num = ($factura->sucurs_numfac);
                $numero_documento = $num + 1;
                $ambiente = 01;
            } else {
                $tipo = "sucurs_numncr";
                $tipoDoc = "facweb_numncr";
                $num = ($factura->sucurs_numncr);
                $numero_documento = ($num) + 1;
                $ambiente = 04;
            }

            $numero_documento = str_pad($numero_documento, 15, "0", STR_PAD_LEFT);

            $sql = "UPDATE tb_sucurs SET $tipo='$numero_documento' WHERE sucurs_sucurs = $sucursal AND sucurs_compan = $codEmpresa";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) throw new Exception("Error al actualizar la secuencia", 1);

            // PARA OBTENER EL NUMERO DE RUC
            $sql = "SELECT * FROM tb_compan WHERE compan_compan = $codEmpresa";
            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return;

            $ruc = trim($exec[0]->compan_docume);

            date_default_timezone_set('America/Guayaquil');
            $cadena = date("dmY") . "-$ambiente-$ruc-2-$numero_documento-123456781";
            $numeroAutorizacion = Funciones::GenerarAutorizacion($cadena);
            $cadena = (string)str_replace("-", "",  $cadena) . $numeroAutorizacion;
            $sql = "UPDATE tb_facweb SET $tipoDoc='$numero_documento', facweb_numaut = '$cadena' WHERE facweb_facweb = $idFactura";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) throw new Exception("Error al añadir la factura", 1);

            $hostFac = "//" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/api/factura.php?idFactura=$idFactura";

            $mensaje = "";

            if ($tipoDocumento == 'F') {
                $mensaje = "la factura";
            } else {
                $mensaje = "la nota de crédito";
            }

            return Funciones::RespuestaJson(1, "Éxito al guardar $mensaje", array('factura' => $numero_documento, "pedido" => $hostFac));
        } catch (Exception $e) {

            Funciones::escribirLogs(basename(__FILE__), $e);

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) $mensaje = "Error interno del servidor";

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function ListarFactura($data)
    {
        try {
            if (!isset($data['empresa'])) throw new Exception("Debe establecer la empresa", 1);
            if (!isset($data['tipo_documento'])) throw new Exception("Debe establecer el tipo de documento", 1);

            $codEmpresa  = isset($data['empresa']) ? intval($data['empresa']) : 0;
            $tipo_documento = isset($data['tipo_documento']) ? $data['tipo_documento'] : 'F';

            $fechaInicio = isset($data['fechaI']) ? date("m/d/Y", strtotime($data['fechaI'])) : date("m/d/Y");
            $fechaFin = isset($data['fechaF']) ? date("m/d/Y", strtotime($data['fechaF'])) : date("m/d/Y");

            $sql = "SELECT * FROM TB_COMPAN WHERE COMPAN_COMPAN = $codEmpresa";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) throw new Exception("No existe la empresa", 1);

            $sqlFac = "SELECT ( SELECT CLIENT_NOMBRE FROM tb_client WHERE client_client = facweb_client ) cliente,  facweb_facweb, facweb_numfac, facweb_numncr, facweb_facfech, facweb_subtot, facweb_valiva, facweb_totfac 
                FROM tb_facweb 
                WHERE facweb_compan = $codEmpresa
                AND FACWEB_TIPDOC = '$tipo_documento'
                AND facweb_facfech BETWEEN '$fechaInicio' AND '$fechaFin'";

            $exec = $this->DBConsulta($sqlFac);

            if (count($exec) == 0) throw new Exception("No hay datos para mostrar", 1);

            $subTot = 0;
            $valIva = 0;
            $valTot = 0;
            $items = array();
            $idFacturas = array();

            foreach ($exec as $item) {
                $item->cliente = utf8_decode($item->cliente);

                $subTot += ($item->facweb_subtot);
                $valIva += ($item->facweb_valiva);
                $valTot += ($item->facweb_totfac);
                
                $item->facweb_subtot = number_format($item->facweb_subtot, 2, ',', '.');
                $item->facweb_valiva = number_format($item->facweb_valiva, 2, ',', '.');
                $item->facweb_totfac = number_format($item->facweb_totfac, 2, ',', '.');

                $item->itemFac = "//" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/api/factura.php?idFactura=" . $item->facweb_facweb;

                if (!in_array(intval($item->facweb_facweb), $idFacturas)) {
                    $idFacturas[] = $item->facweb_facweb;
                }

                $items[] = $item;
            }

            $reporFac['subtotal'] = number_format($subTot, 2, ',', '.');
            $reporFac['valoriva'] = number_format($valIva, 2, ',', '.');
            $reporFac['TotalFac'] = number_format($valTot, 2, ',', '.');
            $reporFac['facturas'] = $items;

            $sqlFormasPago = "SELECT PAGWEB_FORPAG, PAGWEB_DESCRI, SUM(PAGWEB_VALPAG) TOTAL FROM tb_pagweb WHERE pagweb_facweb IN (" . implode(',', $idFacturas) . ") GROUP BY PAGWEB_FORPAG, PAGWEB_DESCRI";

            $execFormasPago = $this->DBConsulta($sqlFormasPago);

            $itemsFormasPago = array();

            if (count($execFormasPago) > 0) {

                foreach ($execFormasPago as $item) {

                    $item->total = number_format($item->total, 2, ',', '.');

                    $itemsFormasPago[] = $item;
                }
            }

            $reporFac['formasPago'] = $itemsFormasPago;

            return Funciones::RespuestaJson(1, "", array("detalleFactura" => $reporFac));
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
