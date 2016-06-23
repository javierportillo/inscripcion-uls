<?php
/**
 * Este archivo contiene funciones que verifican distintos tipos de pago
 * a partir de la información guadada en la BD.
 */

require_once 'database.php';
require_once 'cicloactual.php';
require_once '../vendor/autoload.php';

use Noodlehaus\Config;

// Obtiene los pagos que sean solo del cilo seleccionado.
// Por defecto es el ciclo actual.
function obtenerPagosFiltroCiclo ($carnet, $ciclo = '') {
    if (isset($carnet) !== true || empty($carnet) === true) {
        return false;
    }
    if (isset($ciclo) !== true || empty($ciclo) === true) {
        $ciclo = cicloactual();
    }

    $lista_pagos = obtenerPagos($carnet)?: [];

    $lista_pagos = array_filter($lista_pagos, function ($pago) use ($ciclo) {
        return $pago['ciclo'] === $ciclo;
    });

    return array_values($lista_pagos);
}

// Obtiene una lista formateada con solo los tipos de pago del
// ciclo seleccionado para ser facilmente consumida en javascript.
function obtenerPagosSoloTipos ($carnet, $ciclo = '') {
    $lista_pagos = obtenerPagosFiltroCiclo($carnet, $ciclo);

    if (!$lista_pagos) {
        return false;
    }

    $conf = new Config('../config/config.json');
    $lista_tipos_pagos = $conf->get('tipos_de_pago');

    $lista_pagos = array_map(function ($pago) {
        return substr($pago['referencia_pago'], -2);
    }, $lista_pagos);

    $lista_tipos_pagos = array_map(function ($tipo_pago) {
        return $tipo_pago['tipo'];
    }, $lista_tipos_pagos);

    $lista_pagos_tipos = array_map(function ($pago) use ($lista_tipos_pagos) {
        return $lista_tipos_pagos[$pago];
    }, $lista_pagos);

    return $lista_pagos_tipos;
}

// Obtiene todos los pagos de matricula que ha realizado el alumno desde siempre.
function obtenerPagosMatricula ($carnet) {
    if (isset($carnet) === false || empty($carnet)) {
        return false;
    }

    $lista_pagos = obtenerPagos($carnet);
    if ($lista_pagos === false) {
        return false;
    }

    if (count($lista_pagos) === 0) {
        return false;
    }

    $lista_pagos_matricula = array_filter($lista_pagos, function ($pago) {
        $tipo_pago = substr($pago['referencia_pago'], -2);
        return $tipo_pago === '00' || $tipo_pago === '13';
    });

    return array_values($lista_pagos_matricula);
}

// Obtener datos de pago de matricula.
// Regresa true si la fecha actual está entre el periodo de inscripción y
// hay un pago del alumno hecho entre el periodo de inscripcion (enero y julio)

function haPagadoMatricula ($carnet, $ciclo = '') {
    if (isset($carnet) === false || empty($carnet) === true) {
        return false;
    }
    if (empty($ciclo) === true) {
        $ciclo = $ciclo = cicloactual();
    }

    $lista_pagos = obtenerPagosMatricula($carnet);
    if ($lista_pagos === false) {
        return false;
    }

    foreach ($lista_pagos as $pago) {
        if ($pago['ciclo'] === $ciclo) {
            return true;
        }
    }

    return false;
}
