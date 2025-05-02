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

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\BaseCommandMessages;
use SmartCommand\message\CommandMessages;

final class CommandUtils 
{

    /**
     * It will remove every strings with none content
     *
     * @param array $args
     * @return void
     */
    public static function removeEmptyArgs(array &$args) 
    {
        $args = array_values(
            array_filter(
                $args,
                static function (string $text) : bool {
                    return trim($text) != '';
                }
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

}