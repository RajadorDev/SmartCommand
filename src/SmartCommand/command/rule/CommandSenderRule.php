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

    /**
     * @param CommandSender $sender
     * @param SmartCommand|SubCommand $command
     * @return boolean
     */
    public function parse(CommandSender $sender, $command) : bool;

    /**
     * @param SmartCommand|SubCommand $command
     * @return string
     */
    public function getMessage($command, CommandSender $sender) : string;
    
}