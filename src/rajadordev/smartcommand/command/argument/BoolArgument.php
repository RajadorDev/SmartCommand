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
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * 
**/

namespace rajadordev\smartcommand\command\argument;

class BoolArgument extends BaseArgument
{

    /** @var string */
    protected string $trueName, $falseName;

    public function __construct(string $name, bool $required = true, string $trueName = 'true', string $falseName = 'false', bool $showAsList = false)
    {
        parent::__construct($name, $showAsList ? implode('|', [$trueName, $falseName]) : 'bool', $required, function (string &$given) : bool {
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