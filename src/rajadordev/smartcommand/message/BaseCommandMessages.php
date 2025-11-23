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

use pocketmine\Server;
use pocketmine\utils\Config;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class BaseCommandMessages implements CommandMessages
{

    /** @var string */
    protected string $prefix;

    /** @var array<string,string> */
    protected array $messages = [];

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

    public function getPrefix() : string 
    {
        return $this->prefix;
    }

    public function get(string $id, array|string|null $replace = null, array|string|null $to = null, bool $usePrefix = true): string
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

    public function send(CommandSender $sender, string $id, array|string|null $replace = null, array|string|null $to = null) : CommandMessages
    {
        $sender->sendMessage($this->get($id, $replace, $to));
        return $this;
    }

    public function copy() : BaseCommandMessages
    {
        return clone $this;
    }

    public function copyMessages() : array 
    {
        return $this->messages;
    }

    public function addMessagesFromFile(string $filePath, int $fileType = Config::JSON) : CommandMessages
    {
        foreach ((new Config($filePath, $fileType))->getAll() as $messageId => $messageContent)
        {
            /** @var string $messageId */
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