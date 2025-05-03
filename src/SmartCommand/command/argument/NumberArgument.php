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

namespace SmartCommand\command\argument;

/**
 * This argument should be used when you will accept float and integers
 */
class NumberArgument extends BaseArgument
{

    public function __construct(string $name, bool $required = true)
    {
        parent::__construct($name, 'number', $required, 
            static function (string &$given) : bool {
                if (is_numeric($given))
                {
                    if (strpos($given, '.') !== false) 
                    {
                        $given = (float) $given;
                    } else {
                        $given = (int) $given;
                    }
                }
                return false;
            }
        );
    }

}