<?php

function _fw_autoload($classname) {
    if (substr($classname, 0, 9) == 'Framewub\\') {
        $path = __DIR__ . '/src/' . str_replace('\\', '/', substr($classname, 9)) . '.php';
        include $path;
    }
    else if (substr($classname, 0, 5) == 'Test\\') {
        $path = __DIR__ . '/test/src/' . str_replace('\\', '/', substr($classname, 5)) . '.php';
        include $path;
    }
}

spl_autoload_register('_fw_autoload');
