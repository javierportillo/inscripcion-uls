/*jslint browser: true, devel: true */
/*global $ */

/**
 * Manejo de eventos (publish subscribe pattern)
 */

var eventos = (function () {
    'use strict';

    var lista_eventos = {};

    var suscribir = function (nombre_evento, callback) {
        lista_eventos[nombre_evento] = lista_eventos[nombre_evento] || [];
        if ($.inArray(callback, lista_eventos[nombre_evento]) === -1) {
            lista_eventos[nombre_evento].push(callback);
        }
    };

    var abandonar = function (nombre_evento, callback) {
        if (lista_eventos[nombre_evento]) {
            lista_eventos[nombre_evento] = lista_eventos[nombre_evento].filter(function (funcion) {
                return funcion !== callback;
            });
        }
    };

    var emitirEvento = function (nombre_evento, datos) {
        if (lista_eventos[nombre_evento]) {
            lista_eventos[nombre_evento].forEach(function (callback) {
                callback(datos);
            });
        }
    };

    return {
        on: suscribir,
        off: abandonar,
        emit: emitirEvento
    };

}());

// Fijar algunos eventos por defecto

$(document).ready(function () {
    'use strict';

    // Intentar cargar la página de matricula al inicio, si el usuario
    // no ha iniciado sesión, el servidor envía el login por defecto.
    eventos.emit('cambiarContenidos', 'matricula.php');

    // Crear evento delegado cuando el formulario login haga submit
    $('#main-content').on('click', '#btn-login-entrar', function () {
        eventos.emit('btnLoginEntrar');
    });

    // Crear evento delegado al click de mostrar actividad de pagos
    $('#main-content').on('click', '#btn-mostrar-actividad', function () {
        eventos.emit('cambiarContenidos', 'actividadpagos.php');
    });

    // Mostrar la hoja de inscripción.
    $('#main-content').on('click', '#btn-hoja-inscripcion', function () {
        eventos.emit('cambiarContenidos', 'mostrar_hoja_inscripcion.php');
    });

    // Regresar a la página principal desde la actividad de pagos
    $('#main-content').on('click', '#btn-regresar-matricula', function () {
        eventos.emit('cambiarContenidos', 'matricula.php');
    });

    // Cambiar de contenidos en la actividad de pagos al seleccionar un ciclo
    $('#main-content').on('change', '#cbo-actividad-ciclo', function () {
        eventos.emit('cambiarContenidosPagos', $('#cbo-actividad-ciclo').val());
    });

    // return false para evitar que la página recargue en submit de un formulario.
    $('#main-content').on('submit', 'form', function () {
        return false;
    });
});
