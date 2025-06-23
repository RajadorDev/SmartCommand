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

class StringListArgument extends BaseArgument
{

    /** @var string[] */
    protected array $list;

    /**
     * @param string $name
     * @param string[] $list
     * @param boolean $required
     */
    public function __construct(string $name, array $list, bool $required = true)
    {
        parent::__construct($name, implode('|', $list), $required);
        $this->list = $list;
    }

    public function parse(string &$given) : bool 
    {
        if (in_array(strtolower($given), $this->list))
        {
            $given = strtotime($given);
            return true;
        }
        return false;
    }
    
}