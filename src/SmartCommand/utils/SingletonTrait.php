<?php

declare (strict_types=1);

/***
 *   
 * Rajador Developer
 * 
 * ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
 * ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
 * ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█
 * 
 * GitHub: https://github.com/RajadorDev
 * 
 * Discord: rajadortv
 * 
 * 
**/

namespace SmartCommand\utils;

use Exception;

trait SingletonTrait 
{

    /** @var self */
    private static $instance;

    public static function getInstance() : self 
    {
        return self::$instance;
    }

    private static function setInstance(self $instance) : self 
    {
        if (self::$instance)
        {
            throw new Exception('Instance of ' . get_class($instance) . 'is already registered!');
        }
        self::$instance = $instance;
        return $instance;
    }

}