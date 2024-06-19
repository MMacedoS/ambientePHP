<?php
if (!isset($_SESSION)) {
    @session_start();
}

spl_autoload_register(function ($instancia) {
    $directories = [
        'Controllers' => __DIR__ . '/../Controllers/',
        'Admin' => __DIR__ . '/../Controllers/Admin/',
        'Models' => __DIR__ . '/../Models/',
        'Routers' => __DIR__ . '/../Routers/',        
        'Services' => __DIR__ . '/../Services/'
    ];

    foreach ($directories as $directory) {
        $filePath = $directory . $instancia . '.php';
        if (file_exists($filePath)) {
            require $filePath;
            return;
        }
    }
});