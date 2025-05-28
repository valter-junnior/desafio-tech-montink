<?php
session_start();

require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../helpers.php';

spl_autoload_register(function ($class_name) {
    $paths = [
        __DIR__ . '/../models/' . $class_name . '.php',
        __DIR__ . '/../controllers/' . $class_name . '.php',
        __DIR__ . '/../services/' . $class_name . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/../routes.php';