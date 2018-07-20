<?php declare(strict_types=1);

$uri = $_SERVER['REQUEST_URI'];

if ($uri !== '/' && file_exists(getcwd() . '/public' . $uri)) {
    return false;
}

require getcwd() . '/vendor/autoload.php';
new FlatFile\Application;
