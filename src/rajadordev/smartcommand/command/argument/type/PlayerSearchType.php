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

namespace rajadordev\smartcommand\command\argument\type;

use pocketmine\player\Player;
use pocketmine\Server;

enum PlayerSearchType 
{

    /** Will search the player by prefix name (example: my name is Rajador but you can find me writing "Raja") */
    case NAME_PREFIX;

    /** Will search the player with exact given name */
    case EXACT; 

    /** First per exact name after per prefix */
    case BOTH_TYPES;

    public function search(string $input) : ?Player 
    {
        $searcher = match ($this) {
            self::NAME_PREFIX => static function (Server $server, string $name) : ?Player {
                $prefix = strtolower($name);
                $lastCharsCount = null;
                $found = null;
                foreach ($server->getOnlinePlayers() as $onlinePlayer) {
                    $onlinePlayerName = strtolower($onlinePlayer->getName());
                    if (str_contains($prefix, $onlinePlayerName)) {
                        $onlinePlayerNameLength = strlen($onlinePlayerName);
                        if (is_null($lastCharsCount) || $onlinePlayerNameLength < $lastCharsCount) {
                            $found = $onlinePlayer;
                            $lastCharsCount = $onlinePlayerNameLength;
                        }
                    }
                }
                return $found;
            },
            self::EXACT => static function (Server $server, string $name) : ?Player {
                return $server->getPlayerExact($name);
            },
            self::BOTH_TYPES => static function (Server $server, string $name) : ?Player {
                return self::EXACT->search($name) ?? self::NAME_PREFIX->search($name);
            }
        };
        return $searcher(Server::getInstance(), $input);
    }

}