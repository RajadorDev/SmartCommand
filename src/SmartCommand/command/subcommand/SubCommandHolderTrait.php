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

use pocketmine\command\CommandSender;
use SmartCommand\utils\PrepareCommandException;

trait SubCommandHolderTrait
{

    /** @var array<string,SubCommand> */
    protected $subCommands = [];

    /**
     * @param SubCommand $subCommand
     * @return self
     * @throws PrepareCommandException
     */
    public function registerSubCommand(SubCommand $subcommand) : self 
    {
        if (count(
            $labels = 
            array_filter(
                array_merge([$subcommand->getName()], $subcommand->getAliases()),
                function (string $name) : bool {
                    return $this->fetchSubCommand($name) instanceof SubCommand;
                }
            )
        ) === 0)
        {
            $this->subCommands[strtolower($subcommand->getName())] = $subcommand;
            return $this;
        }
        throw new PrepareCommandException('Sub-command label ' . implode(', ', $labels) . ' is already registered!');
    }

    /**
     * @param SubCommand[] $subCommands
     * @return self
     */
    public function registerSubCommands(array $subCommands) : self 
    {
        foreach ($subCommands as $subCommand)
        {
            $this->registerSubCommand($subCommand);
        }
        return $this;
    }

    /**
     * @param string $input
     * @return SubCommand|null
     */
    protected function fetchSubCommand(string $input)
    {
        $inputLowercase = strtolower($input);
        if (isset($this->subCommands[$inputLowercase]))
        {
            return $this->subCommands[$inputLowercase];
        }
        foreach ($this->subCommands as $subCommand)
        {
            if ($subCommand->isReference($inputLowercase))
            {
                return $subCommand;
            }
        }
        return null;
    }

    /**
     * @return SubCommand[]
     */
    public function getSubCommands() : array 
    {
        return $this->subCommands;
    }

    /**
     * @param string $commandName
     * @param CommandSender $sender
     * @return array
     */
    public function generateSubCommandsUsages(string $commandName, CommandSender $sender) : array 
    {
        $list = [];
        foreach ($this->subCommands as $subCommand)
        {
            if ($sender->hasPermission($subCommand->getPermission()))
            {
                $list[] = $subCommand->getUsage($commandName);
            }
        }
        return $list;
    }

    /**
     * @param CommandSender $sender
     * @param string $label
     * @param string[] $args
     * @return boolean
     */
    protected function tryExecuteSubCommand(CommandSender $sender, string $label, array $args) : bool
    {
        if (count($this->subCommands) > 0)
        {
            if (isset($args[0]))
            {
                $subCommandName = array_shift($args);
                if ($subCommand = $this->fetchSubCommand($subCommandName))
                {
                    $subCommand->execute($sender, $label, $subCommandName, array_values($args));
                    return true;
                }
            }
        }
        return false;
    }

}