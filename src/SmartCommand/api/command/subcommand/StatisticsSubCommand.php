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

use pocketmine\Server;
use SmartCommand\utils\CommandUtils;
use pocketmine\command\CommandSender;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\CommandArguments;
use SmartCommand\utils\AdminPermissionTrait;
use SmartCommand\command\argument\BoolArgument;
use SmartCommand\api\task\CommandStatisticsTask;
use SmartCommand\benchmark\SmartCommandBenchmark;
use SmartCommand\command\argument\StringArgument;
use SmartCommand\command\subcommand\AsyncSubCommand;

class StatisticsSubCommand extends AsyncSubCommand
{

    use AdminPermissionTrait;

    protected function prepare()
    {
        $this->registerArgument(0, new StringArgument('command_name', true));
        $this->registerArgument(1, new BoolArgument('sub_commands', false));
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        $command = Server::getInstance()->getCommandMap()->getCommand($commandName = $args->getString('command_name'));
        $prefix = $this->getCommand()->getPrefix();
        if ($command instanceof SmartCommand)
        {
            $sender->sendMessage($prefix . 'Loading statistics...');
            $this->schedule(
                new CommandStatisticsTask($sender, $command, $this, $args)
            );
        } else if (is_null($command)) {
            $sender->sendMessage($prefix . "§cCommand §f$commandName §cdoes not found!");
        } else {
            $sender->sendMessage($prefix . "§cCommand §f$commandName §cis not a §fSmartCommand§c!");
        }
    }

    public function onCompleteTask(CommandSender $sender, CommandArguments $arguments, $result)
    {
        if (!empty($result))
        {
            $lines = [];
            $commandName = $arguments->getString('command_name');
            $getMS = static function (float $value, string $color = null) : string {
                $value *= 1000;
                $format = number_format($value, 2);
                if (is_null($color))
                {
                    return SmartCommandBenchmark::benchmarkColor($value) . $format . 'ms';
                }
                return $color . $format . 'ms';
            };
            $getAverage = static function (array $values, string $color = null) : string {
                $average = array_sum($values) / count($values);
                $format = number_format($average, 2);
                if ($color)
                {
                    return $color . $format . 'ms';
                }
                return SmartCommandBenchmark::benchmarkColor($average) . $format . 'ms';
            };
            /** @var array<string,array{average:float[],last_time:float,violations:int,highest:float,async_task:array{last_time:float,average:float[],highest_time:float,highest_sync_complete:float,average_sync_complete:float[]}}> $result */
            foreach ($result as $commandLine => $statistics)
            {
                $lines[] = CommandUtils::textLinesWithPrefix(
                    [
                        '§f' . $commandLine . '§7:',
                        '  Average time: ' . $getAverage($statistics['average']),
                        '  Highest time: ' . $getMS($statistics['highest']),
                        '  Violations: ' . $statistics['violations'],
                    ]
                );
                if (isset($statistics['async_task']))
                {
                    $lines[] = CommandUtils::textLinesWithPrefix(
                        [
                            '  Async task:',
                            '    Average: ' . $getAverage($statistics['async_task']['average'], '§d'),
                            '    Highest time: ' . $getMS($statistics['async_task']['highest_time'], '§d'),
                            '    Complete sync average: ' . $getAverage($statistics['async_task']['average_sync_complete']),
                            '    Highest sync complete time: ' . $getMS($statistics['async_task']['highest_sync_complete']),
                        ]
                    );
                }
            }
            $lines = implode("\n", $lines);
            $sender->sendMessage("§8----====(§eCommand §f{/$commandName} §eStatus§8====)----\n$lines");
        } else {
            $sender->sendMessage($this->getCommand()->getPrefix() . '§cThere is no data to display about §f' . $arguments->getString('command_name') . '§c!');
        }
    }
}