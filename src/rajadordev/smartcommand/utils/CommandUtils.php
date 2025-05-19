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

namespace rajadordev\smartcommand\utils;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use rajadordev\smartcommand\command\SmartCommand;

final class CommandUtils 
{

    /**
     * It will remove every strings with none content
     *
     * @param array $args
     * @param int|null $ignoreIndexFrom
     * @return void
     */
    public static function removeEmptyArgs(array &$args, ?int $ignoreIndexFrom = null) : void
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
    public static function register(string $prefix, SmartCommand $command) : void
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
    public static function registerAll(string $prefix, array $commands) : void
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

    /**
     * @param mixed $index
     * @return boolean
     */
    public static function validIndexType($index) : bool 
    {
        return is_string($index) || is_int($index);
    }

    /**
     * @param array $texts
     * @param boolean $returnText If false will return string[]
     * @param string $prefix
     * @return string|string[]
     */
    public static function textLinesWithPrefix(array $texts, bool $returnText = true, string $prefix = '§8-  §7') : string|array
    {
        $text = array_map(
            static function (string $text) use ($prefix) : string {
                return $prefix . $text;
            },
            $texts
        );
        if ($returnText)
        {
            return implode("\n", $text);
        }
        return $text;
    }

}