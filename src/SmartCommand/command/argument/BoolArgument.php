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

class BoolArgument extends BaseArgument
{

    /** @var string */
    protected $trueName, $falseName;

    public function __construct(string $name, bool $required = true, string $trueName = 'true', string $falseName = 'false')
    {
        parent::__construct($name, 'bool', $required, function (string &$given) : bool {
            $input = strtolower($given);
            if (in_array($input, [$this->trueName, $this->falseName]))
            {
                $given = $input == $this->trueName;
                return true;
            }
            return false;
        });
        $this->trueName = $trueName;
        $this->falseName = $falseName;
    }
}