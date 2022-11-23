<?php
require_once "../config.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

    <style>
        table th,
        table td {
            font-size: 0.80rem !important
        }

        table td:first-child {
            font-weight: bolder;
            text-align: right;
            font-size: .7rem !important;
        }

        table td:last-child {
            text-align: right;
        }


        table td.sumatoria:last-child {
            text-align: right;
            font-weight: bolder;
            font-size: .7rem !important;
        }


        .datos {
            font-size: .7rem !important;
            font-weight: bolder;
        }

        thead th {
            font-size: .7rem !important;
            vertical-align: baseline;
            border: 1px solid black;
        }

        table td {
            border: none;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        tfoot {
            border-top: 1px solid black;
        }
    </style>
</head>

<body>
    <div class="container mt-2">
        <div class="row">
            <div class="col-12">


                <?php
                if (!isset($_GET['idFactura'])) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        Debe establecer el número de factura
                    </div>

                <?php
                    return;
                }
                ?>

                <?php
                if (intval($_GET['idFactura']) == 0) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        El número de factura debe ser mayor a cero
                    </div>
                <?php
                    return;
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    require_once "../util/system/conexion.php";

    $conexion = new Conexion();
    $conexion->DBConexion();

    $id = intval($_GET['idFactura']);

    $sql = "SELECT * FROM tb_facweb WHERE facweb_facweb = $id";

    $exec = $conexion->DBConsulta($sql);
    $item = $exec[0];
    $factura = $item;

    // START EMPRESA Y SUCURSAL
    // START EMPRESA
    $emp = intval($item->facweb_compan);
    $sql = "SELECT * FROM tb_compan WHERE compan_compan = $emp";
    $exec = $conexion->DBConsulta($sql);
    $empresa = $exec[0];
    // END EMPRESA

    // START SUCURSAL
    $sucursal = intval($item->facweb_sucurs);
    $sql = "SELECT * FROM tb_sucurs WHERE sucurs_compan = $emp AND sucurs_sucurs = $sucursal";
    $exec = $conexion->DBConsulta($sql);
    $sucur = $exec[0];
    // END SUCURSAL

    // END START Y SUCURSAL
    ?>
    <div class="container">
        <div class="row">
            <div class="col-7">

                <div class="card border-0">
                    
                    <img src="LeerImg.php?image=<?php echo $sucur->sucurs_logsuc; ?>" alt="logo" width="200" height="150" class="card-img-top">

                    <div class="card-body">
                        <div class="border border-dark rounded row">
                            <div class="col-12">
                                <strong style="font-size: .6rem;">
                                    <?php print_r(strtoupper($empresa->compan_nombre)) ?>
                                </strong>
                            </div>
                            <div class="col-12" style="font-size: .6rem;margin-bottom: .6rem;font-weight: bolder;">
                                <!-- Dir Matriz: Loti. Satirion Frente a X de la Alborada Av. Felipe Pezo<br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Campuzano S/N y Tercer Paaje 32 N.O Mz: 020 -->

                                Dir Matriz: <?php print_r(ucfirst($empresa->compan_direcc)) ?>
                            </div>
                            <div class="col-12" style="font-size: .6rem;margin-bottom: .6rem;font-weight: bolder;">
                                <!-- Dir Suc: Loti. Satirion Frente a X de la Alborada Av. Felipe Pezo <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Campuzano S/N y Tercer Paaje 32 N.O Mz: 020 -->
                                Dir. Suc.: <?php print_r(ucfirst($sucur->sucurs_direcc)) ?>
                            </div>
                            <div class="col-12" style="font-size: .6rem;margin-bottom: .6rem;font-weight: bolder;">
                                Contribuyente Especial No. <?php print_r($empresa->compan_contri) ?><br />
                                OBLIGADO A LLEVAR CONTABILIDAD <?php print_r(intval($empresa->compan_oblcon) == 1 ? 'SI' : 'No') ?> <br />
                                Agente de Retención de acuerdo a:<br />
                                Resolución <?php print_r($empresa->compan_resolu) ?>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <div class="col" style="margin-top: 10.3rem;">
                <div class="border border-dark rounded row">
                    <div class="col-12 datos">R.U.C.: <?php print_r($empresa->compan_docume) ?></div>
                    <div class="col-12 datos"><?php echo strtoupper($item->facweb_tipdoc) == 'F' ? 'FACTURA' : 'NOTA DE CREDITO'  ?></div>
                    <div class="col-12 datos">NO.
                        <?php

                        if (strtoupper($item->facweb_tipdoc) == 'F') {
                            print_r($item->facweb_numfac);
                        } else {
                            print_r($item->facweb_numncr);
                        }

                        ?>
                    </div>
                    <div class="col-12 datos">
                        NUMERO DE AUTORIZACION <br />
                        <div style="font-size: .6rem;">
                            <?php echo $item->facweb_numaut ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // DATA START CLIENTE
        $sql = "SELECT *
        FROM tb_client 
        WHERE client_client = " . $item->facweb_client;
        $exec = $conexion->DBConsulta($sql);
        $cliente = $exec[0];
        // DATA END CLIENTE
        ?>


        <div class="row ms-1">
            <div class="col border border-dark rounded mt-1">
                <div class="row ">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6 datos">
                                Razón Social Nombres y Apellidos <?php echo strtoupper($cliente->client_nombre) ?>
                            </div>
                            <div class="col-6 datos" style="text-align: center;">
                                RUC / C.I.: <?php echo $cliente->client_cedula ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 datos">Fecha Emisión: <?php print_r(date("d-m-Y", strtotime($item->facweb_facfech))) ?></div>
                    <div class="col-12 datos">Dirección: <?php echo strtoupper(utf8_decode($cliente->client_direcc)) ?></div>
                </div>
            </div>
        </div>

        <div class="row ms-1 mt-2">
            <div class="col ps-0 pe-0">
                <?php

                $sql = "SELECT * FROM tb_detfac WHERE detfac_facweb = $id";
                // $sql = "SELECT * FROM tb_detfac WHERE detfac_facweb BETWEEN 10 AND 50";

                $exec = $conexion->DBConsulta($sql);
                ?>

                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                Cod<br /> Auxiliar
                            </th>
                            <th>
                                Cod<br /> Principal
                            </th>
                            <th class="text-center">
                                Cant.
                            </th>
                            <th class="text-center">
                                Descripción
                            </th>
                            <th>
                                Detalle<br /> Adicional
                            </th>
                            <th class="text-center" style="width: 12%;">
                                Precio<br /> Unitario
                            </th>
                            <th class="text-center" style="width: 12%;">
                                Precio<br /> Total
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $fila = "";
                        if (count($exec) == 0) {
                            $fila = "
                                <tr>
                                    <td>
                                        Sin datos para mostrar
                                    </td>
                                </tr>
                            ";

                            print_r($fila);
                            return;
                        }

                        $subtot = 0;
                        $subIva0 = 0;
                        $subjetoIva = 0;
                        $descIce = 0;
                        $propina = 0;

                        $iva = 0;

                        foreach ($exec as $item) {
                            $cantidad = intval($item->detfac_cantid);
                            $producto = utf8_encode($item->detfac_nombre);
                            $precio = number_format($item->detfac_precio, 2);
                            $valorTotal = number_format(($item->detfac_precio * $cantidad), 2);
                            $subtot += ($item->detfac_precio * $cantidad);
                            $codigo = $item->detfac_codigo;
                            $id = intval($item->detfac_detfac);
                            $detalle = utf8_encode($item->detfac_detalle);

                            if ($cantidad > 0) {
                                $iva += number_format($item->detfac_valiva, 2) * $cantidad;
                            }


                            $fila .= "
                                <tr>
                                    <td>
                                        $id
                                    </td>
                                    <td>
                                    $codigo
                                    </td>
                                    <td style='text-align: right'>
                                    $cantidad 
                                    </td>
                                    <td>
                                    $producto 
                                    </td>
                                    <td>
                                    $detalle
                                    </td>
                                    <td style='text-align: right'>
                                    $ $precio
                                    </td>
                                    <td>
                                    $ $valorTotal
                                    </td>
                                </tr>
                            ";
                        }
                        print_r($fila);
                        ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="4" rowspan="15">

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                SUBTOTAL 12.00%
                            </td>
                            <td class="sumatoria">$
                                <?php
                                print_r(number_format($subtot, 2));
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                SUBTOTAL 00.00%
                            </td>
                            <td class="sumatoria">
                                $ <?php print_r(number_format($subIva0, 2)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                SUBTOTAL No sujeto de IVA
                            </td>
                            <td class="sumatoria">
                                $ <?php print_r(number_format($descIce, 2)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                SUBTOTAL SIN IMPUESTOS
                            </td>
                            <td class="sumatoria">
                                $
                                <?php
                                print_r(number_format($subtot, 2));
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                DESCUENTO ICE
                            </td>
                            <td class="sumatoria">
                                $ <?php print_r(number_format($subjetoIva, 2)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                IVA 12.00%
                            </td>
                            <td class="sumatoria">
                                $
                                <?php
                                print_r(number_format($factura->facweb_valiva, 2));
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                PROPINA
                            </td>
                            <td class="sumatoria">
                                $ <?php print_r(number_format($propina, 2)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                DSCT <?php echo intval($factura->facweb_descue) ?> %
                            </td>
                            <td class="sumatoria">
                                $ <?php print_r(number_format($factura->facweb_valdesc, 2)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                VALOR TOTAL
                            </td>
                            <td class="sumatoria">
                                $
                                <?php
                                $total = ($subtot + $factura->facweb_valiva + $subIva0 + $subjetoIva + $descIce + $propina) - $factura->facweb_valdesc;

                                print_r(number_format($total, 2));
                                ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print()

            setTimeout(function() {
                window.close();
            }, 100);
        }
    </script>


</body>

</html>