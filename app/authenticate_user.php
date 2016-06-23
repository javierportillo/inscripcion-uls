<?php

/*
 * Este script recibe peticiones de ajax conteniendo datos del carnet y contraseña
 * de un alumno y los verifica contra la base de datos para autenticar a los usuarios.
 */

// Comprobar si el HTTP request proviene de ajax, sino, redirecciona
// hacia el index.
if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    session_start();

    // Si ya ha iniciado sessión, se detiene el script.
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        echo json_encode(['alreadyloggedin' => true]);
        return false;
    }

    // Se verifican los datos y los errores encontrados se almacenan aquí.
    $response_errors = [];

    if (isset($_POST['carnet']) === false || empty($_POST['carnet'])) {
        $response_errors['carnetfalta'] = true;
    }

    $carnet = $_POST['carnet'];
    $carnet = mb_strtoupper($carnet);

    if (preg_match('/^([A-Z]|Ñ|Á|É|Í|Ó|Ú){2}\d{8}$/i', $carnet) == false ) {
        $response_errors['carnetincorrecto'] = true;
    }

    if (isset($_POST['clave']) === false || empty($_POST['clave']) || strlen($_POST['clave']) > 255) {
        $response_errors['clavefalta'] = true;
    }

    // Si se encuentran errores, se envían al cliente usando JSON y se detiene el script.
    if (count($response_errors) > 0) {
        echo json_encode($response_errors);
        return false;
    }

    // Si en este punto no se ha detenido el script, se procede
    // suponiendo que los datos son correctos.
    require_once 'database.php';

    // obtiene la contraseña encriptada de la base de datos y se verifica.
    $pass_hash = getPassword($_POST['carnet']);

    if ($pass_hash === false) {
        echo json_encode(['noencontrado' => true]);
        return false;
    }

    if (password_verify($_POST['clave'], $pass_hash) === false) {
        echo json_encode(['accesodenegado' => true]);
        return false;
    }

    // Al verificar los datos se le concede al cliente una sesión y se envía la confirmación.
    $_SESSION['loggedin'] = true;
    $_SESSION['carnet'] = $_POST['carnet'];
    echo json_encode(['accesoconcedido' => true]);

} else {
    // Si el HTTP request no proviene de ajax, se redirecciona hacia el index.
    header('Location: ../public_html/');
}
