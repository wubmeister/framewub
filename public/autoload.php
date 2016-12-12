<?php

function _fw_autoload($classname) {
    if (substr($classname, 0, 9) == 'Framewub\\') {
        $path = dirname(__DIR__) . '/src/' . str_replace('\\', '/', substr($classname, 9)) . '.php';
        include $path;
    }
    else if (substr($classname, 0, 17) == 'Psr\\Http\\Message\\') {
        $path = dirname(__DIR__) . '/vendor/http-message/src/' . str_replace('\\', '/', substr($classname, 17)) . '.php';
        include $path;
    }
}

spl_autoload_register('_fw_autoload');
