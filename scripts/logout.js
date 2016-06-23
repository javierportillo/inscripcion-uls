/*jslint browser: true, devel: true */
/*global $, eventos */

/**
 * Mandar un AJAX request para eliminar la sesión del usuario.
 */

$(document).ready(function () {
    'use strict';

    var ajaxErrorResponse = function (ignore, status, errorThrown) {
        eventos.emit('errorGlobal', 'Error de conexión con el servidor [' + status + '] ' + errorThrown);
    };

    var ajaxSuccessResponse = function (response) {
        if (response.loggedout) {
            eventos.emit('cambiarContenidos', 'login.php');
        } else {
            eventos.emit('errorGlobal', 'Error al cerrar sesión, por favor intentalo mas tarde o cierra el navegador.');
        }
    };

    $('#main-content').on('click', '#btn-login-cerrar-session', function () {
        $('#btn-login-cerrar-session').prop('disabled', true);
        $.ajax({
            method: 'POST',
            url: '../app/logout.php',
            dataType: 'json',
            error: ajaxErrorResponse,
            success: ajaxSuccessResponse
        });
    });
});
