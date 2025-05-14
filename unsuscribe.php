<?php
/**
 * unsuscribe.php – Script para gestionar la desuscripción de correos.
 *
 * Este código recibe el parámetro GET "email", valida que sea un correo válido
 * y lo registra en una base de datos SQLite. Se evita duplicar registros para un mismo correo.
 * La respuesta se genera a través de una función que renderiza una página HTML minimalista.
 */

header('Content-Type: text/html; charset=utf-8');

/**
 * Renderiza una respuesta HTML con un título y un mensaje.
 *
 * @param string $title   Título de la respuesta.
 * @param string $message Mensaje principal.
 */
function renderResponse($title, $message) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body {
            background: #f0f2f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .response-box {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .response-box h1 {
            margin-bottom: 15px;
            font-size: 24px;
            color: #333;
        }
        .response-box p {
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="response-box">
        <h1>{$title}</h1>
        <p>{$message}</p>
    </div>
</body>
</html>
HTML;
}

// Recuperar y validar el correo del parámetro GET
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    renderResponse("Error en la Desuscripción", "No se proporcionó un correo electrónico válido.");
    exit;
}

// Definir la ruta de la base de datos (creada en la carpeta 'data')
$dbPath = __DIR__ . '/data/unsuscribe.db';

// Asegurarse de que exista el directorio para la base de datos
if (!is_dir(dirname($dbPath))) {
    mkdir(dirname($dbPath), 0755, true);
}

try {
    // Conectar a la base de datos SQLite
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear la tabla de desuscripciones si no existe
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS unsubscribes (
            unsubscribe_id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            request_ip TEXT,
            user_agent TEXT,
            unsubscribed_at DATETIME DEFAULT (datetime('now','localtime'))
        )
    ");

    // Verificar si el correo ya se había desuscrito
    $stmtCheck = $pdo->prepare("SELECT email FROM unsubscribes WHERE email = :email LIMIT 1");
    $stmtCheck->bindParam(':email', $email, PDO::PARAM_STR);
    $stmtCheck->execute();
    if ($stmtCheck->fetch(PDO::FETCH_ASSOC)) {
        renderResponse("Desuscripción Confirmada", "El correo <strong>" . htmlspecialchars($email) . "</strong> ya se encontraba desuscrito.");
        exit;
    }

    // Extraer información de la solicitud
    $requestIp = $_SERVER['REMOTE_ADDR'] ?? 'IP no disponible';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User Agent no disponible';

    // Insertar el registro de desuscripción
    $stmtInsert = $pdo->prepare("INSERT INTO unsubscribes (email, request_ip, user_agent) VALUES (:email, :request_ip, :user_agent)");
    $stmtInsert->execute([
        ':email'       => $email,
        ':request_ip'  => $requestIp,
        ':user_agent'  => $userAgent
    ]);

    renderResponse("Desuscripción Exitosa", "El correo <strong>" . htmlspecialchars($email) . "</strong> ha sido desuscrito correctamente.");
} catch (PDOException $ex) {
    renderResponse("Error en la Desuscripción", "No se pudo procesar su solicitud debido a un error: " . htmlspecialchars($ex->getMessage()));
    exit;
}
?>