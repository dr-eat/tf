<?php
require_once '../config/config.php';
require_once '../models/App.php';
require_once '../models/MDb.php';
require_once '../controllers/CBase.php';
require_once '../models/MBase.php';
require_once '../models/MActions.php';
require_once '../models/MClient.php';
require_once '../controllers/CAccount.php';
require_once '../controllers/CClient.php';
require_once '../controllers/CRate.php';
require_once '../controllers/CTransaction.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit();
}

if (!checkAuth()) {
    httpReponse(401);
    exit(1);
}

$controller_data = getController($_SERVER['REQUEST_URI']);
if (is_numeric($controller_data)) {
    httpReponse($controller_data);
    exit(1);
}

$controller = new $controller_data[0]();
$res = $controller->{$controller_data[1]}();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($res);


/**
 * Simplest way to check clients authentication.
 * NOT FOR PRODUCTION!
 *
 * @return bool
 */
function checkAuth(): bool
{
    App::get()->client_id = MClient::getAuthId($_GET['login'] ?? '', $_GET['pwd'] ?? '');
    return (bool)App::get()->client_id;
}

/**
 * Get called controller and method based on full URL
 *
 * @param string $uri Full url
 * @return array|int
 */
function getController(string $uri): array|int
{
    if (empty($uri)) {
        return 400;
    }

    $path = parse_url($uri, PHP_URL_PATH);
    $parts = explode('/', trim($path, '/'));
    $controller_file_name = array_shift($parts);
    if (!$controller_file_name) {
        return 400;
    }
    $controller_method = array_shift($parts);
    if (!$controller_method) {
        return 400;
    }

    $controller_class_name = 'C' . ucfirst($controller_file_name);
    $controller_file = '../controllers/' . $controller_class_name . '.php';
    if (!is_file($controller_file)) {
        return 404;
    }
    require_once($controller_file);

    if (!class_exists($controller_class_name) || !method_exists($controller_class_name, $controller_method))
    {
        return 404;
    }
    return [$controller_class_name, $controller_method];
}

/**
 * http error code to be returned
 *
 * @param int $code http error code
 * @param string $msg additional message
 * @return void
 */
function httpReponse(int $code = 0, string $msg = ''): void
{
    $msg = match($code) {
        401 => 'Access denied',
        403 => 'Forbidden',
        404 => 'File not found',
        500 => 'Internal Server Error',
        default => 'Unexpected error'
    };
    header("HTTP/1.1 {$code} {$msg}");
}
