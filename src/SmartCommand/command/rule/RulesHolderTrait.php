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

    /** @var array<int,CommandSenderRule[]> */
    private $rules = [];

    protected function registerRule(CommandSenderRule $rule)
    {
        $executionType = $rule->getExecutionType();
        if ($executionType === CommandSenderRule::RULE_BOTH_EXECUTION)
        {
            foreach ([CommandSenderRule::RULE_EXECUTION, CommandSenderRule::RULE_PRE_EXECUTION] as $type)
            {
                if (!isset($this->rules[$type]))
                {
                    $this->rules[$type] = [];
                }
                $this->rules[$type][] = $rule;
            }
        } else if (isset($this->rules[$executionType])) {
            $this->rules[$executionType][] = $rule;
        } else {
            $this->rules[$executionType] = [$rule];
        }
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

    protected function parseRules(CommandSender $sender, int $executionType) : bool 
    {
        if (isset($this->rules[$executionType]))
        {
            foreach ($this->rules[$executionType] as $rule)
            {
                if (!$rule->parse($sender, $this, $executionType))
                {
                    $sender->sendMessage($rule->getMessage($this, $sender));
                    return false;
                }
            }
        }
        return true;
    }

}