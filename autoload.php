<?php

function _fw_autoload($classname) {
    if (substr($classname, 0, 9) == 'Framewub\\') {
        $path = __DIR__ . '/src/' . str_replace('\\', '/', substr($classname, 9)) . '.php';
        include $path;
    } else {
        throw new Exception("No file for class {$classname}");
    }
}

spl_autoload_register('_fw_autoload');
