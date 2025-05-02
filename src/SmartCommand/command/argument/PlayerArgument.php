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

use InvalidArgumentException;
use pocketmine\Server;
use pocketmine\Player;

class PlayerArgument extends BaseArgument
{

    const SEARCH_FROM_PREFIX = 0;

    const SEARCH_EXACT = 1;

    /** @var int */
    protected $searchType;

    public function __construct(string $name, int $searchType, bool $required = true)
    {
        if (in_array($searchType, [self::SEARCH_FROM_PREFIX, self::SEARCH_EXACT]))
        {
            throw new InvalidArgumentException("$searchType is not valid for fetch players");
        }
        parent::__construct($name, 'player', $required, function (string &$given) : bool {
            if ($this->searchType == self::SEARCH_EXACT)
            {
                $player = Server::getInstance()->getPlayerExact($given);
            } else if ($this->searchType == self::SEARCH_FROM_PREFIX) {
                $player = Server::getInstance()->getPlayer($given);
            }
            if (isset($player) && $player instanceof Player) 
            {
                $given = $player;
                return true;
            }
            return false;
        });
        $this->searchType = $searchType;
    }
}