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

namespace SmartCommand\utils;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\CommandSender;
use SmartCommand\command\SmartCommand;

final class CommandUtils 
{

    /**
     * It will remove every strings with none content
     *
     * @param array $args
     * @param int|null $ignoreIndexFrom
     * @return void
     */
    public static function removeEmptyArgs(array &$args, $ignoreIndexFrom = null) 
    {
        $args = array_values(
            array_filter(
                $args,
                static function (string $text, int $key) use ($ignoreIndexFrom) : bool {
                    return (trim($text) != '' || (is_null($ignoreIndexFrom) || $key >= $ignoreIndexFrom));
                },
                ARRAY_FILTER_USE_BOTH
            )
        );
    }

    /**
     * Check if the sender is a player
     *
     * @param CommandSender $sender
     * @param string $messageWhenFail if not be passed, no message will be sent
     * @return boolean
     */
    public static function playerParse(CommandSender $sender, string $messageWhenFail = null) : bool 
    {
        if (!($sender instanceof Player))
        {
            if (is_string($messageWhenFail))
            {
                $sender->sendMessage($messageWhenFail);
            }
            return false;
        }
        return true;
    }

    /**
     * @param string $prefix
     * @param SmartCommand
     */
    public static function register(string $prefix, SmartCommand $command)
    {
        Server::getInstance()->getCommandMap()->register(
            $prefix,
            $command
        );
    }

    /**
     * @param string $prefix
     * @param SmartCommand[] $commands
     * @return void
     */
    public static function registerAll(string $prefix, array $commands)
    {
        Server::getInstance()->getCommandMap()
        ->registerAll($prefix, $commands);
    }

    /**
     * @param CommandSender $sender
     * @return string
     */
    public static function hashSender(CommandSender $sender) : string
    {
        return $sender instanceof Player ? strtolower($sender->getName()) : '@CONSOLE'; 
    }

}