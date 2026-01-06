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

class StringListArgument extends BaseArgument
{

    /** @var string[] */
    protected $list;

    /**
     * @param string $name
     * @param string[] $list
     * @param boolean $required
     */
    public function __construct(string $name, array $list, bool $required = true)
    {
        parent::__construct($name, implode('|', $list), $required, function (string &$given) : bool {
            if (in_array(strtolower($given), $this->list))
            {
                $given = strtolower($given);
                return true;
            }
            return false;
        });
        $this->list = $list;
    }
}