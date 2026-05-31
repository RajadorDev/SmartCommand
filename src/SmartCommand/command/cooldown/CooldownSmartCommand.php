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

namespace SmartCommand\command\cooldown;

use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\cooldown\rule\CheckCooldownRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;

abstract class CooldownSmartCommand extends SmartCommand implements ExecutableCooldown
{

    use ExecutableCooldownHolderTrait;

    public function __construct(string $name, string $description, string $usagePrefix = self::DEFAULT_USAGE_PREFIX, array $aliases = [], CommandMessages $messages = null)
    {
        $this->registerRule(new CheckCooldownRule($this));
        return parent::__construct($name, $description, $usagePrefix, $aliases, $messages);
    }

    /**
     * @param CommandSender $sender
     * @param string $label
     * @param CommandArguments $args
     * @return CooldownResult
     */
    abstract protected function onAllowedRun(CommandSender $sender, string $label, CommandArguments $args) : CooldownResult;


    final protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        $cooldownResult = $this->onAllowedRun($sender, $label, $args);
        $this->applyCooldownResult($sender, $cooldownResult);
    }

}