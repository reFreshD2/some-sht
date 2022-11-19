<?php

const APP_NAME = 'App\\';

spl_autoload_register(static function ($className) {
    require_once __DIR__
        . str_replace(
            '\\',
            DIRECTORY_SEPARATOR,
            str_replace(APP_NAME, '/src/', $className)
        )
        . '.php';
});
