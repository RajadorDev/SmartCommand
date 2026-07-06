<?php

declare (strict_types=1);

/***
 *   
 * Rajador Developer 
 * 
 *  ██████╗  █████╗      ██╗ █████╗ ██████╗  ██████╗ ██████╗ 
 *  ██╔══██╗██╔══██╗     ██║██╔══██╗██╔══██╗██╔═══██╗██╔══██╗
 *  ██████╔╝███████║     ██║███████║██║  ██║██║   ██║██████╔╝
 *  ██╔══██╗██╔══██║██   ██║██╔══██║██║  ██║██║   ██║██╔══██╗
 *  ██║  ██║██║  ██║╚█████╔╝██║  ██║██████╔╝╚██████╔╝██║  ██║
    ╚═╝  ╚═╝╚═╝  ╚═╝ ╚════╝ ╚═╝  ╚═╝╚═════╝  ╚═════╝ ╚═╝  ╚═╝
 * 
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * @copyright 2023 - 2027 Rajador Developer
 * 
 * Repository: https://github.com/rajadordev/SmartCommand
 * 
 * You can use AutoPluginUpdater to update SmartCommand automatically: https://github.com/rajadordev/AutoPluginUpdater
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
        $wasChanged = false;
        foreach ($args as $index => $argumentGiven) {
            if ($ignoreIndexFrom !== null && $index >= $ignoreIndexFrom) {
                break;
            }

            if ($argumentGiven === '') {
                unset($args[$index]);
                $wasChanged = true;
            }
        }

        if ($wasChanged) {
            $args = array_values($args);
        }
    }

    /**
     * Check if the sender is a player
     *
     * @param CommandSender $sender
     * @param string $messageWhenFail if not be passed, no message will be sent
     * @return boolean
     * @deprecated Useless
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

    /**
     * @param mixed $index
     * @return boolean
     */
    public static function validIndexType($index) : bool 
    {
        return is_string($index) || is_int($index);
    }

    /**
     * @param array $lines
     * @param boolean $returnText
     * @param string $prefix
     * @return string[]|string
     */
    public static function textLinesWithPrefix(array $lines, bool $returnText = true, string $prefix = '§8-  §7')
    {
        if ($returnText) {
            return self::textLinesWithPrefixString($lines, $prefix);
        }
        return self::textLinesWithPrefixArray($lines, $prefix);
    }

    public static function textLinesWithPrefixArray(array $lines, string $prefix = '§8-  §7') : array
    {
        foreach ($lines as $lineIndex => $textLine) {
            $lines[$lineIndex] = $prefix . $textLine;
        }
        return $lines;
    }

    public static function textLinesWithPrefixString(array $lines, string $prefix = '§8-  §7') : string
    {
        $text = '';
        foreach ($lines as $textLine) {
            $textLine = $prefix . $textLine;
            $text .= $text !== '' ? "\n$textLine" : $textLine;
        }
        return $text;
    }

    /**
     * @param string $folder
     * @return bool
     */
    public static function openFolder(string $folder) : bool 
    {
        if (!file_exists($folder))
        {
            return mkdir($folder);
        }
        return false;
    }

}