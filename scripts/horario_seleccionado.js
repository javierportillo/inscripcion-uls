/*jslint browser: true, devel:true */
/*global $, eventos, validar, window */

$(document).ready(function () {
    'use strict';

    var check_selected = function ($data_cell) {
        var $cell_selected = $('.horario_seleccionado');
        if ($cell_selected.length >= 5 && $data_cell.hasClass('horario_seleccionado') === false) {
            eventos.emit('errorGlobal', 'Solo puedes inscribir hasta 5 materias.');
            return false;
        }
    };

    var check_already_selected = function (cod_materia) {
        var $cell_selected = $('.horario_seleccionado');
        var hay_seleccionados = $cell_selected.filter(function (ignore, element) {
            if ($(element).data('value') === cod_materia) {
                return true;
            }
            return false;
        });

        if (hay_seleccionados.length > 0) {
            return true;
        }
        return false;
    };

    $('#main-content').on('click', '#horario_materias td', function (event) {
        var $data_cell = $(event.currentTarget);

        if ($data_cell.data('value') === undefined || $data_cell.data('grupo') === undefined) {
            return false;
        }

        if (event.target.nodeName === 'SELECT' || $data_cell.children('select').length > 0) {
            return false;
        }

        if (check_selected($data_cell) === false) {
            return false;
        }

        if ($data_cell.data('value') !== undefined) {
            if ($data_cell.hasClass('horario_seleccionado') === true) {
                $data_cell.removeClass('horario_seleccionado');
            } else {
                if (check_already_selected($data_cell.data('value')) === false) {
                    $data_cell.addClass('horario_seleccionado');
                } else {
                    eventos.emit('errorGlobal', 'Ya has seleccionado esa materia.');
                }
            }
        }
    });

    $('#main-content').on('change', '.horario__multiple-sel', function (event) {
        var $data_cell = $(event.currentTarget).parent();

        if ($data_cell.data('value') === undefined || $data_cell.data('grupo') === undefined) {
            return false;
        }

        if (check_selected($data_cell) === false) {
            event.currentTarget.value = 'no-selection';
            return false;
        }

        if (event.currentTarget.value === 'no-selection') {
            $data_cell.removeClass('horario_seleccionado');
        } else {
            if (check_already_selected(event.currentTarget.value) === false) {
                $data_cell.addClass('horario_seleccionado');
                $data_cell.data('value', event.currentTarget.value);
                $data_cell.data('grupo', $(event.currentTarget).children(':selected').data('grupo'));
            } else {
                event.currentTarget.value = 'no-selection';
                $data_cell.removeClass('horario_seleccionado');
                eventos.emit('errorGlobal', 'Ya has seleccionado esa materia.');
            }
        }
    });

    var verificar_materias = function (lista_materias) {
        if (lista_materias.length === 0) {
            eventos.emit('errorGlobal', 'Elige algunas materias para inscribir.');
            return false;
        }
        if (lista_materias.length > 5) {
            eventos.emit('errorGlobal', 'No puedes inscribir más de cinco materias.');
            return false;
        }
        return true;
    };

    var inscribir_error_response = function (ignore, status, errorThrown) {
        eventos.emit('errorGlobal', 'Error de conexión con el servidor [' + status + '] ' + errorThrown);
        $('#btn-inscribir').prop('disabled', false);
    };

    var inscribir_success_response = function (response) {
        console.log(response);
        if (response.success) {
            eventos.emit('errorGlobal', 'Te has inscrito satisfactoriamente!');
            eventos.emit('cambiarContenidos', 'matricula.php');
        }
        if (response.error) {
            eventos.emit('errorGlobal', response.error);
            $('#btn-inscribir').prop('disabled', false);
        }
    };

    var enviar_inscripcion_server = function (lista_materias) {
        $.ajax({
            method: 'POST',
            url: '../app/ajaxinscribiralumno.php',
            data: {materias: JSON.stringify(lista_materias)},
            dataType: 'json',
            error: inscribir_error_response,
            success: inscribir_success_response
        });
    };

    // boton de inscribir materias
    $('#main-content').on('click', '#btn-inscribir', function (event) {
        var materias_seleccionadas = $('.horario_seleccionado').map(function (ignore, materia) {
            return $(materia).data();
        }).get();

        if (verificar_materias(materias_seleccionadas) === false) {
            return false;
        }

        enviar_inscripcion_server(materias_seleccionadas);
        $(event.currentTarget).prop('disabled', true);
    });
});
