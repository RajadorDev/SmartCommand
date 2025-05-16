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

use pocketmine\level\Level;
use pocketmine\Server;
use SmartCommand\message\CommandMessages;

/**
 * This argument will transform string in Level
 */
class WorldArgument extends BaseArgument
{

    /**
     * @param string $name
     * @param boolean $autoload If true will load automatically 
     * @param boolean $required
     */
    public function __construct(string $name, bool $autoload = true, bool $required = true)
    {
        parent::__construct(
            $name,
            'string',
            $required,
            static function (string &$given) use ($autoload) : bool {
                $server = Server::getInstance();
                if ($server->isLevelGenerated($given))
                {
                    if ($autoload)
                    {
                        $server->loadLevel($given);
                    }
                    $world = $server->getLevelByName($given);
                    if ($world instanceof Level)
                    {
                        $given = $world;
                        return true;
                    }
                }
                return false;
            }
        );
    }

    public function getWrongMessage(CommandMessages $commandMessages, string $argumentUsed): string
    {
        return $commandMessages->get(CommandMessages::INVALID_WORLD, '{name}', $argumentUsed);
    }
    
}