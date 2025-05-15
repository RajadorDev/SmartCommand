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

namespace SmartCommand\command\rule\defaults;

use pocketmine\command\CommandSender;
use SmartCommand\message\CommandMessages;
use SmartCommand\command\rule\CommandSenderRule;

class PermissionCommandRule implements CommandSenderRule
{

    public function parse(CommandSender $sender, $command, int $executionType): bool
    {
        return $sender->hasPermission($command->getPermission());
    }

    public function getMessage($command, CommandSender $sender): string
    {
        return $command->getMessages()->get(CommandMessages::NOT_ALLOWED);
    }

    public function getExecutionType(): int
    {
        return self::RULE_PRE_EXECUTION;
    }

}