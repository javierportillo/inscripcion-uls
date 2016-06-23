<?php

/**
 * Recibe las materias seleccionadas por el alumno en la sección de inscripción
 * e inserta los datos en la BD.
 */

session_start();

// el HTTP request debe venir de ajax (jquery)
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    header('Location: ../public_html/');
    return false;
}

// Los HTTP Request de Ajax se envían utilizando el metodo post, conteniendo el ciclo solicitado.
if (isset($_POST['materias']) === false || empty($_POST['materias']) === true) {
    header('Location: ../public_html/');
    return false;
}

// El alumno debe haber iniciado sessión.
if (isset($_SESSION['loggedin']) === false || $_SESSION['loggedin'] !== true) {
    header('Location: ../public_html/');
    return false;
}

require_once 'database.php';
require_once 'horario_inscripcion.php';

// Obtener los grupos a los cuales el alumno se puede inscribir.
$materias_llevables = materiasLlevables($_SESSION['carnet']);
$materias_seleccionadas = json_decode($_POST['materias'], true);

// Comprueba que los datos sean válidos.
// Que las materias que el usuario eligió las pueda llevar realmente.
$son_datos_buenos = array_reduce($materias_seleccionadas, function ($carry, $materia) use ($materias_llevables) {
    if (isset($materia['value']) !== true || isset($materia['grupo']) !== true) {
        return false;
    }

    $es_materia_llevable = array_filter($materias_llevables, function ($materia_llevable) use ($materia) {
        return $materia_llevable['id_grupo'] === (string) $materia['grupo'] && $materia_llevable['codigo_materia'] === $materia['value'];
    });

    if (count($es_materia_llevable) === 0) {
        return false;
    }

    return $carry;
}, true);

if ($son_datos_buenos === false) {
    echo json_encode([
        'error' => 'ERROR: Se han recibido datos corruptos.'
    ]);
    return false;
}

// Obtener solo los codigos de las materias que el alumno ha seleccionado.
$codigos_materias_sel = array_map(function ($materia) {
    return $materia['value'];
}, $materias_seleccionadas);

$lista_id_grupos = array_map(function ($materia) {
    return $materia['grupo'];
}, $materias_seleccionadas);

// Se eliminan los duplicados. Si existen duplicados y se eliminan
// este array se hace mas pequeño que el original.
$codigos_materias_sel = array_unique($codigos_materias_sel);
$lista_id_grupos = array_unique($lista_id_grupos);

// Si la cantidad de codigos es menor a las materias seleccionadas es que
// se han recibido materias duplicadas. (los alumnos no pueden inscribir en
// dos grupos distintos de la misma materia al mismo tiempo).
if (count($codigos_materias_sel) !== count($materias_seleccionadas) || count($lista_id_grupos) !== count($materias_seleccionadas)) {
    echo json_encode([
        'error' => 'ERROR: Se han recibido materias duplicadas.'
    ]);
    return false;
}

if (insertarInscripcion($_SESSION['carnet'], $lista_id_grupos) === false) {
    echo json_encode([
        'error' => 'ERROR: Hubo un error al ingresar la matrícula en la base de datos.'
    ]);
    return false;
}

echo json_encode([
    'success' => true
]);
