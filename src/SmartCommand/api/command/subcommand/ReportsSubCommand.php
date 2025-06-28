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
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;
use SmartCommand\utils\AdminPermissionTrait;
use SmartCommand\utils\CommandUtils;

class ReportsSubCommand extends BaseSubCommand
{

    use AdminPermissionTrait;

    protected function prepare()
    {}

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        $reports = [];
        $foundCommand = [];
        foreach (Server::getInstance()->getCommandMap()->getCommands() as $command)
        {
            if ($command instanceof SmartCommand && !in_array($command, $foundCommand))
            {
                $foundCommand[] = $command;
                $benchMark = $command->getExecutionBenchmark();
                if ($benchMark->getViolations() > 0)
                {
                    $reports[] = $benchMark->getCommandFormat() . ':' . $benchMark->getViolationsFormatted() . ' Violations';
                }
                foreach ($command->getSubCommands() as $subCommand)
                {
                    if ($subCommand instanceof BaseSubCommand)
                    {
                        $benchMark = $subCommand->getExecutionBenchmark();
                        if ($benchMark->getViolations() > 0)
                        {
                            $reports[] = '  ' . $benchMark->getCommandFormat() . ': ' . $benchMark->getViolationsFormatted() . 'Violations';
                        }
                    }
                }
            }
        }
        if (count($reports))
        {
            $message = CommandUtils::textLinesWithPrefix($reports, true);
            $message = "§8----====(§e§lSMART§bCOMMAND§r§8)====----\n" . $message;
            $sender->sendMessage($message);
        } else {
            $sender->sendMessage($this->getCommand()->getPrefix() . 'There\'s no violation reports since server uptime');
        }
    }
}