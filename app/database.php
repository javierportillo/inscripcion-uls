<?php
/*
 * Conexión a la base de datos MySQL utlizando PDO
 * contiene funciones que interactuan con la BD.
 */

require_once '../vendor/autoload.php';
require_once 'cicloactual.php';

use Noodlehaus\Config;

// Crea una conexión con la base de datos.
function dbConnect () {

    $conf = new Config('../config/config.json');

    $driver = $conf->get('database.driver');
    $host = $conf->get('database.host');
    $schema = $conf->get('database.schema');
    $username = $conf->get('database.username');
    $password = $conf->get('database.password');
    $charset = $conf->get('database.charset');

    // Data Source Name
    $dsn = $driver . ':dbname=' . $schema . ';host=' . $host . ';charset=' . $charset;

    try {
        $dbConnection = new PDO($dsn, $username, $password);
        // Establece el modo de error para capturar exepciones PDOException
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
    } catch (PDOException $e) {
        die('ERROR conexión BD: ' . $e->getMessage());
    }

}

// Obtener datos individuales desde la BD
function getDBData ($prepared_statement, $input_parameters, $fetch_all_data = false) {
    $conn = dbConnect();
    $query = $conn->prepare($prepared_statement);
    $query->execute($input_parameters);
    // PDO::FETCH_ASSOC regresa las filas de resultado en forma de arrays asociativas.
    if ($fetch_all_data == true) {
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $result = $query->fetch(PDO::FETCH_ASSOC);
    }

    return $result;
}

// Insertar datos en la DB
function setDBData ($prepared_statement, $input_parameters) {
    $conn = dbConnect();
    $query = $conn->prepare($prepared_statement);
    return $query->execute($input_parameters);
}

// Obtiene la contraseña del alumno desde la BD
function getPassword ($carnet) {

    if (isset($carnet) === false || empty($carnet)) {
        return false;
    }

    $carnet = strtoupper($carnet);
    $prepared_statement = 'SELECT password FROM matricula WHERE carnet=?';
    $input_parameters = [$carnet];

    $result = getDBData($prepared_statement, $input_parameters);

    if ($result) {
        return $result['password'];
    }

    return false;
}

// Obtiene el nombre completo del alumno desde la BD
function getFullName ($carnet) {

    if (isset($carnet) === false || empty($carnet)) {
        return false;
    }

    $carnet = strtoupper($carnet);
    $prepared_statement = 'SELECT nombre FROM matricula WHERE carnet=?';
    $input_parameters = [$carnet];

    $result = getDBData($prepared_statement, $input_parameters);

    if ($result) {
        return $result['nombre'];
    }

    return false;
}

// Obtiene la carrera del estudiante
function obtenerCarreraAlumno ($carnet) {
    if (isset($carnet) === false || empty($carnet)) {
        return false;
    }

    $carnet = strtoupper($carnet);
    $prepared_statement = 'SELECT carrera.nombre FROM carrera INNER JOIN carrera_alumno USING (codigo_carrera) WHERE carnet=?';
    $input_parameters = [$carnet];

    $result = getDBData($prepared_statement, $input_parameters);

    if ($result) {
        return $result['nombre'];
    }

    return false;
}

// Obtenga el estado del alumno
function obtenerEstadoAlumno ($carnet) {
    if (isset($carnet) === false || empty($carnet)) {
        return false;
    }

    $carnet = strtoupper($carnet);
    $prepared_statement = 'SELECT estado FROM matricula WHERE carnet = ?';
    $input_parameters = [$carnet];

    $result = getDBData($prepared_statement, $input_parameters);

    if ($result) {
        return $result;
    }

    return false;
}

// Obtiene todos los pagos del alumno.
function obtenerPagos($carnet) {

    if (isset($carnet) === false || empty($carnet)) {
        return false;
    }

    $carnet = strtoupper($carnet);
    $prepared_statement = 'SELECT * FROM pagos WHERE carnet = ?';
    $input_parameters = [$carnet];

    $result = getDBData($prepared_statement, $input_parameters, true);

    if ($result) {
        return $result;
    }

    return false;

}

// Obtiene una lista con todos los ciclos que encuentre en los grupos y los agrupa.
function obtenerCiclos () {
    $prepared_statement = 'SELECT ciclo FROM grupo GROUP BY ciclo ORDER BY CONCAT(SUBSTRING(ciclo FROM 2), SUBSTRING(ciclo, -6, 1))';
    $result = getDBData($prepared_statement, null, true);

    if ($result) {
        return $result;
    }

    return false;
}

function obtenerHorarios ($ciclo = '') {
    if (isset($ciclo) !== true || empty($ciclo) === true) {
        $ciclo = cicloactual();
    }
    $prepared_statement = 'SELECT id_horario, hora_inicio, hora_fin FROM grupo INNER JOIN horarios USING (id_horario) WHERE ciclo=? GROUP BY id_horario';
    $input_parameters = [$ciclo];

    $result = getDBData($prepared_statement, $input_parameters, true);

    if ($result) {
        return $result;
    }

    return false;
}

// Obtiene los grupos que el alumno pueda llevar en este ciclo.
// Según la carrera y el ciclo seleccionado.
function obtenerCargaAcademica ($carnet, $ciclo='') {
    if (isset($carnet) !== true || empty($carnet) === true) {
        return false;
    }
    if (isset($ciclo) !== true || empty($ciclo) === true) {
        $ciclo = cicloactual();
    }

    $carnet = strtoupper($carnet);
    $prepared_statement = 'SELECT id_grupo, ciclo, dia, id_horario, grupo.codigo_materia, pensum.nombre, codigo_referencia, codigo_prerrequisito FROM grupo INNER JOIN pensum USING (codigo_materia) INNER JOIN carrera_alumno USING (codigo_carrera) INNER JOIN matricula USING (carnet) WHERE carrera_alumno.carnet=? and ciclo=? and pensum.anio_pensum=matricula.anio_pensum';
    $input_parameters = [$carnet, $ciclo];

    $result = getDBData($prepared_statement, $input_parameters, true);
    if ($result) {
        return $result;
    }

    return false;
}

// print_r(obtenerCargaAcademica('MP01133315'));

// Busca entre las inscripciones y selecciona las materias que tengan nota mayor o igual a 7.
function obtenerMateriasAprobadas ($carnet) {
    if (isset($carnet) !== true || empty($carnet) === true) {
        return false;
    }
    $carnet = strtoupper($carnet);
    $prepared_statement = 'SELECT codigo_materia, nombre, codigo_referencia FROM inscripcion INNER JOIN notas USING(id_notas) INNER JOIN grupo USING(id_grupo) INNER JOIN pensum USING(codigo_materia) WHERE nota_final >= 7 AND carnet = ?';
    $input_parameters = [$carnet];

    $result = getDBData($prepared_statement, $input_parameters, true);
    if ($result) {
        return $result;
    }
    return false;
}
// print_r(obtenerMateriasAprobadas('MP01133315'));

function obtenerPensum ($carnet) {
    if (isset($carnet) !== true || empty($carnet) === true) {
        return false;
    }
    $carnet = strtoupper($carnet);
    $prepared_statement = 'SELECT codigo_materia FROM pensum INNER JOIN carrera_alumno USING(codigo_carrera) WHERE carnet = ?';
    $input_parameters = [$carnet];

    $result = getDBData($prepared_statement, $input_parameters, true);
    if ($result) {
        return $result;
    }
    return false;
}
// print_r(obtenerPensum('MP01133315'));

function insertarInscripcion ($carnet, $lista_id_grupos) {
    if (isset($carnet) !== true || empty($carnet) === true) {
        return false;
    }
    if (isset($lista_id_grupos) !== true || empty($lista_id_grupos) === true) {
        return false;
    }

    $carnet = strtoupper($carnet);

    $input_parameters = [
        ':carnet' => $carnet
    ];

    $prepared_statement = 'START TRANSACTION; ';

    foreach ($lista_id_grupos as $key => $id_grupo) {
        $prepared_statement .= 'INSERT INTO notas VALUES(NULL, 0); ';
        $prepared_statement .= 'INSERT INTO inscripcion VALUES(NULL, \'\', :carnet, :idgrupo'. $key .', LAST_INSERT_ID()); ';

        $input_parameters[':idgrupo' . $key] = $id_grupo;
    }

    $prepared_statement .= 'COMMIT; ';

    return setDBData($prepared_statement, $input_parameters);
}
// print_r(insertarInscripcion('adfa', [3, 17, 23, 21, 36]));

function obtenerInscripcion ($carnet, $ciclo='') {
    if (isset($carnet) !== true || empty($carnet) === true) {
        return false;
    }
    if (isset($ciclo) !== true || empty($ciclo) === true) {
        $ciclo = cicloactual();
    }

    $carnet = strtoupper($carnet);

    $prepared_statement = 'SELECT id_grupo, dia, pensum.nombre, hora_inicio, hora_fin FROM inscripcion INNER JOIN grupo USING (id_grupo) INNER JOIN pensum USING (codigo_materia) INNER JOIN horarios USING (id_horario) WHERE carnet=? AND ciclo=?';
    $input_parameters = [$carnet, $ciclo];

    $result = getDBData($prepared_statement, $input_parameters, true);
    if ($result) {
        return $result;
    }
    return false;
}

// var_dump(obtenerInscripcion('MP01133315'));
