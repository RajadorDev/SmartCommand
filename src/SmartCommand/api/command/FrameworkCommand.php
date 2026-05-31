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

namespace SmartCommand\api\command;

use pocketmine\command\CommandSender;
use SmartCommand\api\command\subcommand\InfoSubCommand;
use SmartCommand\api\command\subcommand\ReportsSubCommand;
use SmartCommand\api\command\subcommand\StatisticsSubCommand;
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
                new ReportsSubCommand($this, 'report', 'Report commands with violations since server uptime', ['violations']),
                new StatisticsSubCommand($this, 'statistics', 'See statistcs of some command and his subcommands', true, ['statistic'])
            ]
        );
        $this->setPrefix(Loader::PREFIX);
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        $this->sendUsage($sender, $label);
    }

}