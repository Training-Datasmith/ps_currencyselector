<?php

if (!defined('_PS_VERSION_')) {
    define('_PS_VERSION_', '9.9.9-test');
}

require __DIR__ . '/stubs/PrestaShopBoundary.php';

if (!function_exists('ps_currencyselector_load_module')) {
    function ps_currencyselector_load_module()
    {
        if (!class_exists('Ps_Currencyselector', false)) {
            require dirname(__DIR__) . '/ps_currencyselector.php';
        }
    }
}
