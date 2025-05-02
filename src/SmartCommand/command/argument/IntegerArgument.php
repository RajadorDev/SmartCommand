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

class IntegerArgument extends BaseArgument
{

    public function __construct(string $name, bool $required = true)
    {
        parent::__construct($name, 'interger', $required, static function (string &$given) : bool {
            if (is_numeric($given))
            {
                if (strpos($given, '.') === false)
                {
                    $given = (int) $given;
                    return true;
                }
            }
            return false;
        });
    }
    
}