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

namespace SmartCommand\api\task;

use pocketmine\command\CommandSender;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\async\AsyncExecutable;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\task\AsyncCommandTask;

class CommandStatisticsTask extends AsyncCommandTask
{

    /** @var string */
    private $statisticFolder, $commandName;

    /** @var bool */
    private $includeSubCommands = false;

    /** @var string[] */
    private $subCommands = [];

    public function __construct(CommandSender $sender, SmartCommand $commandSearch, AsyncExecutable $command, CommandArguments $args)
    {
        if (!$args->has('sub_commands') || $args->getBool('sub_commands'))
        {
            $this->includeSubCommands = true;
            foreach ($commandSearch->getSubCommands() as $subCommand)
            {
                $this->subCommands[] = $subCommand->getName();
            }
        }
        parent::__construct($sender, $command, $args);
    }

    protected function init(CommandSender $sender, AsyncExecutable $command, CommandArguments $args)
    {
        $this->statisticFolder = SmartCommandAPI::getStatisticFolder();
        $this->commandName = $args->getString('command_name');
    }

    protected function execute(string $commandSender, array $args)
    {
        $statistics = [];
        $checkList = [$this->commandName];
        if ($this->includeSubCommands)
        {
            foreach ($this->subCommands as $subCommandName)
            {
                $checkList[] = $this->commandName . '_' . $subCommandName;
            }
        }
        foreach ($checkList as $name)
        {
            $file = $this->statisticFolder . $name . '.json';
            if (file_exists($file))
            {
                $data = json_decode(
                    file_get_contents($file),
                    true
                );
                $statistics[$name] = $data;
            }
        }
        $this->setResult($statistics);
    }

}