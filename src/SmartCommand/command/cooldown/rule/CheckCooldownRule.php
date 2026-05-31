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

namespace SmartCommand\command\cooldown\rule;

use pocketmine\command\CommandSender;
use SmartCommand\command\cooldown\ExecutableCooldown;
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\message\CommandMessages;

class CheckCooldownRule implements CommandSenderRule
{

    /** @var ExecutableCooldown */
    protected $command;

    public function __construct(ExecutableCooldown $command)
    {
        $this->command = $command;
    }
    
    
    public function parse(CommandSender $sender, $command, int $executionType): bool
    {
        if ($this->command->getCooldown($sender) !== null) {
            return false;
        }
        return true;
    }

    public function getMessage($command, CommandSender $sender): string
    {
        $cooldownTime = (float) $this->command->getCooldown($sender);
        $cooldownTimeString = number_format(max(0.0, $cooldownTime), 2);
        return $command->getMessages()->get(CommandMessages::SENDER_IN_COOLDOWN, '{cooldown}', $cooldownTimeString);
    }
}