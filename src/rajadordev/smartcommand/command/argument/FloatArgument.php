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

class FloatArgument extends BaseArgument
{

    /**
     * @param string $name
     * @param boolean $required
     * @param boolean $strict If true, will not convert integers to float
     */
    public function __construct(string $name, bool $required = true, protected readonly bool $strict)
    {
        parent::__construct($name, 'float', $required);
    }

    public function parse(string &$given) : bool 
    {
        if (is_numeric($given))
        {
            if (strpos($given, '.') === false && $this->strict)
            {
                return false;
            }
            $given = (float) $given;
            return true;
        }
        return false;
    }

}