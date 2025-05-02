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
use pocketmine\Player;
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;

class OnlyConsoleCommandRule implements CommandSenderRule 
{

    public function parse(CommandSender $sender, $command): bool
    {
        return !($sender instanceof Player);
    }

    public function getMessage($command): string
    {
        return $command->getMessages()->get(CommandMessages::ONLY_CONSOLE_ALLOWED);
    }
    
}