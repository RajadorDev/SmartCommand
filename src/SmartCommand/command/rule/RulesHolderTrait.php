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

namespace SmartCommand\command\rule;

use pocketmine\command\CommandSender;

trait RulesHolderTrait
{

    /** @var CommandSenderRule[] */
    private $rules = [];

    protected function registerRule(CommandSenderRule $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @param CommandSenderRule ...$rules
     * @return void
     */
    protected function registerRules(CommandSenderRule ...$rules) 
    {
        foreach ($rules as $rule)
        {
            $this->registerRule($rule);
        }
    }

    public function getRules() : array 
    {
        return $this->rules;
    }

    protected function parseRules(CommandSender $sender) : bool 
    {
        foreach ($this->rules as $rule) {
            if (!$rule->parse($sender, $this, CommandSenderRule::RULE_PRE_EXECUTION)) {
                $sender->sendMessage($rule->getMessage($this, $sender));
                return false;
            }
        }
        return true;
    }

}