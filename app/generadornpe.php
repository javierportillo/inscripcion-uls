<?php

/**
* Este documento genera codigos npe a partir de la información del alumno, el ciclo y el tipo de pago.
*/

require_once '../vendor/autoload.php';
require_once 'database.php';

use Noodlehaus\Config;

// Convierte caracteres a su representación en números ASCII
function uniord($u) {
    $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
    $k1 = ord(substr($k, 0, 1));
    $k2 = ord(substr($k, 1, 1));
    return $k2 * 256 + $k1;
}

// Convierte números de la codificación ASCII a caracteres
function unichr($intval) {
    return mb_convert_encoding(pack('n', $intval), 'UTF-8', 'UTF-16BE');
}

function generarNPE ($carnet, $ciclo, $tipo_de_pago, $identifiers = true) {

    $conf = new Config('../config/config.json');

    $lista_tipos_de_pago = $conf->get('tipos_de_pago');
    $identificador_empresa = $conf->get('identificador_empresa');

    $carnet = mb_strtoupper($carnet);

    if (
        (isset($carnet) === false || empty($carnet)) ||
        (isset($ciclo) === false || empty($ciclo)) ||
        (isset($tipo_de_pago) === false || empty($tipo_de_pago))
    ) {
        return false;
    }

    $es_carnet_valido = preg_match('/^([A-Z]|Ñ|Á|É|Í|Ó|Ú){2}\d{8}$/i', $carnet);

    if ($es_carnet_valido === false || $es_carnet_valido === 0) {
        return false;
    }

    $es_ciclo_valido = preg_match('/^\d\-?\d{4}$/', $ciclo);

    if ($es_ciclo_valido === false || $es_ciclo_valido === 0) {
        return false;
    }

    if (isset($lista_tipos_de_pago[$tipo_de_pago]) === false) {
        return false;
    }

    $precio = $lista_tipos_de_pago[$tipo_de_pago]['precio'];

    $ciclo = str_replace('-', '', $ciclo);
    $precio = str_replace('.', '', sprintf('%011.2f', $precio));

    $char_apellido_uno = sprintf("%'.03d", uniord(mb_substr($carnet, 0, 1)));
    $char_apellido_dos = sprintf("%'.03d", uniord(mb_substr($carnet, 1, 1)));
    $digitos_carnet = mb_substr($carnet, 2);

    $codigo_npe = '';

    if ($identifiers === true) {
        $codigo_npe .= '(415)';
    }

    $codigo_npe .= '74197000';
    $codigo_npe .= $identificador_empresa;

    if ($identifiers === true) {
        $codigo_npe .= '(3902)';
    }

    $codigo_npe .= $precio;

    if ($identifiers === true) {
        $codigo_npe .= '(8020)';
    }
    
    $codigo_npe .= $char_apellido_uno;
    $codigo_npe .= $char_apellido_dos;
    $codigo_npe .= $digitos_carnet;
    $codigo_npe .= $ciclo;
    $codigo_npe .= $tipo_de_pago;

    return $codigo_npe;
}

// echo generarNPE('gú01133315', '12016', '00');
