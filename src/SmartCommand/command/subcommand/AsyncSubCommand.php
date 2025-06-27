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

namespace SmartCommand\command\subcommand;

use SmartCommand\benchmark\AsyncCommandBenchmark;
use SmartCommand\benchmark\SmartCommandBenchmark;
use SmartCommand\command\AsyncExecutable;
use SmartCommand\command\AsyncExecutableTrait;

abstract class AsyncSubCommand extends BaseSubCommand implements AsyncExecutable
{

    use AsyncExecutableTrait;

    public function getCommandLine(): string
    {
        return $this->getCommand()->getName() . ' ' . $this->getName();
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