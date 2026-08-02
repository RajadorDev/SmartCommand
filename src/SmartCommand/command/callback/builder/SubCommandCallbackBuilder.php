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
 * This system is protected by laws! Anyone who shares or resells it will be held accountable
 *
 * Edição, compartilhamento ou revenda é proibido por LEI! Quem fizer será responsabilizado judicialmente
 * 
**/

namespace SmartCommand\command\callback\builder;

use pocketmine\command\CommandSender;
use SmartCommand\command\builder\BuildErrorList;
use SmartCommand\command\callback\subcommand\CallbackCooldownSubCommand;
use SmartCommand\command\callback\subcommand\CallbackSubCommand;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\cooldown\CooldownResult;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\SubCommand;

class SubCommandCallbackBuilder extends ExecutableCallbackBuilder
{

    /** @var boolean */
    protected $useCooldown = false;

    /** @var SmartCommand|null */
    protected $command = null;

    public function useCooldown(bool $setUseCooldown = true) : SubCommandCallbackBuilder
    {
        $this->useCooldown = $setUseCooldown;
        return $this;
    }

    public function checkBuildErrors(): BuildErrorList
    {
        $errors = parent::checkBuildErrors();

        if ($this->closure) {
            $closureCheckClass = $this->useCooldown ? CallbackCooldownSubCommand::class : CallbackSubCommand::class;

            if (!$closureCheckClass::validateClosure($this->closure)) {
                $errors->push("Invalid closure structure for " . $closureCheckClass . ' class');
            }
        }
        return $errors;
    }

    /**
     * @param SmartCommand $command
     * @return $this
     */
    public function setCommand(SmartCommand $command) : SubCommandCallbackBuilder
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @param SmartCommand $command
     * @return CallbackSubCommand|CallbackCooldownSubCommand
     */
    public function build(SmartCommand $command = null)
    {
        if ($command) {
            $this->setCommand($command);
        }
        return parent::build();
    }

    public function buildAndRegisterInCommand(SmartCommand $command) : SubCommand
    {
        $build = $this->build($command);
        $command->registerSubCommand($build);
        return $build;
    }

    /**
     * @return CallbackSubCommand|CallbackCooldownSubCommand
     */
    protected function create()
    {
        if ($this->useCooldown) {
            $class = CallbackCooldownSubCommand::class;
        } else {
            $class = CallbackSubCommand::class;
        }
        return new $class(
            $this->command,
            $this->commandName,
            $this->commandDescription,
            $this->permission,
            $this->closure,
            $this->aliases,
            $this->getRules(),
            $this->arguments
        );
    }

    /**
     * @param callable(CommandSender $sender, string $commandLabel, string $subCommandLabel, CommandArguments $args)|callable(CommandSender $sender, string $commandLabel, string $subCommandLabel, CommandArguments $args):CooldownResult $closure
     * @return ExecutableCallbackBuilder
     */
    public function listen(callable $closure): ExecutableCallbackBuilder
    {
        return parent::listen($closure);
    }

    protected function putAditionalParams($command)
    {}
}