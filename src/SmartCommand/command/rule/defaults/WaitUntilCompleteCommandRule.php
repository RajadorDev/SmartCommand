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

namespace SmartCommand\command\rule\defaults;

use pocketmine\command\CommandSender;
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\CommandUtils;

/**
 * This class can be used as queue rule, actualy used by @see SmartCommand\command\async\AsyncExecutableTrait
 * NOTE: Do not use this rule without adding and removing the player manually, only in AsyncSmartCommand and AsyncSubCommand it works automatically with the constructor parameter waitUntilComplete
 */
class WaitUntilCompleteCommandRule implements CommandSenderRule
{

    /** @var string[] */
    private $waiting = [];

    public function parse(CommandSender $sender, $command, int $executionType): bool
    {
        return !$this->isWaiting($sender);
    }

    public function getMessage($command, CommandSender $sender): string
    {
        return $command->getMessages()->get(CommandMessages::ACTION_IN_PROCESS);
    }

    public function getExecutionType(): int
    {
        return self::RULE_PRE_EXECUTION;
    }

    public function isWaiting(CommandSender $sender) : bool 
    {
        return in_array(CommandUtils::hashSender($sender), $this->waiting);
    }

    /**
     * @param string|CommandSender $sender
     * @param boolean $set
     * @return boolean
     */
    public function setWaiting($sender, bool $set) : bool 
    {
        $hash = is_string($sender) ? $sender : CommandUtils::hashSender($sender);
        if ($set)
        {
            if (!$this->isWaiting($sender))
            {
                $this->waiting[] = $hash;
                return true;
            }
        } else if ($this->isWaiting($sender)) {
            unset($this->waiting[array_search($hash, $this->waiting)]);
            return true;
        }
        return false;
    }

}