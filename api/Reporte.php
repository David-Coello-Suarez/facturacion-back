<?php

// // phpinfo();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (!isset($_GET['fechaI'])) die("DEBE ESTABLECER LA FECHA DE INICIO");

if (!isset($_GET['fechaF'])) die("DEBE ESTABLECER LA FECHA DE FINALIZACIÓN");

if (!isset($_GET['tipoDocumento'])) die("DEBE ESTABLECER EL TIPO DE DOCUMENTO");

require_once "../config.php";
require_once "../util/system/conexion.php";
$conexion = new Conexion();
$conexion->DBConexion();

$fechaI = $_GET['fechaI'];
$fechaF = $_GET['fechaF'];
$tipoDocumento = strtoupper($_GET['tipoDocumento']);
$compan = intval($_GET['compan']);

$sql = "SELECT ( SELECT CLIENT_NOMBRE FROM tb_client WHERE client_client = facweb_client ) cliente, facweb_descue, facweb_facweb, facweb_numfac, facweb_numncr, facweb_facfech, facweb_subtot, facweb_valiva, facweb_totfac 
FROM TB_FACWEB 
WHERE  FACWEB_TIPDOC = '$tipoDocumento'
AND FACWEB_COMPAN = $compan
AND FACWEB_FACFECH BETWEEN DATE('$fechaI') AND DATE('$fechaF')";

$exec = $conexion->DBConsulta($sql);

if (count($exec) == 0) die("NO HAY DATOS PARA MOSTRAR 1 $sql");

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->setActiveSheetIndex(0);

$tipoExcel = "FACTURAS";

if ($tipoDocumento == "N") {
    $tipoExcel = "NOTA DE CREDITO";
}

$abecedario = ["a", "b", "c", "d", "e", "F", "G", "H"];
$cabecera = [$tipoExcel, "NOMBRE CLIENTE", "subtotal", "DSCTO.", "graba I.V.A.",  "no graba i.v.a.", "IVA", "total"];

if (count($abecedario) != count($cabecera)) die("LOMNGITUD DE CELDAS NO COINCIDEN CON LA CABECERA DEL REPORTE");

// Set document properties
$spreadsheet->getProperties()->setCreator('SISTEMA')
    ->setLastModifiedBy('')
    ->setTitle("REPORTES DE $tipoExcel")
    ->setCategory("REPORTE");

$lastColumns = $abecedario[count($abecedario) - 1];
$sheet->setCellValue('A2', "REPORTE DE $tipoExcel DESDE $fechaI HASTA $fechaF")->mergeCells("A2:" . $lastColumns . "2")->getStyle("A2:" . $lastColumns . "2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$fila = 4;

foreach ($cabecera as $key => $value) {
    $columna = strtoupper($abecedario[$key] . "$fila");

    $sheet->setCellValue($columna, strtoupper($value));
}

$fila++;

foreach ($exec as $item) {
    $value = array();

    // COLUMNA FAC - NCRE
    $value[] = $tipoDocumento == 'F' ? $item->facweb_numfac : $item->facweb_numncr;

    // CLIENTE
    $value[] = $item->cliente;

    // SUBTOTAL FACTURA
    $value[] = number_format($item->facweb_subtot, 2);

    // DESCUENTO
    $descuento = 0;

    if (intval($item->facweb_descue) > 0) {
        $valDesc = intval($item->facweb_descue) / 100;
        $descuento =  $item->facweb_subtot * $valDesc;
    }

    $value[] = number_format($descuento, 2);

    // GRABA IVA
    $value[] = number_format($item->facweb_subtot - $descuento, 2);

    // NO GRABA IVA
    $subtDesc = (int)($item->facweb_subtot - $descuento)*1000;
    $totMenIva = (int)($item->facweb_totfac - $item->facweb_valiva)*1000;

    $value[] = $subtDesc - $totMenIva;

    // IVA
    $value[] = number_format($item->facweb_valiva, 2);

    // TOTAL
    $value[] = number_format($item->facweb_totfac, 2);

    foreach ($value as $key => $valueInt) {
        $columna = strtoupper($abecedario[$key] . "$fila");

        $data = $valueInt;

        if (!is_string($valueInt)) {
            $data = strtoupper($valueInt);
        }

        $sheet->getColumnDimension($abecedario[$key])->setAutoSize(true);
        $sheet->setCellValue($columna, trim($data));
    }

    $value = array();
    $fila++;
}

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('REPORTE');

// // Set active sheet index to the first sheet, so Excel opens this as the first sheet
// $spreadsheet->setActiveSheetIndex(0);

$newName = "REPORTES-" . str_replace(" ", "-", $tipoExcel) . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=$newName");
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
// header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;

?>

<!-- <script>
    console.log("ok")
    window.close()
</script> -->