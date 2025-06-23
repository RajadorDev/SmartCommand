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

use pocketmine\Server;
use pocketmine\world\World;
use rajadordev\smartcommand\message\CommandMessages;

/**
 * This argument will transform string in World
 */
class WorldArgument extends BaseArgument
{

    /**
     * @param string $name
     * @param boolean $autoload If true will load automatically 
     * @param boolean $required
     */
    public function __construct(string $name, protected readonly bool $autoload = true, bool $required = true)
    {
        parent::__construct(
            $name,
            'string',
            $required
        );
    }

    public function parse(string &$given) : bool 
    {
        $worldManager = Server::getInstance()->getWorldManager();
        if ($worldManager->isWorldGenerated($given))
        {
            if ($this->autoload)
            {
                $worldManager->loadWorld($given);
            }
            $world = $worldManager->getWorldByName($given);
            if ($world instanceof World)
            {
                $given = $world;
                return true;
            }
        }
        return false;
    }

    public function getWrongMessage(CommandMessages $commandMessages, string $argumentUsed): string
    {
        return $commandMessages->get(CommandMessages::INVALID_WORLD, '{name}', $argumentUsed);
    }
    
}