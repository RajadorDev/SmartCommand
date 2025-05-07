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
    protected function registerRules(...$rules) 
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
        foreach ($this->rules as $rule)
        {
            if (!$rule->parse($sender, $this))
            {
                $sender->sendMessage($rule->getMessage($this, $sender));
                return false;
            }
        }
        return true;
    }

}