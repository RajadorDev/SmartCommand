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

namespace SmartCommand\command;

use SmartCommand\benchmark\AsyncCommandBenchmark;
use SmartCommand\benchmark\SmartCommandBenchmark;
use SmartCommand\command\async\AsyncExecutable;
use SmartCommand\command\async\AsyncExecutableTrait;

abstract class AsyncSmartCommand extends SmartCommand implements AsyncExecutable
{

    use AsyncExecutableTrait;

    public function getCommandLine(): string
    {
        return $this->getName();
    }

    protected function loadExecutionBenchmark(): SmartCommandBenchmark
    {
        return new AsyncCommandBenchmark('AsyncExecution', $this);
    }

    public function getAsyncBenchmark(): AsyncCommandBenchmark
    {
        return $this->getExecutionBenchmark();
    }

}