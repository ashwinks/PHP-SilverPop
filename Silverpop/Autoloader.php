<?php

namespace Silverpop;

class Autoloader {

    public static function register()
    {
        spl_autoload_register(array(new self, 'autoload'));
    }

    public static function autoload($class)
    {
        $file = realpath(dirname(__FILE__)) . '/' . str_replace('\\', '/', preg_replace('{^Silverpop\\\}', '', $class)) . '.php';
        if (0 !== strpos($class, 'Silverpop\\')){
            return;
        }else if (file_exists($file)){
            include ($file);
        }
    }
}