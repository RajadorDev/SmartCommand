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

namespace SmartCommand\command;

use pocketmine\command\CommandSender;
use SmartCommand\command\argument\Argument;
use SmartCommand\command\argument\TextArgument;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\PrepareCommandException;

/**
 * @phpstan-type ArgumentsFormatted array<int|string,mixed>
 */
trait ArgumentableTrait 
{

    /** @var array<int,Argument> */
    protected $arguments = [];

    /** @var array<string,bool> */
    protected $requiredMap = [];

    /** @var string */
    protected $argumentsDescription = '';

    /** @var integer|null */
    protected $textArgumentIndex = null;

    /** @var integer|null */
    protected $argumentNeedleIndex = null;

    /** @var string|null */
    protected $cachedGeneratedArgumentsList = null;

    /** @var integer|null */
    protected $lastArgumentPosition = null;

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
                        $this->onArgumentRegistered($position, $argument);
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

    protected function onArgumentRegistered(int $indexPosition, Argument $argument) 
    {
        if ($argument instanceof TextArgument) {
            $this->textArgumentIndex = $indexPosition;
        }
        $this->updateArgumentsNeedleIndex();
        $this->cachedGeneratedArgumentsList = null;
        $this->lastArgumentPosition = $indexPosition;
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
    protected function getTextArgumentIndex() 
    {
        return $this->textArgumentIndex;
    }

    protected function updateArgumentsNeedleIndex()
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
        $this->argumentNeedleIndex = $found;
    }

    /**
     * @return int|null
     */
    public function getArgNeedleIndex()
    {
        return $this->argumentNeedleIndex;
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
        if ($this->cachedGeneratedArgumentsList === null) {
            $list = '';
            if (!empty($this->arguments)) {
                foreach ($this->arguments as $argument) {
                    $list .= ' ' . $argument->getFormat($messages);
                }
            }
            $this->cachedGeneratedArgumentsList = $list;
        } else {
            $list = $this->cachedGeneratedArgumentsList;
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
    public function getArgument(int $index)
    {
        return $this->arguments[$index] ?? null;
    }


    /**
     * @param array<int,string> $args
     * @param CommandSender $sender
     * @param CommandMessages $messages
     * @param boolean $sendErrorMessage
     * @return boolean
     * @param-out ArgumentsFormatted $args
     */
    protected function formatArguments(array &$args, CommandSender $sender, CommandMessages $messages, bool $sendErrorMessage = true) : bool 
    {
        /** @var ArgumentsFormatted */
        $argumentsResult = $args;
        $textArgumentIndex = $this->getTextArgumentIndex();
        foreach ($args as $argumentIndex => $argumentGiven) {
            if ($argument = $this->getArgument($argumentIndex)) {


                if ($argumentIndex === $textArgumentIndex) {
                    $argumentGiven = implode(' ', array_slice($args, $argumentIndex));
                }

                $argumentResult = $argumentGiven;
                if (!$argument->parse($argumentResult)) {
                    if ($sendErrorMessage) {
                        $sender->sendMessage($argument->getWrongMessage($messages, $argumentGiven));
                    }
                    return false;
                }


                unset($argumentsResult[$argumentIndex]);
                $argumentsResult[$argument->getName()] = $argumentResult;
                
                if ($argumentIndex === $textArgumentIndex) {
                    break;
                }
            }

            break;
        }

        $args = $argumentsResult;
        return true;
    }
    
}