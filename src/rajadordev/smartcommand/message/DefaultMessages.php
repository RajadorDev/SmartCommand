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

namespace rajadordev\smartcommand\message;

use Exception;
use InvalidArgumentException;
use pocketmine\plugin\PluginLogger;
use pocketmine\Server;
use pocketmine\utils\Config;
use rajadordev\smartcommand\message\CommandMessages;

/**
 * @method static CommandMessagesTemplate PORTUGUESE()
 * @method static CommandMessagesTemplate ENGLISH()
 */
final class DefaultMessages 
{

    /** @var array<string,CommandMessages> */
    private static $messages = [];

    public static function init(string $dir, array $defaultList)
    {
        if (count(self::$messages) == 0)
        {
            foreach ($defaultList as $name => $messagesData)
            {
                $fileName = $messagesData['file'];
                $messages = $messagesData['data'];
                $filePath = $dir . $fileName;
                if (file_exists($filePath))
                {
                    self::$messages[strtoupper($name)] = BaseCommandMessages::messagesFromFile($filePath, '', Config::JSON, $messages);
                    Server::getInstance()->getLogger()->debug("Default messages $name registered suceffully from $filePath");
                } else {
                    throw new Exception("File $filePath not found");
                }
            }
        } else {
            throw new Exception("Default messages already registered");
        }
    }

    /**
     * @param string $name
     * @return CommandMessagesTemplate
     */
    public static function __callStatic($name, $arguments)
    {
        if (isset(self::$messages[$name]))
        {
            return new CommandMessagesTemplate(self::$messages[$name]);
        } else {
            throw new InvalidArgumentException("Default message: \"$name\" does not exists");
        }
    }

}