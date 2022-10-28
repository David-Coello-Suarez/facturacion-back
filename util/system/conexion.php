<?php
class Conexion
{
    private $dns;
    private $usuario;
    public $conexion;
    private $contrasena;

    public function __construct()
    {
        $this->dns = DNS;
        $this->usuario = USER;
        $this->contrasena = PASS;
    }

    public function DBConexion()
    {
        try {
            $this->conexion = odbc_connect($this->dns, $this->usuario, $this->contrasena);
  } catch (PDOException $e) {
            Funciones::escribirLogs("DBConexion", "Error de conexion => " . $e);
            die("Error de conexión. " . $e->getMessage());
        }
    }

    // CU = CREATE O UPATE
    public function DBConsulta($sql,  $CU = false)
    {
        try {
            if (!$CU) {
                $data = odbc_exec($this->conexion, $sql);

                $items = array();

                while ($row = odbc_fetch_object($data)) {
                    $items[] = $row;
                }

                return $items;
            } else {

                $estadoGuardar = odbc_exec($this->conexion, $sql);

                return $estadoGuardar;
            }
        } catch (Exception $e) {
            Funciones::escribirLogs("DBConsulta", "( " . $sql . " ) => " . $e);
            die("Error de petición. (" . $sql . ") => " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        if ($this->conexion) {
            $this->conexion = null;
        }
    }
}
