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
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * 
**/

namespace rajadordev\smartcommand\benchmark;

use Exception;
use pocketmine\utils\TextFormat;
use rajadordev\smartcommand\command\SmartCommand;
use rajadordev\smartcommand\command\subcommand\SubCommand;
use rajadordev\smartcommand\utils\CommandUtils;
use Stringable;

class SmartCommandBenchmark implements Stringable
{

    const MAX_AVERAGE = 50;

    /** @var SubCommand|Smartcommand */
    private SmartCommand|SubCommand $command;

    /** @var string */
    private string $name;

    /** @var float[] */
    private array $average = [];

    /** @var float */
    private float $highestTime = 0.0, $lastTime = 0.0;

    /** @var float|null */
    private ?float $started = null;

    /** @var int */
    private int $benchmarkTimes = 0;

    public function __construct(string $name, SmartCommand|SubCommand $command)
    {
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

    public function getCommand() : SmartCommand|SubCommand
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

    protected function addTime(float $time) : void
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

    public function clearAverage() : void 
    {
        $this->average = [];
    }

    public function start() : void
    {
        $now = microtime(true);
        $this->started = $now;
    }

    public function stop() : void
    {
        $now = microtime(true);
        if (!is_null($this->started))
        {
            $time = $now - $this->started;
            $time *= 1000;
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

    public function debugFormat(int $startIdentation = 0) : string
    {
        $identation = $startIdentation > 0 ? str_repeat(' ', $startIdentation) : '';
        return CommandUtils::textLinesWithPrefix(
            [
                "{$identation}{$this->name} {$this->getCommandFormat()}",
                $identation . '  §7Highest time: §f' . $this->getHighestTimeFormatted() . 'ms',
                $identation . '  §7Average time: §f' . $this->getAverageFormatted() . 'ms',
                $identation . '  §7Last time: §f' . $this->getLastTimeFormatted() . 'ms',
                $identation . '  §7Average count: §f' . count($this->average) . ' times'
            ]
        );
    }

    public function __toString(): string
    {
        return $this->debugFormat();
    }


}