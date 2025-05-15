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
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\SubCommand;

interface CommandSenderRule 
{

    /** Will be checked before the subcommands/arguments be processed (The first thing to be processed) */
    const RULE_PRE_EXECUTION = 0;

    /** Will be checked before onRun method, after arguments/subcommands be processed (before execute subcommands too) */
    const RULE_EXECUTION = 1;

    /** Will be executed before and after (RULE_EXECUTION, RULE_PRE_EXECUTION) */
    const RULE_BOTH_EXECUTION = 2;

    /**
     * @param CommandSender $sender
     * @param SmartCommand|SubCommand $command
     * @param int $executionType
     * @return boolean
     */
    public function parse(CommandSender $sender, $command, int $executionType) : bool;

    /**
     * @param SmartCommand|SubCommand $command
     * @return string
     */
    public function getMessage($command, CommandSender $sender) : string;

    /**
     * @return int
     */
    public function getExecutionType() : int;
    
    
}