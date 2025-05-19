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

namespace rajadordev\smartcommand\command;

use pocketmine\command\CommandSender;
use rajadordev\smartcommand\message\CommandMessages;
use rajadordev\smartcommand\command\argument\Argument;
use rajadordev\smartcommand\command\argument\TextArgument;
use rajadordev\smartcommand\utils\PrepareCommandException;

trait ArgumentableTrait 
{

    /** @var array<int,Argument> */
    protected array $arguments = [];

    /** @var array<string,bool> */
    protected array $requiredMap = [];

    /** @var string */
    protected string $argumentsDescription = '';

    /**
     * @param integer $position
     * @param Argument $argument
     * @return self
     * @throws PrepareCommandException
     */
    protected function registerArgument(int $position, Argument $argument)
    {
        if (!isset($this->arguments[$position]))
        {
            if (!is_int($this->getTextArgumentIndex()))
            {
                if (!$this->hasNonRequiredArgument() || !$argument->isRequired())
                {
                    if ($position == 0 || !is_null($this->getArgument($position - 1)))
                    {
                        $this->arguments[$position] = $argument;
                        $this->requiredMap[$argument->getName()] = $argument->isRequired();
                        return $this;
                    } else {
                        throw new PrepareCommandException("Argument $position can't be in this position without a previous argument");
                    }
                    return $this;
                } else {
                    throw new PrepareCommandException('Cannot register a required argument after a not required argument');
                }
            } else {
                throw new PrepareCommandException("Command {$this->getName()} cannot have any argument after " . TextArgument::class);
            }
        }
        throw new PrepareCommandException("$position argument already is registered!");
    }

    /**
     * @param array<int,Argument> $arguments
     * @return self
     * @throws PrepareCommandException
     */
    protected function registerArguments(array $arguments)
    {
        foreach ($arguments as $position => $argument)
        {
            $this->registerArgument($position, $argument);
        }
        return $this;
    }

    /**
     * @return int|null
     */
    protected function getTextArgumentIndex() : ?int 
    {
        foreach ($this->arguments as $index => $argument)
        {
            if ($argument instanceof TextArgument)
            {
                return $index;
            }
        }
        return null;
    }

    /**
     * @return int|null
     */
    public function getArgNeedleIndex() : ?int
    {
        $found = null;
        foreach ($this->arguments as $index => $argument)
        {
            if ($argument->isRequired())
            {
                $found = $index;
                continue;
            } 
            break;
        }
        return $found;
    }

    public function hasNonRequiredArgument() : bool 
    {
        return count(array_filter(
            $this->arguments,
            static function (Argument $argument) : bool {
                return !$argument->isRequired();
            }
        )) > 0;
    }

    public function generateArgumentsList(string $command, CommandMessages $messages, bool $includeDescription = true, bool $raw = false) : string
    {
        $list = [];
        foreach ($this->arguments as $argument)
        {
            $list[] = $argument->getFormat($messages);
        }
        $list = implode(' ', $list);
        if ($list !== '')
        {
            $list = ' ' . $list;
        }
        $format = "/$command{$list}" . (($includeDescription && $this->argumentsDescription != '') ? " $this->argumentsDescription" : '');
        if (!$raw)
        {
            return $messages->get(CommandMessages::USAGE_LINE_FORMAT, '{usage}', $format, false);
        }
        return $format;
    }

    /**
     * @param integer $index
     * @return Argument|null
     */
    public function getArgument(int $index) : ? Argument
    {
        return $this->arguments[$index] ?? null;
    }

    protected function formatArguments(array &$args, CommandSender $sender, CommandMessages $messages, bool $sendErrorMessage = true) : bool 
    {
        $realArgs = $args;
        foreach ($args as $index => $argumentSenderValue)
        {
            if ($argument = $this->getArgument($index))
            {
                if ($argument instanceof TextArgument)
                {
                    $argumentSenderValue = implode(' ', array_slice($realArgs, $index));
                }
                if ($argument->parse($argumentSenderValue))
                {
                    $args[$argument->getName()] = $argumentSenderValue;
                    unset($args[$index]);
                } else {
                    if ($sendErrorMessage)
                    {
                        $message = $argument->getWrongMessage($messages, $argumentSenderValue);
                        $sender->sendMessage($message);
                    }
                    return false;
                }
                continue;
            }
            $realArgs = array_merge($realArgs, array_slice($args, $index));
            break;
        }
        return true;
    }

    
}