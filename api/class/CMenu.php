<?php

class Menu extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        parent::DBConexion();

        date_default_timezone_set("America/Guayaquil");
    }

    public function ObtenerMenu($data)
    {
        try {
            $id = intval(trim($data['id']));

            $sqlAccesos = "SELECT DISTINCT men.menweb_menweb, men.* 
            FROM tb_menweb as men
            INNER JOIN tb_acceso AS acc
            ON acc.acceso_idpadr = men.menweb_menweb
            WHERE men.menweb_estado = 1
            AND acc.acceso_usuari = $id";

            $exec = $this->DBConsulta($sqlAccesos);

            if (count($exec) == 0) throw new Exception("No tiene modulos asignado", 1);

            $items = array();

            foreach ($exec as $item) {
                $idpadre = intval($item->menweb_menweb);

                $sql = "SELECT men.* 
                FROM tb_menweb as men
                INNER JOIN tb_acceso AS acc
                ON acc.acceso_idmenu = men.menweb_menweb
                WHERE men.menweb_estado = 1
                AND acc.acceso_usuari = $id
                AND acc.acceso_idpadr = $idpadre
                ";

                $execSub = $this->DBConsulta($sql);

                if (count($execSub) > 0) {
                    $subMenu = array();

                    foreach ($execSub as $subMenuItem) {

                        $subMenuItem->menweb_modulo = strtolower($subMenuItem->menweb_modulo);
                        $subMenuItem->menweb_ventan = utf8_encode($subMenuItem->menweb_ventan);

                        $subMenu[] = $subMenuItem;
                    }

                    $item->menweb_submenu = $subMenu;
                } else {
                    $item->menweb_submenu = array();
                }

                $items[] = $item;
            }

            // $sql = "SELECT * FROM tb_menweb WHERE menweb_ispadr = '1' AND menweb_estado = '1' ORDER BY menweb_ordvis ASC";

            // $exec = $this->DBConsulta($sql);

            // if (count($exec) == 0) throw new Exception("No hay datos para mostrar", 1);

            // $items = array();

            // foreach ($exec as $item) {

            //     $id = intval($item->menweb_menweb);

            //     $sql = "SELECT * FROM tb_menweb WHERE menweb_ispadr = '2' AND menweb_idpadr = $id AND menweb_estado = '1' ORDER BY menweb_ordvis ASC";

            //     $execItem = $this->DBConsulta($sql);

            //     if (count($execItem) == 0) {
            //         $item->menweb_submenu = array();
            //     } else {
            //         $subMenu = array();

            //         foreach ($execItem as $subMenuItem) {

            //             $subMenuItem->menweb_modulo = strtolower($subMenuItem->menweb_modulo);
            //             $subMenuItem->menweb_ventan = utf8_encode($subMenuItem->menweb_ventan);

            //             $subMenu[] = $subMenuItem;
            //         }

            //         $item->menweb_submenu = $subMenu;
            //     }

            //     $item->menweb_modulo = strtolower($item->menweb_modulo);
            //     $item->menweb_ventan = utf8_decode($item->menweb_ventan);

            //     $items[] = $item;
            // }

            return Funciones::RespuestaJson(1, "", array("menu" => $items));
        } catch (Exception $e) {

            $mensaje = $e->getMessage();

            if ($e->getCode() != 1) {
                Funciones::escribirLogs(basename(__FILE__), $e);
                $mensaje = "Error interno del servidor";
            }

            return Funciones::RespuestaJson(2, $mensaje);
        }
    }

    public function CambiarEstado($data)
    {
        try {
            if (!isset($data['menweb_menweb'])) throw new Exception("Debe establecer el id del menú", 1);
            if (intval($data['menweb_menweb']) == 0) throw new Exception("Debe establecer el id del menú", 1);

            if (!isset($data['menweb_estado'])) throw new Exception("Debe establecer el nuevo estado", 1);

            $id = intval($data['menweb_menweb']);
            $estado = intval($data['menweb_estado']);

            $sql = "UPDATE tb_menweb SET menweb_estado = $estado WHERE menweb_menweb = $id";

            $exec = $this->DBConsulta($sql, true);

            if (!$exec) return Funciones::RespuestaJson(2, "Error al actualizar el estado");

            $sql = "SELECT * FROM tb_menweb WHERE menweb_menweb = $id";

            $exec = $this->DBConsulta($sql);

            if (count($exec) == 0) return Funciones::RespuestaJson(2, "Error al obtener el menú");

            return Funciones::RespuestaJson(1, "", array("menudata" => $exec[0]));
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
