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

namespace SmartCommand\message;

use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class BaseCommandMessages implements CommandMessages
{

    /** @var string */
    protected $prefix;

    /** @var array<string,string> */
    protected $messages = [];

    /**
     * @param array<string,string|array<string,string>> $messages
     */
    public function __construct(array $messages, string $prefix)
    {
        foreach ($messages as $messageId => $messageValue)
        {
            if (is_array($messageValue))
            {
                foreach ($messageValue as $subMessageId => $messageValue)
                {
                    $this->messages[$messageId . '.' . $subMessageId] = $messageValue;
                }
                continue;
            }
            $this->messages[$messageId] = $messageValue;
        }
        $this->setPrefix($prefix);
    }

    public function exists(string $id): bool
    {
        return isset($this->messages[$id]);
    }

    public function setPrefix(string $text) : BaseCommandMessages
    {
        $this->prefix = $text;
        return $this;
    }

    public function get(string $id, $replace = null, $to = null, bool $usePrefix = true): string
    {
        if (isset($this->messages[$id]))
        {
            $message = $this->messages[$id];
            $canReplace = (is_array($replace) || is_string($replace));
            $canReplaceTo = (is_array($to) || is_string($to));
            if ($canReplace === $canReplaceTo)
            {
                $message = $canReplace ? str_replace($replace, $to, $message) : $message;
            } else {
                throw new InvalidArgumentException("replace and to argument must be passed together");
            }
        } else {
            Server::getInstance()->getLogger()->error(($this instanceof Command ? $this->getName() : '') . " Message with id $id not found!");
            $message = TextFormat::RED . 'Message not found!';
        }
        return $this->prefix . $message;
    }

    public function set(string $id, string $text) : CommandMessages
    {
        $this->messages[$id] = $text;
        return $this;
    }

    public function add(array $messages): CommandMessages
    {
        foreach ($messages as $id => $text)
        {
            $this->set($id, $text);
        }
        return $this;
    }

    public function send(CommandSender $sender, string $id, $replace = null, $to = null) : CommandMessages
    {
        $sender->sendMessage($this->get($id, $replace, $to));
        return $this;
    }

    public function copy() : BaseCommandMessages
    {
        return clone $this;
    }

    public function addMessagesFromFile(string $filePath, int $fileType = Config::JSON) : CommandMessages
    {
        foreach ((new Config($filePath, $fileType))->getAll() as $messageId => $messageContent)
        {
            $this->set($messageId, $messageContent);
        }
        return $this;
    }

    /**
     * @param string $fileName
     * @param string $prefix
     * @param int $fileType
     * @return BaseCommandMessages
     */
    public static function messagesFromFile(string $fileName, string $prefix, int $fileType = Config::JSON) : BaseCommandMessages
    {
        return new BaseCommandMessages((new Config($fileName, $fileType))->getAll(), $prefix);
    }

}