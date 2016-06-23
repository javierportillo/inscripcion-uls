/*jslint browser: true, devel: true */
/*global $, eventos */

/**
 * Carga los tipos de pago cuando el usuario selecciona un ciclo.
 */

var tipos_de_pago = (function () {
    'use strict';

    var tipos_error_response = function (ignore, status, errorThrown) {
        eventos.emit('errorGlobal', 'Error de conexión con el servidor [' + status + '] ' + errorThrown);
    };

    var tipos_success_response = function (response) {
        if (response === false) {
            return false;
        }
        response.forEach(function (tipo_pago) {
            var $label = $('label[for="' + tipo_pago + '"]');
            var $check = $('#' + tipo_pago);
            if ($check.prop('disabled') === false) {
                $check.prop('disabled', true);
                $label.html($label.text() + '<span> [CANCELADO]</span>');
            }
        });
    };

    var tipos_peticion_server = function (ciclo) {
        if (ciclo === 'no-selection') {
            return false;
        }
        $.ajax({
            method: 'POST',
            url: '../app/ajaxtipospagos.php',
            data: {ciclo: ciclo},
            dataType: 'json',
            error: tipos_error_response,
            success: tipos_success_response
        });
    };

    var tipos_reset = function () {
        var $label = $('.label_tipos_pagos');
        var $check = $('.chk_tipo_pago');
        $label.children('span').remove();
        $check.prop('disabled', false);
        $check.prop('checked', false);
    };

    var mostrar_segun_ciclo = function (ciclo) {
        $('#tipos_de_pago_container').children().fadeOut(0);


        if (ciclo === 'no-selection') {
            return false;
        }

        tipos_reset();

        tipos_peticion_server(ciclo);

        if (ciclo.substring(0, 1) === '1') {
            $('#tipos_pagos_ciclo_uno').fadeIn(250);
        } else {
            $('#tipos_pagos_ciclo_dos').fadeIn(250);
        }
    };

    var obtener_datos_pagos = function () {
        var cbo_ciclo_val = $('#cbo-pagos-ciclo').val();

        if (cbo_ciclo_val === 'no-selection') {
            return false;
        }

        var valores_pagos_seleccionados = [];

        $('.chk_tipo_pago:checked').each(function (ignore, input) {
            valores_pagos_seleccionados.push(input.value);
        });

        if (valores_pagos_seleccionados.length === 0) {
            return false;
        }

        return {
            ciclo: cbo_ciclo_val,
            pagos_seleccionados: valores_pagos_seleccionados
        };
    };

    var cache_datos_a_enviar = null;

    var cargar_seccion_recibo = function () {
        cache_datos_a_enviar = obtener_datos_pagos();
        if (cache_datos_a_enviar === false || cache_datos_a_enviar === null) {
            return false;
        }

        eventos.emit('cambiarContenidos', 'mostrar_recibo.php');
    };

    var cargar_pdf_recibo = function (pagina_cargada) {
        if (cache_datos_a_enviar === false || cache_datos_a_enviar === null) {
            return false;
        }
        if (pagina_cargada !== 'mostrar_recibo.php') {
            return false;
        }

        $('#pdf_container').append('<embed src="../app/recibo.php?data=' + encodeURIComponent(JSON.stringify(cache_datos_a_enviar)) + '" width="100%" height="500" type="application/pdf">');
    };

    eventos.on('tipos_pago_mostrar', mostrar_segun_ciclo);
    eventos.on('loaded_content', cargar_pdf_recibo);
    eventos.on('on_generar_recibo', cargar_seccion_recibo);

}());

//  onclick="window.open('../app/recibo.php', '_blank');

$(document).ready(function () {
    'use strict';
    $('#main-content').on('change', '#cbo-pagos-ciclo', function (event) {
        eventos.emit('tipos_pago_mostrar', event.target.value);
    });

    $('#main-content').on('click', '#btn-generar-recibo', function () {
        eventos.emit('on_generar_recibo');
    });
});
