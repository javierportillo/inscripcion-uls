<?php
/**
 * Calcula el ciclo actual a partir de la fecha del servidor
 */

function cicloActual () {

    $anio =  date('Y');
    $mes = (int) date('n');

    $ciclo = 1;

    if ($mes > 6) {
        $ciclo = 2;
    }

    return $ciclo. '-' .$anio; // Ej: 1-2016

}
