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

namespace SmartCommand\api\task;

use pocketmine\command\CommandSender;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\async\AsyncExecutable;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\task\AsyncCommandTask;

class CommandStatisticsTask extends AsyncCommandTask
{

    /** @var string */
    private $statisticFolder, $commandName;

    /** @var bool */
    private $includeSubCommands = false;

    /** @var string[] */
    private $subCommands = [];

    public function __construct(CommandSender $sender, SmartCommand $commandSearch, AsyncExecutable $command, CommandArguments $args)
    {
        if (!$args->has('sub_commands') || $args->getBool('sub_commands'))
        {
            $this->includeSubCommands = true;
            foreach ($commandSearch->getSubCommands() as $subCommand)
            {
                $this->subCommands[] = $subCommand->getName();
            }
        }
        parent::__construct($sender, $command, $args);
    }

    protected function init(CommandSender $sender, AsyncExecutable $command, CommandArguments $args)
    {
        $this->statisticFolder = SmartCommandAPI::getStatisticFolder();
        $this->commandName = $args->getString('command_name');
    }

    protected function execute(string $commandSender, array $args)
    {
        $statistics = [];
        $checkList = [$this->commandName];
        if ($this->includeSubCommands)
        {
            foreach ($this->subCommands as $subCommandName)
            {
                $checkList[] = $this->commandName . '_' . $subCommandName;
            }
        }
        foreach ($checkList as $name)
        {
            $file = $this->statisticFolder . $name . '.json';
            if (file_exists($file))
            {
                /** @var array{average:float[],last_time:float,violations:int,highest:float,times:int}[] */
                $data = json_decode(
                    file_get_contents($file),
                    true
                );
                $dataValue = ['average' => [], 'violations' => 0, 'times' => 0];
                $currentHighest = 0.0;
                $currentHighestAsync = 0.0;
                $currentHighestSync = 0.0;
                $hasAsync = false;
                $averageAsync = [];
                $averageSync = [];
                foreach ($data as $statisticInfo)
                {
                    $dataValue['average'] = array_merge($dataValue['average'], $statisticInfo['average']);
                    $highest = $statisticInfo['highest'];
                    if ($highest > $currentHighest)
                    {
                        $currentHighest = $highest;
                    }
                    $dataValue['violations'] += $statisticInfo['violations'];
                    $dataValue['times'] += $statisticInfo['times'];
                    if (isset($statisticInfo['async_task']))
                    {
                        $hasAsync = true;
                        /** @var array{average:float[],highest_time:float,average_sync_complete:float[],highest_sync_complete:float} $asyncStatistic */
                        $asyncStatistic = $dataValue['async_task'];
                        $highestAsync = $asyncStatistic['highest_time'];
                        if ($highestAsync > $currentHighestAsync)
                        {
                            $currentHighestAsync = $highestAsync;
                        }
                        $highestSync = $asyncStatistic['highest_sync_complete'];
                        if ($highestSync > $currentHighestSync)
                        {
                            $currentHighestSync = $highestSync;
                        }
                        $averageSync = array_merge($averageSync, $asyncStatistic['average_sync_complete']);
                        $averageAsync = array_merge($averageAsync, $asyncStatistic['average']);
                    }
                }
                if ($hasAsync)
                {
                    $dataValue['async_task'] = [
                        'average' => $averageAsync,
                        'average_sync_complete' => $averageSync,
                        'highest_time' => $highestAsync,
                        'highest_sync_complete' => $highestSync
                    ];
                }
                $dataValue['highest_time'] = $currentHighest;
                $statistics[$name] = $dataValue;
            }
        }
        $this->setResult($statistics);
    }

}