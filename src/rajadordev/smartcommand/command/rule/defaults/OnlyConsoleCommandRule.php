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
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * 
**/

namespace rajadordev\smartcommand\command\rule\defaults;

use pocketmine\command\CommandSender;
use rajadordev\smartcommand\message\CommandMessages;
use rajadordev\smartcommand\command\rule\CommandSenderRule;
use pocketmine\player\Player;
use rajadordev\smartcommand\command\SmartCommand;
use rajadordev\smartcommand\command\subcommand\SubCommand;

class OnlyConsoleCommandRule implements CommandSenderRule 
{

    public function parse(CommandSender $sender, $command, int $executionType): bool
    {
        return !($sender instanceof Player);
    }

    public function getMessage(SmartCommand|SubCommand $command, CommandSender $sender): string
    {
        return $command->getMessages()->get(CommandMessages::ONLY_CONSOLE_ALLOWED);
    }

    public function getExecutionType(): int
    {
        return self::RULE_PRE_EXECUTION;
    }
    
}