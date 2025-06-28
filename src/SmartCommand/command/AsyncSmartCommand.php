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
use SmartCommand\command\rule\defaults\WaitUntilCompleteCommandRule;
use SmartCommand\message\CommandMessages;
use SmartCommand\message\DefaultMessages;

abstract class AsyncSmartCommand extends SmartCommand implements AsyncExecutable
{

    use AsyncExecutableTrait;

    /**
     * @param string $name
     * @param string $description
     * @param boolean $waitUntilComplete If true the player will wait the task complete to use the command again
     * @param string $usagePrefix
     * @param string[] $aliases
     * @param CommandMessages|null $messages
     */
    public function __construct(string $name, string $description, bool $waitUntilComplete, string $usagePrefix = self::DEFAULT_USAGE_PREFIX, array $aliases = [], CommandMessages $messages = null)
    {
        parent::__construct($name, $description, $usagePrefix, $aliases, $messages ?? DefaultMessages::ENGLISH());
        if ($waitUntilComplete)
        {
            $this->registerRule($this->waitUntilCompleteRule = new WaitUntilCompleteCommandRule);
        }
    }

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