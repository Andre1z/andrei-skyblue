<?php
/**
 * index.php – Punto de entrada del sitio.
 *
 * Este archivo enruta la solicitud basándose en parámetros GET (producto, pagina, contacto, categoria)
 * y utiliza un motor de plantillas para renderizar los contenidos correspondientes.
 */

// Asegúrate de incluir el archivo del motor de plantillas  
require_once __DIR__ . '/inc/motorplantilla.php';

// Instanciar el motor de plantillas
// Aquí usamos el nombre de la clase definida en motorplantilla.php (por ejemplo, MotorPlantilla)
$templateEngine = new MotorPlantilla();

// Definir rutas de las plantillas de encabezado y pie de página
$headerTemplate = __DIR__ . '/templates/header.html';
$footerTemplate = __DIR__ . '/templates/footer.html';

// Iniciar el búfer de salida para estructurar toda la página
ob_start();

// Renderizar el encabezado
if (file_exists($headerTemplate)) {
    echo file_get_contents($headerTemplate);
} else {
    echo "<!-- No se encontró la plantilla de encabezado -->";
}

// Inicializar variables para el contenido principal y los atributos del formulario
$data = [];
$attributes = [];
$templatePath = '';

// Enrutamiento basado en los parámetros GET
if (isset($_GET['producto'])) {
    // Procesar parámetro 'producto'
    $prodParam = filter_input(INPUT_GET, 'producto', FILTER_SANITIZE_STRING);
    $prodFile = __DIR__ . '/json/productos/' . str_replace(" | ", "_", $prodParam) . '.json';
    if (file_exists($prodFile)) {
        $jsonContent = file_get_contents($prodFile);
        $data = json_decode($jsonContent, true) ?? [];
    }
    $templatePath = __DIR__ . '/templates/landing.html';
    $attributes = [
        'form' => [
            'method' => 'POST',
            'action' => 'https://email.jocarsa.com/envio.php'
        ]
    ];
} elseif (isset($_GET['pagina'])) {
    // Procesar parámetro 'pagina'
    $pageParam = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_STRING);
    $pageFile = __DIR__ . '/json/paginas/' . $pageParam . '.json';
    if (file_exists($pageFile)) {
        $jsonContent = file_get_contents($pageFile);
        $data = json_decode($jsonContent, true) ?? [];
    }
    $templatePath = __DIR__ . '/templates/page.html';
} elseif (isset($_GET['contacto'])) {
    // Vista de contacto
    $templatePath = __DIR__ . '/templates/contact.html';
    $data = [];
    $attributes = [
        'form' => [
            'method' => 'POST',
            'action' => 'https://email.jocarsa.com/envio.php'
        ]
    ];
} elseif (isset($_GET['categoria'])) {
    // Procesar parámetro 'categoria'
    $catParam = filter_input(INPUT_GET, 'categoria', FILTER_SANITIZE_STRING);
    $catFile = __DIR__ . '/json/categorias/' . $catParam . '.json';
    if (file_exists($catFile)) {
        $jsonContent = file_get_contents($catFile);
        $data = json_decode($jsonContent, true) ?? [];
    }
    $templatePath = __DIR__ . '/templates/category.html';
} else {
    // Página de inicio por defecto
    $homeFile = __DIR__ . '/json/home.json';
    if (file_exists($homeFile)) {
        $jsonContent = file_get_contents($homeFile);
        $data = json_decode($jsonContent, true) ?? [];
    }
    $templatePath = __DIR__ . '/templates/home.html';
}

// Renderizar el contenido principal usando el motor de plantillas
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

// Liberar el contenido del búfer y enviarlo al navegador
ob_end_flush();
?>