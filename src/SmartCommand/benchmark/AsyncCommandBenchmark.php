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

namespace SmartCommand\benchmark;

use Exception;
use InvalidArgumentException;
use pocketmine\utils\TextFormat;
use SmartCommand\command\AsyncExecutable;
use SmartCommand\utils\CommandUtils;

class AsyncCommandBenchmark extends SmartCommandBenchmark
{

    /** @var array<int,float> */
    private $startedTaskProcess = [];

    /** @var int */
    private $processId = 0;

    /** @var float[] */
    private $averageTaskTimes = [], $averageCompleteTimes = [];

    /** @var float */
    private $lastTaskTime = 0.0, $lastSyncCompleteTime = 0.0;

    /** @var float */
    private $highestTaskTime = 0.0, $highestSyncCompleteTime = 0.0;

    /**
     * Used when these methods will be called: AsyncExecutable::onCompleteTask, AsyncExecutable::onTaskError, AsyncExecutable::onInvalidComplete
     *
     * @var float|null
     */
    private $syncCompleteTask = null;

    /**
     * @param string $name
     * @param AsyncSubCommand|AsyncSmartCommand $asyncCommand
     */
    public function __construct(string $name, AsyncExecutable $asyncCommand)
    {
        parent::__construct($name, $asyncCommand);
    }

    /**
     * @return integer Returns the process id
     */
    public function startTaskProcess() : int 
    {
        $id = $this->processId++;
        $this->startedTaskProcess[$id] = microtime(true);
        return $id;
    }

    public function stopProcess(int $id) 
    {
        if (isset($this->startedTaskProcess[$id]))
        {
            $time = microtime(true) - $this->startedTaskProcess[$id];
            $this->addTaskTime($time);
            unset($this->startedTaskProcess[$id]);
        } else {
            throw new InvalidArgumentException("Theres no process with id $id started");
        }
    }

    public function startSyncCompleteTask()
    {
        if (is_null($this->syncCompleteTask))
        {
            $this->syncCompleteTask = microtime(true);
        } else {
            throw new Exception("Can't start a started complete task process");
        }
    }

    public function stopSyncCompleteTask()
    {
        if (is_float($this->syncCompleteTask))
        {
            $time = microtime(true) - $this->syncCompleteTask;
            $this->addSyncCompleteTime($time);
        } else {
            throw new Exception("Can't stop a stoped complete task process");
        }
    }

    protected function addSyncCompleteTime(float $time)
    {
        $this->lastSyncCompleteTime = $time;
        $this->averageCompleteTimes[] = $time;
        if ($time > $this->highestSyncCompleteTime)
        {
            $this->highestSyncCompleteTime = $time;
        }
        if (count($this->averageCompleteTimes) > self::MAX_AVERAGE)
        {
            array_shift($this->averageCompleteTimes);
        }
    }

    protected function addTaskTime(float $time)
    {
        $this->lastTaskTime = $time;
        if ($time > $this->highestTaskTime)
        {
            $this->highestTaskTime = $time;
        }
        $this->averageTaskTimes[] = $time;
        if (count($this->averageTaskTimes) > self::MAX_AVERAGE)
        {
            array_shift($this->averageTaskTimes);
        }
    }

    public function getLastAsyncTime() : float 
    {
        return $this->lastTaskTime;
    }

    public function getLastAsyncTimeFormatted() : string 
    {
        $time = $this->getLastAsyncTime() * 1000;
        return number_format($time, 2);
    }

    public function getAsyncAverageTime() : float 
    {
        return array_sum($this->averageTaskTimes) / count($this->averageTaskTimes);
    }

    public function getAsyncAverageTimeFormatted() : string 
    {
        return number_format($this->getAsyncAverageTime() * 1000, 2);
    }

    public function getHighestAsyncTime() : float 
    {
        return $this->highestTaskTime;
    }

    public function getHighestAsyncTimeFormatted() : string 
    {
        return number_format($this->highestTaskTime * 1000, 2);
    }

    public function getHighestSyncCompleteTime() : float 
    {
        return $this->syncCompleteTask;
    }

    public function getHighestSyncCompleteTimeFormatted() : string 
    {
        $time = $this->highestSyncCompleteTime * 1000;
        return self::benchmarkColor($time) . number_format($time, 2);   
    }

    public function getLastSyncTimeFormatted() : string 
    {
        $time = $this->lastSyncCompleteTime;
        return self::benchmarkColor($time) . number_format($time, 2);
    }

    public function getLastSyncTime() : float 
    {
        return $this->lastSyncCompleteTime;
    }

    public function getAverageSyncTime() : float
    {
        $time = array_sum($this->averageCompleteTimes) / count($this->averageCompleteTimes);
        return $time;
    }

    public function getAverageSyncTimeFormatted() : string 
    {
        $time = $this->getAverageSyncTime();
        return self::benchmarkColor($time) . number_format($time, 2);
    }


    public function debugFormat(int $startIdentation = 0): string
    {
        $identation = $startIdentation > 0 ? str_repeat(' ', $startIdentation) : '';
        return parent::debugFormat($startIdentation) . "\n" . CommandUtils::textLinesWithPrefix(
            [
                $identation . '  Last task time: §d' . $this->getLastAsyncTimeFormatted(),
                $identation . '  Average task time: §d' . $this->getAverageFormatted(),
                $identation . '  Highest task time: §d' . $this->getHighestAsyncTimeFormatted(),
                $identation . '  Complete sync average: §f' . $this->getAverageSyncTimeFormatted(),
                $identation . '  Last sync complete time: §f' . $this->getLastSyncTimeFormatted(),
                $identation . '  Highest sync complete time: §f' . $this->getHighestSyncCompleteTimeFormatted()
            ]
        );
    }

    public function jsonSerialize()
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'async_task' => [
                    'last_time' => $this->lastTaskTime,
                    'average' => $this->averageTaskTimes,
                    'highest_time' => $this->highestTaskTime,
                    'last_sync_complete_time' => $this->lastSyncCompleteTime,
                    'average_sync_complete' => $this->averageCompleteTimes,
                    'highest_sync_complete' => $this->highestSyncCompleteTime,
                ]
            ]
        );
    }

}