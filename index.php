<?php
/**
 * index.php – Punto de entrada del sitio andrei-skyblue.
 * 
 * Este código enruta la petición en función de parámetros GET (producto, pagina, contacto, categoria)
 * y carga los respectivos archivos JSON y plantillas HTML locales. Se emplea un motor de plantillas
 * (implementado en inc/motorplantilla.php) para procesar los datos y renderizar la vista.
 */

require_once __DIR__ . '/inc/motorplantilla.php';

// Instanciar el motor de plantillas
$templateEngine = new TemplateEngine();

// Definir rutas para plantillas globales locales
$headerTemplate = __DIR__ . '/templates/header.html';
$footerTemplate = __DIR__ . '/templates/footer.html';

// Iniciar búfer de salida para definir la estructura completa de la página
ob_start();

// Renderizar encabezado
if (file_exists($headerTemplate)) {
    echo file_get_contents($headerTemplate);
} else {
    echo "<!-- No se encontró la plantilla de encabezado -->";
}

// Inicializar variables para el contenido principal
$data = [];
$attributes = [];
$templatePath = '';

// Enrutamiento basado en parámetros GET
if (isset($_GET['producto'])) {
    // Procesar el parámetro 'producto'
    $prodParam = filter_input(INPUT_GET, 'producto', FILTER_SANITIZE_STRING);
    // Reemplazar " | " con guion bajo, ajustando el nombre del archivo JSON
    $prodFile = __DIR__ . '/json/productos/' . str_replace(" | ", "_", $prodParam) . '.json';
    if (file_exists($prodFile)) {
        $jsonContent = file_get_contents($prodFile);
        $data = json_decode($jsonContent, true) ?? [];
    }
    // Seleccionar plantilla específica para productos (local)
    $templatePath = __DIR__ . '/templates/landing.html';
    // Agregar atributos para el formulario, si es necesario
    $attributes = [
        'form' => [
            'method' => 'POST',
            'action' => 'https://email.jocarsa.com/envio.php'
        ]
    ];
} elseif (isset($_GET['pagina'])) {
    // Procesar el parámetro 'pagina'
    $pageParam = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_STRING);
    $pageFile = __DIR__ . '/json/paginas/' . $pageParam . '.json';
    if (file_exists($pageFile)) {
        $jsonContent = file_get_contents($pageFile);
        $data = json_decode($jsonContent, true) ?? [];
    }
    $templatePath = __DIR__ . '/templates/page.html';
} elseif (isset($_GET['contacto'])) {
    // Vista de contacto sin archivo JSON asociado, pero con atributos para el form
    $templatePath = __DIR__ . '/templates/contact.html';
    $data = [];
    $attributes = [
        'form' => [
            'method' => 'POST',
            'action' => 'https://email.jocarsa.com/envio.php'
        ]
    ];
} elseif (isset($_GET['categoria'])) {
    // Procesar el parámetro 'categoria'
    $catParam = filter_input(INPUT_GET, 'categoria', FILTER_SANITIZE_STRING);
    $catFile = __DIR__ . '/json/categorias/' . $catParam . '.json';
    if (file_exists($catFile)) {
        $jsonContent = file_get_contents($catFile);
        $data = json_decode($jsonContent, true) ?? [];
    }
    $templatePath = __DIR__ . '/templates/category.html';
} else {
    // Ruta por defecto: la página de inicio
    $homeFile = __DIR__ . '/json/home.json';
    if (file_exists($homeFile)) {
        $jsonContent = file_get_contents($homeFile);
        $data = json_decode($jsonContent, true) ?? [];
    }
    $templatePath = __DIR__ . '/templates/home.html';
}

// Renderizar el contenido principal mediante el motor de plantillas
if (file_exists($templatePath)) {
    echo $templateEngine->render($templatePath, $data, $attributes);
} else {
    echo "<!-- No se encontró la plantilla: $templatePath -->";
}

// Renderizar pie de página
if (file_exists($footerTemplate)) {
    echo file_get_contents($footerTemplate);
} else {
    echo "<!-- No se encontró la plantilla de pie de página -->";
}

// Liberar y mostrar el contenido del búfer
ob_end_flush();