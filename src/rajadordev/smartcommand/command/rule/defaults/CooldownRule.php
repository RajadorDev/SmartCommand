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

namespace rajadordev\smartcommand\command\rule\defaults;

use rajadordev\smartcommand\utils\CommandUtils;
use pocketmine\command\CommandSender;
use rajadordev\smartcommand\message\CommandMessages;
use rajadordev\smartcommand\command\rule\CommandSenderRule;
use pocketmine\player\Player;
use rajadordev\smartcommand\command\SmartCommand;
use rajadordev\smartcommand\command\subcommand\SubCommand;

/**
 * I recommend that you register this rule as the last
 */

class CooldownRule implements CommandSenderRule
{

    /** @var array<string,float> */
    protected array $cooldownList = [];

    /** @var bool */
    protected bool $ignoreConsole, $addToCooldownWhenExecuted;

    /** @var float|int */
    private float|int $cooldownTime;

    /**
     * @param integer $ms The cooldown in mileseconds
     * @param boolean $addToCooldownWhenExecuted If true will add to cooldown when the method onRun be executed
     * @param boolean $ignoreConsole If true, the command/subcommand cooldown will be ignored by console
     */
    public function __construct(int $ms, bool $addToCooldownWhenExecuted = true, bool $ignoreConsole = true)
    {
        $this->cooldownTime = $ms / 1000;
        $this->ignoreConsole = $ignoreConsole;
        $this->addToCooldownWhenExecuted = $addToCooldownWhenExecuted;
    }

    public function addToCooldown(CommandSender $sender) : bool
    {
        if (!($sender instanceof Player) && $this->ignoreConsole)
        {
            return false;
        }
        $hash = CommandUtils::hashSender($sender);
        $this->cooldownList[$hash] = microtime(true) + $this->cooldownTime;
        return true;
    }

    public function getCooldownTime(CommandSender $sender) : float
    {
        $hash = CommandUtils::hashSender($sender);
        if (isset($this->cooldownList[$hash]))
        {
            return $this->cooldownList[$hash];
        }
        return 0.0;
    }

    public function inCooldown(CommandSender $sender) : bool 
    {
        return $this->getCooldownTime($sender) > microtime(true);
    }

    public function removeFromCooldown(CommandSender $sender) : void
    {
        unset($this->cooldownList[CommandUtils::hashSender($sender)]);
    }

    public function parse(CommandSender $sender, $command, int $executionType): bool
    {
        if ($executionType === CommandSenderRule::RULE_PRE_EXECUTION)
        {
            if (!$this->inCooldown($sender))
            {
                if (!$this->addToCooldownWhenExecuted)
                {
                    $this->addToCooldown($sender);
                }
                return true;
            }
        } else if ($executionType === CommandSenderRule::RULE_EXECUTION) {
            $this->addToCooldown($sender);
            return true;
        }
        return false;
    }

    public function getMessage(SmartCommand|SubCommand $command, CommandSender $sender): string
    {
        return $command->getMessages()->get(CommandMessages::SENDER_IN_COOLDOWN, '{cooldown}', number_format($this->getCooldownTime($sender) - microtime(true), 2));
    }

    public static function secondsToMs(int $seconds) : int 
    {
        return $seconds * 1000;
    }

    public function getExecutionType(): int
    {
        return $this->addToCooldownWhenExecuted ? self::RULE_BOTH_EXECUTION : self::RULE_PRE_EXECUTION;
    }

}