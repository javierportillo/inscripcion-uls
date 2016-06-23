<?php

/**
 * Envía los tipos de pago realizados por el alumno.
 */

// Si el HTTP request no proviene de AJAX, redireccionar al index.
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    header('Location: ../public_html/');
    return false;
}

// Los HTTP Request de Ajax se envían utilizando el metodo post, conteniendo el ciclo solicitado.
if (isset($_POST['ciclo']) === false || empty($_POST['ciclo']) === true) {
    header('Location: ../public_html/');
    return false;
}

require_once 'analizarpagos.php';

session_start();

echo json_encode(obtenerPagosSoloTipos($_SESSION['carnet'], $_POST['ciclo']));
