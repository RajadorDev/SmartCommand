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

namespace SmartCommand\api\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\Utils;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\subcommand\BaseSubCommand;
use SmartCommand\utils\AdminPermissionTrait;
use SmartCommand\utils\CommandUtils;

class InfoSubCommand extends BaseSubCommand
{

    use AdminPermissionTrait;

    protected function prepare()
    {}

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        $info = SmartCommandAPI::getFrameworkDescription();
        $message = CommandUtils::textLinesWithPrefix(
            [
                'SmartCommand Framework info:',
                '',
                'Author: §f' . $info['author'],
                '',
                'Version: §f' . $info['version'],
                '',
                'API: §f' . $info['api'],
                '',
                'Running on OS: §b' . Utils::getOS(),
                '',
                'GitHub: §e' . $info['github'],
                '',
                'Discord: §9' . $info['discord'],
                '' 
            ]
        );
        $message = "§8----====(§e§lSMART§bCOMMAND§r§8)====----\n" . $message;
        $sender->sendMessage($message);
    }

}