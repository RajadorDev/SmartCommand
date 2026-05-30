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

namespace SmartCommand\command;

use pocketmine\command\CommandSender;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\command\rule\defaults\CooldownRule;
use SmartCommand\Loader;

/**
 * @deprecated  Do not use this trait on your code
 */
trait DeprecatedCooldownRuleTrait 
{

    
    /** @var CooldownRule|null */
    private $__smartCommandOldCooldownRule = null;

    private function trySetCooldownRule(CommandSenderRule $rule)
    {
        if ($rule instanceof CooldownRule) {
            $this->__smartCommandOldCooldownRule = $rule;
        }
    }

    private function addToDeprecatedCooldown(CommandSender $sender)
    {
        if ($this->__smartCommandOldCooldownRule) {
            $this->__smartCommandOldCooldownRule->addToCooldown($sender);
        }
    }
}