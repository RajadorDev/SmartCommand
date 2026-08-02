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

namespace SmartCommand\command\cooldown\subcommand;

use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\cooldown\CooldownResult;
use SmartCommand\command\cooldown\ExecutableCooldown;
use SmartCommand\command\cooldown\ExecutableCooldownHolderTrait;
use SmartCommand\command\cooldown\rule\CheckCooldownRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;

abstract class CooldownBaseSubCommand extends BaseSubCommand implements ExecutableCooldown
{

    use ExecutableCooldownHolderTrait;

    public function __construct(SmartCommand $command, string $name, string $description, array $aliases = [])
    {
        $this->registerRule(new CheckCooldownRule($this));
        return parent::__construct($command, $name, $description, $aliases);
    }

    final protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        $result = $this->onAllowedRun($sender, $commandLabel, $subcommandLabel, $args);
        $this->applyCooldownResult($sender, $result);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string $subcommandLabel
     * @param CommandArguments $args
     * @return CooldownResult
     */
    abstract protected function onAllowedRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args) : CooldownResult;
}