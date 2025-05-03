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

use pocketmine\Player;
use SmartCommand\utils\CommandUtils;
use pocketmine\command\CommandSender;
use SmartCommand\message\CommandMessages;
use SmartCommand\command\rule\CommandSenderRule;

/**
 * I recommend that you register this rule as the last
 */

class CooldownRule implements CommandSenderRule
{

    /** @var array<string,float> */
    protected $cooldownList = [];

    /** @var bool */
    protected $ignoreConsole;

    /** @var float|int */
    private $cooldownTime;

    /**
     * @param integer $ms The cooldown in mileseconds
     * @param boolean $ignoreConsole If true, the command/subcommand cooldown will be ignored by console
     */
    public function __construct(int $ms, bool $ignoreConsole = true)
    {
        $this->cooldownTime = $ms / 1000;
        $this->ignoreConsole = $ignoreConsole;
    }

    public function addToCooldown(CommandSender $sender)
    {
        if (!($sender instanceof Player) && $this->ignoreConsole)
        {
            return false;
        }
        $hash = CommandUtils::hashSender($sender);
        $this->cooldownList[$hash] = microtime(true) + $this->cooldownTime;
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

    public function removeFromCooldown(CommandSender $sender)
    {
        unset($this->cooldownList[CommandUtils::hashSender($sender)]);
    }

    public function parse(CommandSender $sender, $command): bool
    {
        if (!$this->inCooldown($sender))
        {
            $this->addToCooldown($sender);
            return true;
        }
        return false;
    }

    public function getMessage($command, CommandSender $sender): string
    {
        return $command->getMessages()->get(CommandMessages::SENDER_IN_COOLDOWN, '{cooldown}', number_format($this->getCooldownTime($sender) - microtime(true), 2));
    }

    public static function secondsToMs(int $seconds) : int 
    {
        return $seconds * 1000;
    }

}