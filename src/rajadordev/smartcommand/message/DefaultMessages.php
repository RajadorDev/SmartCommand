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
use pocketmine\Server;
use pocketmine\utils\Config;
use rajadordev\smartcommand\api\SmartCommandAPI;
use rajadordev\smartcommand\message\CommandMessages;

/**
 * @method static CommandMessagesTemplate PORTUGUESE()
 * @method static CommandMessagesTemplate ENGLISH()
 */
final class DefaultMessages 
{

    /** This is the global index to save DefaultMessages and reuse when there are 2 or more SmartCommand libs running */
    const MESSAGES_GLOBAL_INDEX = SmartCommandAPI::GLOBAL_PREFIX_ID . '-messages-' . SmartCommandAPI::VERSION;

    /** @var array<string,CommandMessages> */
    private static $messages = [];

    public static function tryLoadFromGlobal() : bool
    {
        if (isset($GLOBALS[self::MESSAGES_GLOBAL_INDEX])) {
            foreach ($GLOBALS[self::MESSAGES_GLOBAL_INDEX]::all() as $name => $messages)
            {
                /** @var BaseCommandMessages $messages */
                self::$messages[$name] = new BaseCommandMessages($messages->copyMessages(), $messages->getPrefix());
            }
            return true;
        }
        return false;
    }

    public static function init(string $dir, array $defaultList)
    {
        if (count(self::$messages) == 0)
        {
            foreach ($defaultList as $name => $messagesData)
            {
                $fileName = $messagesData['file'];
                $messages = $messagesData['data'];
                $filePath = $dir . $fileName;
                if (!file_exists($filePath))
                {
                    file_put_contents($filePath, json_encode($messages));
                }
                self::$messages[strtoupper($name)] = BaseCommandMessages::messagesFromFile($filePath, '', Config::JSON, $messages);
                Server::getInstance()->getLogger()->debug("Default messages $name registered suceffully from $filePath");
            }
            $GLOBALS[self::MESSAGES_GLOBAL_INDEX] = get_called_class();
        } else {
            throw new Exception("Default messages already registered");
        }
    }

    /** @return array<string,CommandMessages> */
    public static function all() : array 
    {
        return self::$messages;
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