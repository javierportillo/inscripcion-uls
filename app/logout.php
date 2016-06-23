<?php

/*
 * Recibe peticiones AJAX para eliminar la sessión de un usuario.
 */

 session_start();
 
// Comprobar si el la petición proviene de AJAX
if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    if (session_destroy()) {
        echo json_encode(['loggedout' => true]);
        return true;
    }
    echo json_encode(['loggedout' => false]);
    return false;
} else {
    // Si la petición no proviene de AJAX, redireccionar al index.
    header('Location: ../public_html/');
}
