<?php

session_start();

// Si el usiario no ha iniciado sesión, redirigir al index.
if (isset($_SESSION['loggedin']) !== true ||
    isset($_SESSION['carnet']) !== true ||
    $_SESSION['loggedin'] !== true
) {
    header('Location: ../public_html/');
    return false;
}

require_once '../vendor/autoload.php';
require_once 'database.php';
require_once 'cicloactual.php';

$dias = [
    '1' => 'Lunes',
    '2' => 'Martes',
    '3' => 'Miércoles',
    '4' => 'Jueves',
    '5' => 'Viernes',
    '6' => 'Sábado',
    '7' => 'Domingo'
];

$full_name = getFullName($_SESSION['carnet']);
$carnet = $_SESSION['carnet'];
$carrera = obtenerCarreraAlumno($carnet);
$ciclo = cicloActual();
$lista_inscripcion = obtenerInscripcion($carnet);
$mascara_printf = '%10s: %s';

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
$pdf->Cell(116, 9, utf8_decode('Hoja de Inscripción'), 0, 0, 'R');
$pdf->Ln(14);
$pdf->SetFont('Courier', '', 12);
// $pdf->Cell(0, 0, '', 1, 1);
$pdf->Ln(4);
$pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Estudiante', $full_name)), 0, 1);
$pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Carnet', $carnet)), 0, 1);
$pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Carrera', $carrera)), 0, 1);
$pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Ciclo', $ciclo)), 0, 1);
$pdf->Ln(4);
// $pdf->Cell(0, 0, '', 1, 1);
$pdf->Ln(4);
$pdf->SetFont('Courier', 'B', 12);
$pdf->Cell(83, 5, utf8_decode('Asignaturas:'), 0, 1);
$pdf->SetFont('Courier', '', 12);
$pdf->Ln(4);
foreach ($lista_inscripcion as $materia) {
    $pdf->Cell(0, 0, '', 1, 1);
    $pdf->Ln(4);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Asignatura', $materia['nombre'])), 0, 1);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Grupo', $materia['id_grupo'])), 0, 1);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Dia', $dias[$materia['dia']])), 0, 1);
    $pdf->Cell(83, 5, utf8_decode(sprintf($mascara_printf, 'Hora', $materia['hora_inicio'] . ' - ' . $materia['hora_fin'])), 0, 1);
    $pdf->Ln(4);
}
$pdf->Cell(0, 0, '', 1, 1);
$pdf->Output();
