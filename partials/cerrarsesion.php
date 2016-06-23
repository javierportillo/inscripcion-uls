<?php

require_once '../app/database.php';

$full_name = htmlentities(getFullName($_SESSION['carnet']), ENT_QUOTES);
$output_carnet = htmlentities($_SESSION['carnet'], ENT_QUOTES);

$section_contents = <<<EOD
<section class="section" id="section-logout">
    <h2>Bienvenido {$full_name}</h2>
    <p>Has iniciado sesión como {$output_carnet}</p>
    <button type="button" class="button" id="btn-login-cerrar-session">Cerrar Sesión</button>
</section>
EOD;
