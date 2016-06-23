<?php

require_once '../app/database.php';
require_once '../app/analizarpagos.php';
require_once '../app/cicloactual.php';
require_once '../app/horario_inscripcion.php';

$estado_alumno = obtenerEstadoAlumno($_SESSION['carnet'])['estado'];

if ($estado_alumno === 'activo') {

    $lista_ciclos = obtenerCiclos();
    $html_ciclos = '';

    foreach ($lista_ciclos as $ciclo) {
        $html_ciclos .= "<option value={$ciclo['ciclo']}>{$ciclo['ciclo']}</option>";
    }

$section_contents = <<<EOD
<section class="section">
    <h2>Realizar un Pago</h2>
    <p>Elige los tipos de pago que deseas realizar </p>
    <form id="form_tipos_pagos" class="section__container" target="../app/test.php" method="post">
        <label for="cbo-pagos-ciclo" class="form__label">Selecciona un ciclo</label>
        <select id="cbo-pagos-ciclo" class="form__input form__input--combo" name="ciclo">
            <option value="no-selection">Selecciona Un Ciclo</option>
            {$html_ciclos}
        </select>
        <label for="" class="form__label">Selecciona el tipo de pago</label>
        <div id="tipos_de_pago_container" class="section">
            <div id="tipos_pagos_ciclo_uno" class="hidden">
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="00" id="matricula_ciclo1">
                <label for="matricula_ciclo1" class="label_tipos_pagos form__label form__label--inline">Matrícula</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="01" id="enero">
                <label for="enero" class="label_tipos_pagos form__label form__label--inline">Enero</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="02" id="febrero">
                <label for="febrero" class="label_tipos_pagos form__label form__label--inline">Febrero</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="03" id="marzo">
                <label for="marzo" class="label_tipos_pagos form__label form__label--inline">Marzo</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="04" id="abril">
                <label for="abril" class="label_tipos_pagos form__label form__label--inline">Abril</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="05" id="mayo">
                <label for="mayo" class="label_tipos_pagos form__label form__label--inline">Mayo</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="06" id="junio">
                <label for="junio" class="label_tipos_pagos form__label form__label--inline">Junio</label><br />
            </div>
            <div id="tipos_pagos_ciclo_dos" class="hidden">
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="13" id="matricula_ciclo2">
                <label for="matricula_ciclo2" class="label_tipos_pagos form__label form__label--inline">Matrícula</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="07" id="julio">
                <label for="julio" class="label_tipos_pagos form__label form__label--inline">Julio</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="08" id="agosto">
                <label for="agosto" class="label_tipos_pagos form__label form__label--inline">Agosto</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="09" id="septiembre">
                <label for="septiembre" class="label_tipos_pagos form__label form__label--inline">Septiembre</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="10" id="octubre">
                <label for="octubre" class="label_tipos_pagos form__label form__label--inline">Octubre</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="11" id="noviembre">
                <label for="noviembre" class="label_tipos_pagos form__label form__label--inline">Noviembre</label><br />
                <input class="chk_tipo_pago" type="checkbox" name="tipo_pago" value="12" id="diciembre">
                <label for="diciembre" class="label_tipos_pagos form__label form__label--inline">Diciembre</label><br />
            </div>
        </div>
        <button id="btn-generar-recibo" class="button">Generar Recibo</button>
        <button id="btn-mostrar-actividad" class="button" type"button">Mostrar Pagos Realizados</button>
EOD;

if (obtenerInscripcion($_SESSION['carnet']) !== false) {
$section_contents .= <<<EOD
    <button id="btn-hoja-inscripcion" class="button">Hoja de Inscripción</button>
EOD;
}

$section_contents .= <<<EOD
    </form>
</section>
EOD;

if (haPagadoMatricula($_SESSION['carnet']) === true) {
$cicloactual = cicloactual();

    if (obtenerInscripcion($_SESSION['carnet']) === false) {
        $html_horario = htmlHorario($_SESSION['carnet']);

$section_contents .= <<<EOD
<section id="section_inscribir_materias" class="section">
    <h2>Inscribe Ciclo {$cicloactual}</h2>
    <p>Selecciona tus materias a inscribir.</p>
    <p>Si varias materias coinciden el mismo día y hora, selecciona una de ellas.</p>
    <div class="table-container">
        <table id="horario_materias" class="responsive-table">
            <caption>Elige Las Materias A Inscribir</caption>
            {$html_horario}
        </table>
    </div>
    <p>**Por favor, revisa bien tu selección de materias antes de realizar la inscripción.**</p>
    <button class="button" id="btn-inscribir">Inscribir</button>
</section>
EOD;


    }
}

return true;

}

if ($estado_alumno === 'inactivo') {

$section_contents = <<<EOD
<section class="section">
    <h2>Tu estado es inactivo</h2>
    <p>No puedes realizar una inscripción mientras te encuentres inactivo.</p>
    <p>Por favor, acercate a la administración academica para cambiar tu estado.</p>
</section>
EOD;

return true;

}

if ($estado_alumno === 'pendiente') {

$section_contents = <<<EOD
<section class="section">
    <h2>Tu estado está pendiente.</h2>
    <p>No puedes realizar una inscripción mientras te encuentres con tramites pendientes.</p>
    <p>Por favor, acercate a la administración academica para resolver tu problema.</p>
</section>
EOD;

return true;

}

// Si el estado del alumno no es ninguno de los anteriores
$section_contents = <<<EOD
<section class="section">
    <h2>Hubo un problema al verificar tu estado.</h2>
    <p>Por favor, contacta con la unidad de informática para resolver tu probema.</p>
</section>
EOD;
