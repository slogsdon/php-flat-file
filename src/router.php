<?php declare(strict_types=1);

$uri = $_SERVER['REQUEST_URI'];
$cwd = (string)getcwd();

if (is_string($uri) && $uri !== '/' && file_exists($cwd . '/public' . $uri)) {
    return false;
}

require $cwd . '/vendor/autoload.php';
new FlatFile\Application;
