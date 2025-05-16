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
use pocketmine\utils\TextFormat;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\SubCommand;
use SmartCommand\utils\CommandUtils;

class SmartCommandBenchmark
{

    const MAX_AVERAGE = 50;

    /** @var SubCommand|Smartcommand */
    private $command;

    /** @var string */
    private $name;

    /** @var float[] */
    private $average = [];

    /** @var float */
    private $highestTime = 0.0, $lastTime = 0.0;

    /** @var float|null */
    private $started = null;

    /** @var int */
    private $benchmarkTimes = 0;

    public function __construct(string $name, $command)
    {
        assert($command instanceof SmartCommand || $command instanceof SubCommand);
        $this->command = $command;
        $this->name = $name;
    }

    /**
     * @param float $value
     * @param array{0:float,1:string}[] $pallet
     * @return void
     */
    public static function benchmarkColor(float $value, array $pallet = [
            [0.5, TextFormat::GREEN],
            [1.0, TextFormat::YELLOW],
            [1.5, TextFormat::GOLD],
            [10.0, TextFormat::RED]
        ], string $default = TextFormat::DARK_RED) : string {
        foreach ($pallet as $values)
        {
            $numberMax = $values[0];
            if ($value < $numberMax)
            {
                $color = $values[1];
                return $color;
            }
        }
        return $default;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getCommand() 
    {
        return $this->command;
    }

    public function getCommandFormat() : string 
    {
        $command = $this->command;
        if ($command instanceof SubCommand)
        {
            $command = '/' . $command->getCommand()->getName() . ' ' . $command->getName();
        } else {
            $command = '/' . $command->getName();
        }
        return $command;
    }

    public function getAverage() : float 
    {
        $averageCount = count($this->average);
        if ($averageCount > 0)
        {
            return array_sum($this->average) / $averageCount;
        }
        return 0.0;
    }

    public function getAverageFormatted() : string 
    {
        $average = $this->getAverage();
        return self::benchmarkColor($average) . number_format($average, 2);
    }

    public function getLastTime() : float 
    {
        return $this->lastTime;
    }

    public function getLastTimeFormatted() : string 
    {
        $lastTime = $this->getLastTime();
        return self::benchmarkColor($lastTime) . number_format($lastTime, 2);
    }

    public function getHighestTime() : float 
    {
        return $this->highestTime;
    }

    public function getHighestTimeFormatted() : string 
    {
        $time = $this->getHighestTime();
        return self::benchmarkColor($time) . number_format($time, 2);
    }

    protected function addTime(float $time)
    {
        $this->lastTime = $time;
        if ($time > $this->highestTime)
        {
            $this->highestTime = $time;
        }
        if (count($this->average) >= self::MAX_AVERAGE)
        {
            array_shift($this->average);
        }
        $this->average[] = $time;
    }

    public function clearAverage() 
    {
        $this->average = [];
    }

    public function start()
    {
        $now = microtime(true);
        $this->started = $now;
    }

    public function stop()
    {
        $now = microtime(true);
        if (!is_null($this->started))
        {
            $time = $now - $this->started;
            $this->addTime($time);
            $this->started = null;
            $this->benchmarkTimes++;
        } else {
            throw new Exception("Can't stop a stopped time in {$this->name} Benchmark");
        }
    }

    public function isStopped() : bool 
    {
        return is_null($this->started);
    }

    public function getBenchmarkTimes() : int 
    {
        return $this->benchmarkTimes;
    }

    public function debugFormat(int $startIdentation = 0)
    {
        $identation = $startIdentation > 0 ? str_repeat(' ', $startIdentation) : '';
        return CommandUtils::textLinesWithPrefix(
            [
                "{$identation}{$this->name} {$this->getCommandFormat()}",
                $identation . '  §7Highest time: §f' . $this->getHighestTimeFormatted() . 's',
                $identation . '  §7Average time: §f' . $this->getAverageFormatted() . 's',
                $identation . '  §7Last time: §f' . $this->getLastTimeFormatted() . 's',
                $identation . '  §7Average count: §f' . count($this->average) . ' times'
            ]
        );
    }


}