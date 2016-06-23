<?php

require_once '../app/cicloactual.php';
$cicloactual = cicloActual();

$section_contents = <<<EOD
<section class="section">
    <button id="btn-regresar-matricula" class="button" type="button">Regresar</button>
</section>
<section class="section">
    <h2>Hoja de Inscripción Ciclo {$cicloactual}</h2>
    <p>Revisa tu hoja de iscripción para este ciclo aquí.</p>
    <embed src="../app/hojainscripcion.php" width="100%" height="500" type="application/pdf">
</section>
EOD;
