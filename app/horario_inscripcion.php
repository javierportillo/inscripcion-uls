<?php
/**
 * Este documento inserta los datos necesarios en el horario de inscripción del alumno.
 */

require_once 'database.php';
require_once 'cicloactual.php';

// Obtiene un array conteniendo los prerrequisitos de una materia.
// Posibles valores de ejemplo en la base de datos son:
//      50: Un solo numero entero, el codigo de referencia de la materia prerrequisito;
//      2,3,5: Una lista separada por comas con los numeros de referencia;
//      90%: Un porcentage de la cantidad de materias que debe haber aprobado.
function analizadorPrerrequisito ($prerrequisito_bd) {
    if ($prerrequisito_bd === false) {
        return false;
    }

    // Si el prerrequisito es un numero o una lista separada por comas.
    $es_prerrequisito_numero = preg_match('/^[\d]+(?:,[\d]+)*$/', $prerrequisito_bd);
    if ($es_prerrequisito_numero === 1) {

        return [
            'tipo' => 'numerico',
            'val' => explode(',', $prerrequisito_bd)
        ];
    }

    // Si el prerrequisito es un porcentage del pensum
    $es_prerrequisito_porcentage = preg_match('/^\d+\%$/', $prerrequisito_bd);
    if ($es_prerrequisito_porcentage === 1) {
        return [
            'tipo' => 'porcentage',
            'val' => (int) $prerrequisito_bd
        ];
    }

    // Si no se pudo determinar el tipo de prerrequisito.
    return false;
}



function materiasLlevables ($carnet) {
    if (isset($carnet) !== true || empty($carnet) === true) {
        return false;
    }
    $carnet = strtoupper($carnet);

    $materias_disponibles = obtenerCargaAcademica($carnet);
    $materias_aprobadas = obtenerMateriasAprobadas($carnet);
    $materias_pensum = obtenerPensum($carnet);

    if ($materias_disponibles === false || $materias_pensum === false) {
        return false;
    }

    $cantidad_materias_pensum = count($materias_pensum);
    $cantidad_aprobadas_del_pensum = 0;
    if ($materias_aprobadas !== false) {
        foreach ($materias_pensum as $materia) {
            foreach ($materias_aprobadas as $aprobada) {
                if ($materia['codigo_materia'] === $aprobada['codigo_materia']) {
                    $cantidad_aprobadas_del_pensum += 1;
                }
            }
        }
    }

    $materias_llevables = array_filter($materias_disponibles, function ($materia) use ($materias_aprobadas, $cantidad_materias_pensum, $cantidad_aprobadas_del_pensum) {
        $prerrequisito = analizadorPrerrequisito($materia['codigo_prerrequisito']);

        if ($materias_aprobadas !== false) {
            foreach ($materias_aprobadas as $aprobada) {
                // comprobar si la materia ya ha sido aprobada
                if ($materia['codigo_referencia'] === $aprobada['codigo_referencia']) {
                    return false;
                }
            }
            // Si el alumno ha aprobado algunas materias, comprobar si estas materias aprobadas son parte del prerrequisito.
            $prerrequisitos_cumplidos = array_filter($materias_aprobadas, function ($materia) use ($prerrequisito) {
                if ($prerrequisito['tipo'] === 'numerico') {
                    foreach ($prerrequisito['val'] as $valor) {
                        // si alguno de los prerrequisitos es cero
                        if ($valor === $materia['codigo_referencia']) {
                            return true;
                        }

                    }
                    return false;
                }
            });
        }

        // comprobar los prerrequisitos de cada materia disponible
        if ($prerrequisito['tipo'] === 'numerico') {
            foreach ($prerrequisito['val'] as $valor) {
                // si alguno de los prerrequisitos es cero
                if ($valor === '0') {
                    return true;
                }
            }
            // Si los prerrequisitos no son cero, ver si tiene todos sus prerrequisitos cumplidos.
            if ($materias_aprobadas !== false) {
                if (count($prerrequisito['val']) === count($prerrequisitos_cumplidos)) {
                    return true;
                }
            }
        }

        if ($prerrequisito['tipo'] === 'porcentage') {
            $porcentage_aprobadas = 100 / $cantidad_materias_pensum * $cantidad_aprobadas_del_pensum;
            if ($porcentage_aprobadas >= $prerrequisito['val']) {
                return true;
            }
        }

        return false;
    });

    return array_values($materias_llevables);
}

// print_r(materiasLlevables('MP01133315'));


function htmlHorario ($carnet) {
    if (isset($carnet) !== true || empty($carnet) === true) {
        return false;
    }

    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

    $materias_llevables = materiasLlevables($carnet);
    $lista_horarios = obtenerHorarios();
    // print_r($lista_horarios);
    // var_dump($materias_llevables);
    // return false;

    $html_horario = '<thead>';
    $html_horario .= '<tr>';
    $html_horario .= '<th scope="col">Día</th>';

    foreach ($lista_horarios as $num_horario => $horario) {
        $hora_inicio = $lista_horarios[$num_horario]['hora_inicio'];
        $hora_fin = $lista_horarios[$num_horario]['hora_fin'];
        $html_horario .= '<th scope="col">' . date('H:i', strtotime($hora_inicio)) . ' - ' . date('H:i', strtotime($hora_fin)) .'</th>';
    }

    $html_horario .= '</tr>';
    $html_horario .= '</thead>';
    $html_horario .= '<tbody>';

    foreach ($dias as $num_dia => $dia) {
        $html_horario .= '<tr>';
        $html_horario .= '<th scope="row">' . $dia . '</th>';

        $materias_del_dia = array_filter($materias_llevables, function ($materia) use ($num_dia) {
            return (int) $materia['dia'] === $num_dia + 1;
        });

        $materias_del_dia = array_values($materias_del_dia);

        foreach ($lista_horarios as $num_horario => $horario) {
            $hora_inicio = $lista_horarios[$num_horario]['hora_inicio'];
            $hora_fin = $lista_horarios[$num_horario]['hora_fin'];
            $html_horario .= '<td data-title="' . date('H:i', strtotime($hora_inicio)) . ' - ' . date('H:i', strtotime($hora_fin)) .'"';

            $materias_de_la_hora = array_filter($materias_del_dia, function ($materia) use ($horario) {
                return $materia['id_horario'] === $horario['id_horario'];
            });

            $materias_de_la_hora = array_values($materias_de_la_hora);
            // print_r($materias_de_la_hora);
            // return false;

            if (count($materias_de_la_hora) === 0) {
                $html_horario .= '>--';
            }

            if (count($materias_de_la_hora) === 1) {
                $html_horario .= ' data-grupo="' . $materias_de_la_hora[0]['id_grupo'] . '"';
                $html_horario .= ' data-value="' . $materias_de_la_hora[0]['codigo_materia'] . '">';
                $html_horario .= $materias_de_la_hora[0]['nombre'];
            }

            if (count($materias_de_la_hora) > 1) {
                $html_horario .= ' data-grupo="no-selection"';
                $html_horario .= ' data-value="no-selection">';
                $html_horario .= '<select class="horario__multiple-sel">';
                $html_horario .= '<option data-grupo="no-selection" value="no-selection" selected>Selecciona Una</option>';
                foreach ($materias_de_la_hora as $materia) {
                    $html_horario .= '<option data-grupo="'. $materia['id_grupo'] .'" ';
                    $html_horario .= 'value="' . $materia['codigo_materia'] . '">';
                    $html_horario .= $materia['nombre'];
                    $html_horario .= '</option>';
                    $selected = '';
                }
                $html_horario .= '</select>';
            }

            $html_horario .= '</td>';
        }

        $html_horario .= '</tr>';
    }

    $html_horario .= '</tbody>';

    return $html_horario;
}
// echo htmlHorario('MP01133315');
