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

use rajadordev\smartcommand\command\argument\type\PlayerSearchType;
use rajadordev\smartcommand\message\CommandMessages;

class PlayerArgument extends BaseArgument
{

    protected PlayerSearchType $searchType;

    public function __construct(string $name, PlayerSearchType $searchType, bool $required = true)
    {
        parent::__construct($name, 'player', $required);
        $this->searchType = $searchType;
    }

    public function parse(string &$given) : bool 
    {
        if ($target = $this->searchType->search($given)) 
        {
            $given = $target;
            return true;
        }
        return false;
    }

    public function getWrongMessage(CommandMessages $commandMessages, string $argumentUsed): string
    {
        return $commandMessages->get(CommandMessages::PLAYER_NOT_FOUND, '{name}', $argumentUsed);
    }
    
}