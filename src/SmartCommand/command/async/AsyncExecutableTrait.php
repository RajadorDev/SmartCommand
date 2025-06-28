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

namespace SmartCommand\command\async;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use SmartCommand\benchmark\AsyncCommandBenchmark;
use SmartCommand\benchmark\SmartCommandBenchmark;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\WaitUntilCompleteCommandRule;
use SmartCommand\message\CommandMessages;
use SmartCommand\task\AsyncCommandTask;

trait AsyncExecutableTrait
{

    /** @var WaitUntilCompleteCommandRule|null */
    private $waitUntilCompleteRule = null;

    protected function loadExecutionBenchmark() : SmartCommandBenchmark
    {
        return new AsyncCommandBenchmark('Execution', $this);
    }

    /**
     * A little way to schedule AsyncCommandTask
     *
     * @param AsyncCommandTask $task
     * @return AsyncCommandTask
     */
    protected function schedule(AsyncCommandTask $task) : AsyncCommandTask
    {
        Server::getInstance()->getScheduler()->scheduleAsyncTask(
            $task
        );
        return $task;
    }

    public function onPrepareTask(AsyncCommandTask $task)
    {
        if ($this->waitUntilCompleteRule)
        {
            $this->waitUntilCompleteRule->setWaiting($task->getSenderUsername(), true);
        }
    }

    public function onFinishTask(AsyncCommandTask $task)
    {
        if ($this->waitUntilCompleteRule)
        {
            $this->waitUntilCompleteRule->setWaiting($task->getSenderUsername(), false);
        }
    }

    /**
     * Called when the task is completed
     *
     * @param CommandSender $sender
     * @param CommandArguments $arguments
     * @param mixed $result The task result given
     * @return void
     */
    abstract public function onCompleteTask(CommandSender $sender, CommandArguments $arguments, $result);

    
    public function onTaskError(CommandSender $sender)
    {
        $sender->sendMessage($this->getMessages()->get(CommandMessages::GENERIC_INTERNAL_ERROR));
    }

    public function onInvalidComplete(string $senderUsername, CommandArguments $args, $result) {}


}