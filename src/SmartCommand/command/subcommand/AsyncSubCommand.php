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
use SmartCommand\command\async\AsyncExecutable;
use SmartCommand\command\async\AsyncExecutableTrait;
use SmartCommand\command\rule\defaults\WaitUntilCompleteCommandRule;
use SmartCommand\command\SmartCommand;

abstract class AsyncSubCommand extends BaseSubCommand implements AsyncExecutable
{

    use AsyncExecutableTrait;

    /**
     * @param SmartCommand $command
     * @param string $name
     * @param string $description
     * @param bool $waitUntilComplete If true the player will wait the task complete to use the command again
     * @param string $aliases
     */
    public function __construct(SmartCommand $command, string $name, string $description, bool $waitUltilComplete, array $aliases = [])
    {
        parent::__construct($command, $name, $description, $aliases);
        if ($waitUltilComplete)
        {
            $this->registerRule($this->waitUntilCompleteRule = new WaitUntilCompleteCommandRule);
        }
    }

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