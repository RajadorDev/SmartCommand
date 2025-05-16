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

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;
use SmartCommand\command\subcommand\SubCommand;
use SmartCommand\utils\AdminPermissionTrait;
use SmartCommand\utils\CommandUtils;

class StatusSubCommand extends BaseSubCommand
{

    use AdminPermissionTrait;

    protected function prepare()
    {}

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        /** @var SmartCommand[] */
        $commands = array_filter(
            Server::getInstance()->getCommandMap()->getCommands(),
            static function (Command $command) : bool {
                return $command instanceof SmartCommand;
            }
        );
        $messageFormat = " \n§8----====(§eSmartCommand §aStatus List§8)====----\n§8-";
        $subCommandsCount = 0;
        $commandsProcessed = [];
        foreach ($commands as $command)
        {
            if (in_array($command, $commandsProcessed))
            {
                continue;
            }
            $messageFormat .= "\n" . $command->getExecutionBenchmark()->debugFormat() ."\n§8-    §7Uses: §f{$command->getExecutionBenchmark()->getBenchmarkTimes()}";
            $subCommands = array_filter(
                $command->getSubCommands(),
                static function (SubCommand $subCommand) : bool {
                    return $subCommand instanceof BaseSubCommand;
                }
            );
            if (count($subCommands) > 0)
            {
                $messageFormat .= "\n§8-    §bSubcommands: §f";
            }
            /** @var BaseSubCommand[] $subCommands */
            foreach ($subCommands as $subCommand)
            {
                $subCommandsCount++;
                $messageFormat .= "\n" . $subCommand->getExecutionBenchmark()->debugFormat(2) . "\n§8-      §7Uses: §f" . $subCommand->getExecutionBenchmark()->getBenchmarkTimes();
            }
            $commandsProcessed[] = $command;
        }
        $commandsCount = count($commands);
        $messageFormat .= "\n" . CommandUtils::textLinesWithPrefix(
            [
                '',
                'Total commands: §f' . $commandsCount,
                '',
                'Total subcommands: §f' . $subCommandsCount,
                ''
            ]
        );
        $sender->sendMessage($messageFormat);
    }


}