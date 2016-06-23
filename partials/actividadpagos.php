<?php

require_once '../app/database.php';

$full_name = htmlentities(getFullName($_SESSION['carnet']), ENT_QUOTES);
$output_carnet = htmlentities($_SESSION['carnet'], ENT_QUOTES);
$lista_pagos = obtenerPagos($_SESSION['carnet']);

$lista_ciclos = [];

if ($lista_pagos) {
    $lista_ciclos = array_map(function ($pago) {
        return $pago['ciclo'];
    }, $lista_pagos);
    $lista_ciclos = array_unique($lista_ciclos);
}

$html_ciclos = '';

foreach ($lista_ciclos as $ciclo) {
    $html_ciclos .= "<option value={$ciclo}>{$ciclo}</option>";
}

if (strlen($html_ciclos) > 0) {
    $html_ciclos = '<option value="no_data">Selecciona un ciclo</option>' . $html_ciclos;
} else {
    $html_ciclos = '<option value="no_data">No se encuentran datos</option>';
}

$section_contents = <<<EOD
<section class="section" id="section-actividad-pagos">
    <h2>Actividad de pagos para {$output_carnet}</h2>
    <p>Revisa aquí tus pagos realizados</p>
    <form class="section__container">
        <label for="cbo-actividad-ciclo" class="form__label">Selecciona un ciclo</label>
        <select class="form__input form__input--combo" id="cbo-actividad-ciclo">
        {$html_ciclos}
        </select>
    </form>
    <div id="actividad-pagos-container" class="section__container"></div>
    <button id="btn-regresar-matricula" class="button" type="button">Regresar</button>
</section>
EOD;
