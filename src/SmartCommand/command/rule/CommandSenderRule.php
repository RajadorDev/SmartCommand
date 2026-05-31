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
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\SubCommand;

interface CommandSenderRule 
{

    /** 
     * @deprecated 
     * Will be checked before the subcommands/arguments be processed (The first thing to be processed)
     */
    const RULE_PRE_EXECUTION = 0;

    /** 
     * @deprecated 
     * Will be checked before onRun method, after arguments/subcommands be processed (before execute subcommands too) 
     */
    const RULE_EXECUTION = 1;

    /** 
     * @deprecated 
     * Will be executed before and after (RULE_EXECUTION, RULE_PRE_EXECUTION)
     */
    const RULE_BOTH_EXECUTION = 2;

    /**
     * @param CommandSender $sender
     * @param SmartCommand|SubCommand $command
     * @param int $executionType @deprecated It will be removed
     * @return boolean
     */
    public function parse(CommandSender $sender, $command, int $executionType) : bool;

    /**
     * @param SmartCommand|SubCommand $command
     * @return string
     */
    public function getMessage($command, CommandSender $sender) : string;
    
    
}