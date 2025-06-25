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

use pocketmine\command\CommandSender;
use pocketmine\Server;
use SmartCommand\message\CommandMessages;
use SmartCommand\task\AsyncCommandTask;

trait AsyncExecutableTrait
{

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