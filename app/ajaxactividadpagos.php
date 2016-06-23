<?php

/**
 * Obtiene los pagos del alumno y los envía al cliente en formato JSON
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

require_once 'database.php';

session_start();

$pagos = obtenerPagos($_SESSION['carnet']);

if ($pagos === false) {
    echo json_encode([
        'status' => 'error',
        'contents' => 'No se encontraron datos de tus pagos'
    ]);
    return false;
}

// Filtrar todos los pagos; mostrar solo los del ciclo seleccionado.
$pagos = array_filter($pagos, function ($pago) {
    return $pago['ciclo'] === $_POST['ciclo'];
});

function htmlFormatearPagos ($lista_pagos) {

    // La información de cada pago se convierte en una tabla con esos datos.
    $html_pagos = array_map(function ($pago) {

        $html_output = '<table class="section table">';

        // Los datos de cada pago se convierten en filas de la tabla.
        $html_output .= implode("\n", array_map(function ($key, $val) {

            // Las llaves vienen de la base de datos, reemplazar los guines bajos con " de ".
            // Ej: id_pago se convierte en Id de pago.
            $key = str_replace('_', ' de ', $key);

            return
                "<tr>".
                "<td class=\"align-right capitalize paddign-sm bold\">{$key}:</td>".
                "<td class=\"txtalign-botton paddign-sm\">{$val}</tdv>".
                "</tr>";

        }, array_keys($pago), $pago));

        $html_output .= '</table>';

        return $html_output;

    }, $lista_pagos);

    return implode("\n", $html_pagos);

}

// Se envía la respuesta en formato JSON.
echo json_encode([
    'status' => 'success',
    'contents' => htmlFormatearPagos($pagos)
]);
