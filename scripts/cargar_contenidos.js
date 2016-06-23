/*jslint browser: true, devel: true */
/*global $, eventos */

/**
 * Cambia los contenidos sin recargar la página.
 */

var cambiarContenidos = (function () {
    'use strict';

    var $contenedor = $('#main-content');

    var ajaxErrorResponse = function (ignore, status, errorThrown) {
        eventos.emit('errorGlobal', 'Error de conexión con el servidor [' + status + '] ' + errorThrown);
    };

    var parcialSuccessResponse = function (response) {
        // console.log(response);
        // return false;
        if (response.pagina === 'no-page') {
            eventos.emit('errorGlobal', response.contents);
            return false;
        }

        $contenedor.fadeOut(250, function () {
            $contenedor.html(response.contents);
            if (response.pagina === 'login.php') {
                eventos.emit('loginSectionLoaded');
            }
            eventos.emit('loaded_content', response.pagina);
        }).fadeIn(250);

    };

    var cargarParcial = function (paginas) {
        $.ajax({
            method: 'POST',
            url: '../partials/load.php',
            data: {partial: paginas},
            dataType: 'json',
            error: ajaxErrorResponse,
            success: parcialSuccessResponse
        });
    };

    var pagosSuccessResponse = function (response) {
        // console.log(response);
        // return false;

        if (response.status === 'error') {
            eventos.emit('errorGlobal', response.contents);
            return false;
        }

        var $contenedorActPagos = $('#actividad-pagos-container');

        $contenedorActPagos.fadeOut(250, function () {
            $contenedorActPagos.html(response.contents);
        }).fadeIn(250);

    };

    var cargarActividadPagos = function (ciclo_seleccionado) {
        if (ciclo_seleccionado === 'no_data') {
            var $contenedorActPagos = $('#actividad-pagos-container');
            $contenedorActPagos.fadeOut(250, function () {
                $contenedorActPagos.html('');
            }).fadeIn(250);
            return false;
        }
        $.ajax({
            method: 'POST',
            url: '../app/ajaxactividadpagos.php',
            data: {ciclo: ciclo_seleccionado},
            dataType: 'json',
            error: ajaxErrorResponse,
            success: pagosSuccessResponse
        });
    };

    eventos.on('cambiarContenidos', cargarParcial);
    eventos.on('cambiarContenidosPagos', cargarActividadPagos);

}());
