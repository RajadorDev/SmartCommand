<?php

declare (strict_types=1);

/***
 *   
 * Rajador Developer Diamond API
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

namespace SmartCommand\command\rule\defaults;

use pocketmine\Player;
use SmartCommand\utils\CommandUtils;
use pocketmine\command\CommandSender;
use SmartCommand\message\CommandMessages;
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\command\cooldown\CooldownSmartCommand;

/**
 * @deprecated Use @see CooldownSmartCommand
 */
class CooldownRule implements CommandSenderRule
{

    /** @var array<string,float> */
    protected $cooldownList = [];

    /** @var bool */
    protected $ignoreConsole, $addToCooldownWhenExecuted;

    /** @var float|int */
    private $cooldownTime;

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

    public function parse(CommandSender $sender, $command, int $executionType): bool
    {
        return !$this->inCooldown($sender);
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