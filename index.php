<?php
/**
 * index.php – Punto de entrada del sitio.
 *
 * Este archivo enruta la solicitud basándose en parámetros GET y utiliza el motor
 * de plantillas para cargar el contenido dinámico. En particular, se han definido rutas
 * específicas para las páginas "quienessomos" y "contacto".
 */

// Incluir el motor de plantillas (definido en inc/motorplantilla.php)
require_once __DIR__ . '/inc/motorplantilla.php';

// Instanciar el motor de plantillas
$templateEngine = new MotorPlantilla();

// Definir rutas de las plantillas de encabezado y pie de página
$headerTemplate = __DIR__ . '/templates/header.html';
$footerTemplate = __DIR__ . '/templates/footer.html';

// Comenzar el búfer para la estructura completa de la página
ob_start();

// Renderizar el encabezado
if (file_exists($headerTemplate)) {
    echo file_get_contents($headerTemplate);
} else {
    echo "<!-- No se encontró la plantilla de encabezado -->";
}

// Inicializar variables para los datos, atributos y ruta de la plantilla principal
$data = [];
$attributes = [];
$templatePath = '';

// Enrutar la petición
if (isset($_GET['pagina'])) {
    // Se procesa la petición para páginas
    $page = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_STRING);

    if ($page === 'quienessomos') {
        // Página "Quiénes Somos": Cargar datos del JSON y plantilla específica
        $jsonFile = __DIR__ . '/json/paginas/quienessomos.json';
        if (file_exists($jsonFile)) {
            $data = json_decode(file_get_contents($jsonFile), true);
        }
        $templatePath = __DIR__ . '/templates/quienessomos.html';
    } else {
        // Para otras páginas se asume un JSON correspondiente y una plantilla genérica
        $jsonFile = __DIR__ . '/json/paginas/' . $page . '.json';
        if (file_exists($jsonFile)) {
            $data = json_decode(file_get_contents($jsonFile), true);
        }
        $templatePath = __DIR__ . '/templates/page.html';
    }
} elseif (isset($_GET['contacto'])) {
    // Página de "Contacto": Se utiliza una plantilla dedicada y se definen atributos para el formulario
    $templatePath = __DIR__ . '/templates/contacto.html';
    $attributes = [
        'form' => [
            'method' => 'POST',
            'action' => 'https://email.jocarsa.com/envio.php'
        ]
    ];
} elseif (isset($_GET['producto'])) {
    // Enrutado para productos (manteniendo funcionalidad actual)
    $prodParam = filter_input(INPUT_GET, 'producto', FILTER_SANITIZE_STRING);
    $jsonFile = __DIR__ . '/json/productos/' . str_replace(" | ", "_", $prodParam) . '.json';
    if (file_exists($jsonFile)) {
        $data = json_decode(file_get_contents($jsonFile), true);
    }
    $templatePath = __DIR__ . '/templates/landing.html';
    $attributes = [
        'form' => [
            'method' => 'POST',
            'action' => 'https://email.jocarsa.com/envio.php'
        ]
    ];
} elseif (isset($_GET['categoria'])) {
    // Enrutado para categorías
    $catParam = filter_input(INPUT_GET, 'categoria', FILTER_SANITIZE_STRING);
    $jsonFile = __DIR__ . '/json/categorias/' . $catParam . '.json';
    if (file_exists($jsonFile)) {
        $data = json_decode(file_get_contents($jsonFile), true);
    }
    $templatePath = __DIR__ . '/templates/category.html';
} else {
    // Por defecto, se renderiza la página de inicio
    $jsonFile = __DIR__ . '/json/home.json';
    if (file_exists($jsonFile)) {
        $data = json_decode(file_get_contents($jsonFile), true);
    }
    $templatePath = __DIR__ . '/templates/home.html';
}

// Renderizar el contenido principal mediante el motor de plantillas
if (file_exists($templatePath)) {
    echo $templateEngine->render($templatePath, $data, $attributes);
} else {
    echo "<!-- No se encontró la plantilla: $templatePath -->";
}

// Renderizar el pie de página
if (file_exists($footerTemplate)) {
    echo file_get_contents($footerTemplate);
} else {
    echo "<!-- No se encontró la plantilla de pie de página -->";
}

// Enviar todo el contenido al navegador
ob_end_flush();
?>