<?php

/*
 * Recibe peticiones AJAX para cargar contenido dinámico sin recargar la página.
 * Las páginas contenidas en esta misma carpeta son las secciones de contenido
 * que se cargan dinamicamente.
 */

// Si el HTTP request no proviene de AJAX, redireccionar al index.
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    header('Location: ../public_html/');
    return false;
}

// Los HTTP Request de Ajax se envían utilizando el metodo post, conteniendo la página solicitada.
if (isset($_POST['partial']) === false || empty($_POST['partial']) === true) {
    header('Location: ../public_html/');
    return false;
}

require_once '../vendor/autoload.php';
use Noodlehaus\Config;

session_start();

// Si la página solicitada no existe, enviar aquí:
$pagina_por_defecto = 'login.php';
$requested_page = $_POST['partial'];

// Carga los contenidos desde los archivos parciales y los envía al cliente.
function cargarContenidos ($pagina_solicitada) {

    if (is_file($pagina_solicitada) === false) {
        $error_msg = 'La página solicitada no existe';
        echo json_encode([
            'pagina' => 'no-page',
            'contents' => $error_msg
        ]);
        return false;
    }

    $content_to_send = '';

    // En todas las páginas donde el usuario esté con sesión iniciada, cargar primero la sección para cerrar sesión.
    if (isset($_SESSION['loggedin']) === true && $_SESSION['loggedin'] === true) {
        require_once 'cerrarsesion.php';
        $content_to_send .= $section_contents;
    }

    require_once $pagina_solicitada;
    $content_to_send .= $section_contents;

    echo json_encode([
        'pagina' => $pagina_solicitada,
        'contents' => $content_to_send
    ]);
    return true;

}

/*
 * Ingresar en config.json los archivos que deben ser protegidos y asignarles el valor true.
 * Para que el usuario acceda a archivos protegidos debe de tener una sessión activa.
 * El resto de archivos que no requieren protección se detectan automáticamente.
 * De esta forma simplemente se agregan archivos php en esta carpeta y si requieren
 * inicio de sesión, se declaran en la configuración.
 */

$conf = new Config('../config/config.json');

$archivos_protegidos = $conf->get('archivos_protegidos');

// Obtener todos los archivos en este directorio que sean PHP.
$directory = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($directory);
$php_files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($php_files as $file_name => $ignore) {

    // Quitar la ruta del archivo, solo necesito el nombre.
    $file_name = basename($file_name);

    // Si el archivo es este mismo, saltar al siguiente.
    if (basename($file_name) === basename(__FILE__)) {
        continue;
    }

    // Si el archivo se ha declarado como protegido, saltar al siguiente.
    if (isset($archivos_protegidos[$file_name]) === true) {
        continue;
    }

    $archivos_protegidos[$file_name] = false;

}

// Si la página solicitada no existe, enviar la página por defecto.
if (isset($archivos_protegidos[$requested_page]) === false) {
    cargarContenidos($pagina_por_defecto);
    return false;
}

// Si el usuario no ha iniciado sessión, redireccionar a la página por defecto cuando
// intente entrara a una página protegida.
if (isset($_SESSION['loggedin']) === false || $_SESSION['loggedin'] === false) {
    if ($archivos_protegidos[$requested_page] === true) {
        cargarContenidos($pagina_por_defecto);
        return false;
    }
}

// Si en este punto, el script no ha sido detenido, dar acceso a las páginas.
cargarContenidos($requested_page);
