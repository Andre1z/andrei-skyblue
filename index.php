<?php
/**
 * index.php – Punto de entrada del sitio.
 *
 * Este archivo enruta la solicitud basándose en parámetros GET y utiliza el motor de plantillas
 * (MotorPlantilla) para renderizar el contenido correspondiente.
 */

// Incluir el motor de plantillas
require_once __DIR__ . '/inc/motorplantilla.php';

// Instanciar el motor de plantillas
$templateEngine = new MotorPlantilla();

// Definir rutas de las plantillas de encabezado y pie de página
$headerTemplate = __DIR__ . '/templates/header.html';
$footerTemplate = __DIR__ . '/templates/footer.html';

// Iniciar el búfer de salida
ob_start();

// Renderizar el encabezado
if (file_exists($headerTemplate)) {
    echo file_get_contents($headerTemplate);
} else {
    echo "<!-- No se encontró la plantilla de encabezado -->";
}

// Inicializar variables para los datos, atributos y la plantilla principal
$data = [];
$attributes = [];
$templatePath = '';

// Enrutamiento basado en los parámetros GET
if (isset($_GET['pagina'])) {
    // Se procesa la petición para páginas
    $page = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_STRING);

    if ($page === 'quienessomos') {
        // Página "Quiénes Somos"
        $jsonFile = __DIR__ . '/json/paginas/quienessomos.json';
        if (file_exists($jsonFile)) {
            $jsonContent = file_get_contents($jsonFile);
            $decodedData = json_decode($jsonContent, true);
            $data = is_array($decodedData) ? $decodedData : [];
        }
        $templatePath = __DIR__ . '/templates/quienessomos.html';
    } else {
        // Otras páginas
        $jsonFile = __DIR__ . '/json/paginas/' . $page . '.json';
        if (file_exists($jsonFile)) {
            $jsonContent = file_get_contents($jsonFile);
            $decodedData = json_decode($jsonContent, true);
            $data = is_array($decodedData) ? $decodedData : [];
        }
        $templatePath = __DIR__ . '/templates/page.html';
    }
} elseif (isset($_GET['contacto'])) {
    // Página de "Contacto"
    $templatePath = __DIR__ . '/templates/contacto.html';
    $attributes = [
        'form' => [
            'method' => 'POST',
            'action' => 'https://email.jocarsa.com/envio.php'
        ]
    ];
    $data = []; // Garantizamos que sea un arreglo
} elseif (isset($_GET['producto'])) {
    // Página de producto
    $prodParam = filter_input(INPUT_GET, 'producto', FILTER_SANITIZE_STRING);
    $jsonFile = __DIR__ . '/json/productos/' . str_replace(" | ", "_", $prodParam) . '.json';
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $decodedData = json_decode($jsonContent, true);
        $data = is_array($decodedData) ? $decodedData : [];
    }
    $templatePath = __DIR__ . '/templates/landing.html';
    $attributes = [
        'form' => [
            'method' => 'POST',
            'action' => 'https://email.jocarsa.com/envio.php'
        ]
    ];
} elseif (isset($_GET['categoria'])) {
    // Página de categoría
    $catParam = filter_input(INPUT_GET, 'categoria', FILTER_SANITIZE_STRING);
    $jsonFile = __DIR__ . '/json/categorias/' . $catParam . '.json';
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $decodedData = json_decode($jsonContent, true);
        $data = is_array($decodedData) ? $decodedData : [];
    }
    $templatePath = __DIR__ . '/templates/category.html';
} else {
    // Página de inicio por defecto
    $jsonFile = __DIR__ . '/json/home.json';
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $decodedData = json_decode($jsonContent, true);
        $data = is_array($decodedData) ? $decodedData : [];
    }
    $templatePath = __DIR__ . '/templates/home.html';
}

// Aseguramos que $data sea un arreglo antes de renderizar
$data = is_array($data) ? $data : [];

// Renderizar el contenido principal utilizando el motor de plantillas
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

// Enviar el contenido del búfer al navegador
ob_end_flush();
?>