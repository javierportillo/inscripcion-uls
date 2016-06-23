<?php

session_start();

// Si el usiario no ha iniciado sesión, redirigir al index.
if (isset($_SESSION['loggedin']) !== true ||
    isset($_SESSION['carnet']) !== true ||
    isset($_GET['data']) !== true ||
    $_SESSION['loggedin'] !== true
) {
    header('Location: ../public_html/');
    return false;
}

$data = json_decode($_GET['data'], true);
if (!$data) {
    echo "<p>No se obtuvieron suficientes datos para generar el archivo PDF.</p>";
    return false;
}

require_once '../vendor/autoload.php';
require_once 'database.php';
require_once 'utf8sprintf.php';
require_once 'generadornpe.php';

use Ayeo\Barcode;
use Noodlehaus\Config;

$conf = new Config('../config/config.json');

$full_name = getFullName($_SESSION['carnet']);
$carnet = $_SESSION['carnet'];
$carrera = obtenerCarreraAlumno($carnet);

$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetAutoPageBreak(false);
$pdf->Image('../img/uls_logo.jpg', 10, 10, 30, 0);
$pdf->Cell(150, 9, utf8_decode('Universidad Luterana Salvadoreña'), 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(50, 3, utf8_decode('NIT No. 0614-200789-106-8'), 0, 2, 'R');
$pdf->Cell(50, 3, utf8_decode('Registro No. 102972-0'), 0, 2, 'R');
$pdf->Cell(50, 3, utf8_decode('E-mail: uls@uls.edu.sv'), 0, 1, 'R');
$pdf->SetFontSize(12);
$pdf->Cell(116, 9, utf8_decode('Boleta de pagos'), 0, 0, 'R');
$pdf->Ln(14);
$pdf->SetFont('Courier', '', 12);

$data_len = count($data['pagos_seleccionados']);
$page_height = $pdf->GetPageHeight();
$mascara_printf = '%12s: %s';
for ($i=0; $i < $data_len; $i++) {

    $cod_pago = $data['pagos_seleccionados'][$i];
    $conf_lista_tipos = $conf->get('tipos_de_pago');

    if (isset($conf_lista_tipos[$cod_pago]) === false) {
        continue;
    }

    $tipo_pago = $conf_lista_tipos[$cod_pago]['tipo'];
    $nombre_pago = $conf_lista_tipos[$cod_pago]['nombre'];
    $precio_pago = $conf_lista_tipos[$cod_pago]['precio'];

    $filename = 'barcode' . $i . '.png';

    $builder = new Barcode\Builder();
    $builder->setFilename('../img/barcodes/' . $filename);
    $builder->saveImageFile(true);
    $builder->setWidth(1200);
    $builder->setHeight(120);
    $builder->setFontSize(18);
    $builder->output(generarNPE($carnet, $data['ciclo'], $cod_pago));

    // todos los recibos tienen una altura base de X, calcular si la página tiene
    // espacio suficiente para otro recibo dejando margen al fondo, sino, agregar una
    // nueva página.
    $tamano_base = 70;
    $tamano_desgloce = 5;

    if (isset($conf_lista_tipos[$cod_pago]['desgloce']) === true) {
        $desgloce_pago = $conf_lista_tipos[$cod_pago]['desgloce'];
        foreach ($desgloce_pago as $detalle) {
            $tamano_desgloce += 5;
        }
    } else {
        unset($desgloce_pago);
    }

    $pos_y = $pdf->GetY();

    if (($tamano_base + $tamano_desgloce + $pos_y) > ($page_height)) {
        $pdf->AddPage();
    }

    $pdf->Cell(0, 0, '', 1, 1);
    $pdf->Ln(2);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Estudiante', $full_name)), 0, 1);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Carnet', $carnet)), 0, 1);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Carrera', $carrera)), 0, 1);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Ciclo', $data['ciclo'])), 0, 1);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Fecha', date('d/m/Y'))), 0, 1);
    $pdf->Ln(5);
    if (isset($desgloce_pago) === true) {
        foreach ($desgloce_pago as $detalle) {
            $pdf->Cell(83, 5, utf8_decode(mb_sprintf($mascara_printf, $detalle['nombre'], sprintf("$%01.2f", $detalle['precio']))), 0, 1);
        }
    } else {
        $pdf->Cell(83, 5, utf8_decode(mb_sprintf($mascara_printf, $nombre_pago, sprintf("$%01.2f", $precio_pago))), 0, 1);
    }
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Total', sprintf("$%01.2f", $precio_pago))), 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'NPE', generarNPE($carnet, $data['ciclo'], $cod_pago, false))), 0, 1);
    $pdf->Image('../img/barcodes/' . $filename, 41, $pdf->GetY(), 150, 0);
    $pdf->Ln(18);
    $pdf->Cell(0, 0, '', 1, 1);

    unlink('../img/barcodes/' . $filename);
}

$pdf->Output();

// echo generarNPE('gú01133315', '12016', '00');

// */
