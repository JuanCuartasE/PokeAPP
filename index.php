<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\CacheManager;
use App\HttpClient;
use App\Database;

// Configuración de rutas
define('ROOT_PATH', __DIR__);
define('CACHE_PATH', ROOT_PATH . '/cache');
define('DB_PATH', ROOT_PATH . '/data/pokemon.sqlite');

// Inicialización de servicios
try {
    $cache = new CacheManager(CACHE_PATH);
    $http = new HttpClient($cache);
    $db = new Database(DB_PATH);
} catch (\Exception $e) {
    die("Error inicializando la aplicación: " . $e->getMessage());
}

// Router básico
$page = $_GET['page'] ?? 'home';
$id = $_GET['id'] ?? null;
$name = $_GET['name'] ?? null;

// En este punto cargaremos las vistas basadas en $page
include ROOT_PATH . '/views/layout.php';
