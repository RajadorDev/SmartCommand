<?php

declare (strict_types=1);

/***
 *   
 * Rajador Developer 
 * 
 *  ██████╗  █████╗      ██╗ █████╗ ██████╗  ██████╗ ██████╗ 
 *  ██╔══██╗██╔══██╗     ██║██╔══██╗██╔══██╗██╔═══██╗██╔══██╗
 *  ██████╔╝███████║     ██║███████║██║  ██║██║   ██║██████╔╝
 *  ██╔══██╗██╔══██║██   ██║██╔══██║██║  ██║██║   ██║██╔══██╗
 *  ██║  ██║██║  ██║╚█████╔╝██║  ██║██████╔╝╚██████╔╝██║  ██║
    ╚═╝  ╚═╝╚═╝  ╚═╝ ╚════╝ ╚═╝  ╚═╝╚═════╝  ╚═════╝ ╚═╝  ╚═╝
 * 
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * @copyright 2023 - 2027 Rajador Developer
 * 
 * Repository: https://github.com/rajadordev/SmartCommand
 * 
 * You can use AutoPluginUpdater to update SmartCommand automatically: https://github.com/rajadordev/AutoPluginUpdater
 * 
**/

namespace SmartCommand\command\subcommand;

use pocketmine\command\CommandSender;
use SmartCommand\utils\PrepareCommandException;

trait SubCommandHolderTrait
{

    /** @var array<string,SubCommand> */
    protected $subCommands = [];

    /** @var array<string,SubCommand> */
    protected $aliasesMap = [];

    /** @var integer|null */
    protected $highestSubcommandsArgumentIndex = null;

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
            $subCommandLowercaseName = strtolower($subcommand->getName());
            $this->subCommands[$subCommandLowercaseName] = $subcommand;
            foreach ($subcommand->getAliases() as $aliasName) {
                $this->aliasesMap[strtolower($aliasName)] = $subcommand;
            }
            $this->onRegisterSubCommand($subcommand);
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

    protected function onRegisterSubCommand(SubCommand $subCommand)
    {}

    /**
     * @param string $input
     * @return SubCommand|null
     */
    protected function fetchSubCommand(string $input)
    {
        $inputLowercase = strtolower($input);
        return $this->aliasesMap[$inputLowercase] ?? $this->subCommands[$inputLowercase] ?? null;
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
        if (!empty($this->subCommands))
        {
            if (isset($args[0]))
            {
                $subCommandName = array_shift($args);
                if ($subCommand = $this->fetchSubCommand($subCommandName))
                {
                    $this->executeSubCommand($sender, $subCommand, $label, $subCommandName, array_values($args));
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param CommandSender $sender
     * @param SubCommand $subCommand
     * @return void
     */
    abstract protected function executeSubCommand(CommandSender $sender, SubCommand $subCommand, string $commandLabel, string $subCommandLabel, array $args);

}