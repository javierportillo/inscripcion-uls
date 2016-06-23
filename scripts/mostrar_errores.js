/*jslint browser: true, devel: true */
/*global $, eventos */

/**
 * Muestra errores y mensajes animados al usuario.
 */

var mostrarError = (function () {
    'use strict';

    // Cache the DOM
    var $error_global = $('#site-error');

    var render = function ($selector, texto) {
        $selector.text(texto);
        if ($selector.css('display') !== 'none') {
            $selector.finish();
        }
        $selector.slideDown(500).delay(5000).slideUp(500);
    };

    var global = function (texto) {
        render($error_global, texto);
    };

    var local = function (datos) {
        if (datos.selector.length === 0) {
            // el selector no existe
            return false;
        }
        render(datos.selector, datos.texto);
    };

    eventos.on('errorGlobal', global);
    eventos.on('errorLocal', local);

}());
