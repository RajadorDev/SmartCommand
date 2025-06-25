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
use SmartCommand\command\argument\StringArgument;
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
    {
        $this->registerArgument(0, new StringArgument('command_name', false));
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        if ($args->has('command_name'))
        {
            if ($commandFound = Server::getInstance()->getCommandMap()->getCommand(strtolower(ltrim($commandName = $args->getString('command_name'), '/'))))
            {
                if ($commandFound instanceof SmartCommand)
                {
                    $commands = [$commandFound];
                } else {
                    $sender->sendMessage($this->getCommand()->getPrefix() . "§cCommand §f/{$commandFound->getName()} §7is not a §fSmartCommand§c!");
                    return;
                }
            } else {
                $sender->sendMessage($this->getCommand()->getPrefix() . "§cCommand §f{$commandName} §cdoes not found!");
                return;
            }
        } else {
            $commands = Server::getInstance()->getCommandMap()->getCommands();
        }
        $messageFormat = " \n§8----====(§eSmartCommand §aStatus List§8)====----\n§8-";
        $subCommandsCount = 0;
        $commandsCount = 0;
        $commandsProcessed = [];
        foreach ($commands as $command)
        {
            if (!($command instanceof SmartCommand) || in_array($command, $commandsProcessed))
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
            $commandsCount++;
        }
        if ($args->has('command_name'))
        {
            $messageFormatLines = [
                '',
                'Total subcommands: §f' . $subCommandsCount,
                ''
            ];
        } else {
            $messageFormatLines = [
                '',
                'Total commands: §f' . $commandsCount,
                '',
                'Total subcommands: §f' . $subCommandsCount,
                ''
            ];
        }
        $messageFormat .= "\n" . CommandUtils::textLinesWithPrefix(
            $messageFormatLines
        );
        $sender->sendMessage($messageFormat);
    }


}