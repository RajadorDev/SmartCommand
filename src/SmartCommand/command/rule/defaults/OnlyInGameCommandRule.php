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

use pocketmine\Player;
use pocketmine\command\CommandSender;
use SmartCommand\message\CommandMessages;
use SmartCommand\command\rule\CommandSenderRule;

class OnlyInGameCommandRule implements CommandSenderRule
{

    public function parse(CommandSender $sender, $command): bool
    {
        return $sender instanceof Player;
    }

    public function getMessage($command, CommandSender $sender): string
    {
        return $command->getMessages()->get(CommandMessages::ONLY_PLAYER_ALLOWED);
    }
    
}