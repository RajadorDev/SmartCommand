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

namespace SmartCommand\api\command;

use pocketmine\command\CommandSender;
use SmartCommand\api\command\subcommand\InfoSubCommand;
use SmartCommand\api\command\subcommand\ReportsSubCommand;
use SmartCommand\api\command\subcommand\StatusSubCommand;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\Loader;
use SmartCommand\utils\AdminPermissionTrait;

class FrameworkCommand extends SmartCommand
{

    use AdminPermissionTrait;

    protected function prepare()
    {
        $this->registerSubCommands(
            [
                new InfoSubCommand($this, 'info', 'See framework info', ['about', 'version', '?']),
                new StatusSubCommand($this, 'status', 'Show status of subcommands and execution benchmark'),
                new ReportsSubCommand($this, 'report', 'Report commands with violations since server uptime', ['violations'])
            ]
        );
        $this->setPrefix(Loader::PREFIX);
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        $this->sendUsage($sender, $label);
    }

}