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

use pocketmine\Player;
use pocketmine\command\CommandSender;
use SmartCommand\benchmark\AsyncCommandBenchmark;
use SmartCommand\command\CommandArguments;

interface AsyncExecutable 
{

    /**
     * @return string
     */
    public function getCommandLine() : string;

    /**
     * Called when the task is completed
     *
     * @param CommandSender|Player $sender
     * @param CommandArguments $arguments
     * @param mixed $result The task result given
     * @return void
     */
    public function onCompleteTask(CommandSender $sender, CommandArguments $arguments, $result);

    /**
     * Called when some error happens inside execute function block @see SmartCommand\task\AsyncCommandTask
     *
     * @param CommandSender|Player $sender
     * @return void
     */
    public function onTaskError(CommandSender $sender);

    /**
     * Called when the player leave the game while task is running
     * 
     * @param string $username
     * @param CommandArguments $args
     * @param mixed $result
     * @return void
     */
    public function onInvalidComplete(string $username, CommandArguments $args, $result);

    /**
     * @return AsyncCommandBenchmark
     */
    public function getAsyncBenchmark() : AsyncCommandBenchmark;

}