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
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;

class PermissionCommandRule implements CommandSenderRule
{

    public function parse(CommandSender $sender, $command): bool
    {
        return $sender->hasPermission($command->getPermission());
    }

    public function getMessage($command): string
    {
        return $command->getMessages()->get(CommandMessages::NOT_ALLOWED);
    }

}