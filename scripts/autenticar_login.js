/*jslint browser: true, devel:true */
/*global $, eventos, validar */

var autenticarUsuario = (function () {
    'use strict';

    var $formulario;
    var $carnet;
    var $clave;
    var $login_error;
    var $btn_login;

    var cacheContenidos = function () {
        $formulario = $('#login-form');
        $carnet = $formulario.children('#login-carnet');
        $clave = $formulario.children('#login-clave');
        $login_error = $formulario.children('#login-error');
        $btn_login = $formulario.children('#btn-login-entrar');
    };

    var emitirError = function (texto) {
        eventos.emit('errorLocal', {selector: $login_error, texto: texto});
    };

    var ajaxErrorResponse = function (ignore, status, errorThrown) {
        eventos.emit('errorGlobal', 'Error de conexión con el servidor [' + status + '] ' + errorThrown);
    };

    var ajaxSuccessResponse = function (response) {
        if (response.alreadyloggedin) {
            eventos.emit('errorGlobal', 'Por favor cierra sesión antes de entrar a otra cuenta.');
        } else if (response.carnetfalta) {
            emitirError('Por favor, introduce tu carnet...');
        } else if (response.carnetincorrecto) {
            emitirError('El carnet es inválido...');
        } else if (response.clavefalta) {
            emitirError('Por favor, introduce tu contraseña...');
        } else if (response.noencontrado) {
            emitirError('El usuario ' + $carnet.val() + ' no existe, verifica tus datos.');
        } else if (response.accesodenegado) {
            emitirError('La contraseña es incorrecta, acceso denegado.');
        } else if (response.accesoconcedido) {
            $btn_login.prop('disabled', true);
            eventos.emit('cambiarContenidos', 'matricula.php');
        }
    };

    var enviarDatos = function () {
        $.ajax({
            method: 'POST',
            url: '../app/authenticate_user.php',
            data: {carnet: $carnet.val(), clave: $clave.val()},
            dataType: 'json',
            error: ajaxErrorResponse,
            success: ajaxSuccessResponse
        });
    };

    var validarLoginInputs = function () {

        var validar_carnet = validar($carnet, 10, 10, new RegExp(/^([a-z]|ñ|á|é|í|ó|ú){2}\d{8}$/i));
        var validar_clave = validar($clave, 1, 255);

        if (validar_carnet.valid === false) {
            if (validar_carnet.status === 'bad-selector' || validar_carnet.status === 'not-an-input') {
                emitirError('Error en el campo "carnet", por favor, intentalo más tarde.');
                return false;
            }
            if (validar_carnet.status === 'empty') {
                emitirError('Por favor, introduce tu carnet.');
                return false;
            }
            if (validar_carnet.status === 'out-of-bounds') {
                emitirError('El carnet debe tener 10 caracteres.');
                return false;
            }
            emitirError('El carnet es inválido.');
            return false;
        }

        if (validar_clave.valid === false) {
            if (validar_clave.status === 'bad-selector' || validar_clave.status === 'not-an-input') {
                emitirError('Error en el campo "contraseña", por favor, intentalo más tarde.');
                return false;
            }
            if (validar_clave.status === 'empty') {
                emitirError('Por favor, introduce tu contraseña.');
                return false;
            }
            emitirError('La contraseña es inválida.');
            return false;
        }

        enviarDatos();

    };

    eventos.on('loginSectionLoaded', cacheContenidos);
    eventos.on('btnLoginEntrar', validarLoginInputs);

}());
