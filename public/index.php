<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Router;

$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);
?>