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
    else if (substr($classname, 0, 17) == 'Psr\\Http\\Message\\') {
        $path = __DIR__ . '/vendor/http-message/src/' . str_replace('\\', '/', substr($classname, 17)) . '.php';
        include $path;
    }
}

spl_autoload_register('_fw_autoload');

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        return [
            'Referer' => 'http://example.com',
            'Upgrade-Insecure-Requests' => '1',
            'Host' => 'example.com',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'nl,en-US;q=0.7,en;q=0.3',
            'Connection' => 'keep-alive'
        ];
    }
}

// Framewub\Services::register('Session', function () { return new Framewub\Session\Cli(); });