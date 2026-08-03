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

namespace SmartCommand\command\callback\builder;

use pocketmine\command\CommandSender;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\builder\BuildErrorList;
use SmartCommand\command\callback\CallbackCooldownSmartCommand;
use SmartCommand\command\callback\CallbackSmartCommand;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\subcommand\SubCommand;
use SmartCommand\command\subcommand\SubCommandHolderTrait;

class CommandCallbackBuilder extends ExecutableCallbackBuilder
{

    /** @var SubCommandCallbackBuilder[] */
    protected $subCommandsBuilds = [];

    /** @var boolean */
    protected $useCooldown = false;

    public function useCooldown(bool $setUseCooldown = true) : CommandCallbackBuilder
    {
        $this->useCooldown = $setUseCooldown;
        return $this;
    }

    public function checkBuildErrors(): BuildErrorList
    {
        $errors = parent::checkBuildErrors();

        if ($this->closure) {
            $closureCheckClass = $this->useCooldown ? CallbackCooldownSmartCommand::class : CallbackSmartCommand::class;

            if (!$closureCheckClass::validateClosure($this->closure)) {
                $errors->push("Invalid closure structure for " . $closureCheckClass . ' class');
            }
        }
        return $errors;
    }

    public function createSubCommand(string $name, string $description, string $permission) : SubCommandCallbackBuilder
    {
        return $this->subCommandsBuilds[] = (
            new SubCommandCallbackBuilder($name, $description)
        )->permission($permission);
    }

    /**
     * @return CallbackCooldownSmartCommand|CallbackSmartCommand
     */
    public function build()
    {
        $build = parent::build();
        foreach ($this->subCommandsBuilds as $subCommands) {
            $subCommands->buildAndRegisterInCommand($build);
        }
        return $build;
    }

    /**
     * @return CallbackCooldownSmartCommand|CallbackSmartCommand
     * @throws CallbackBuildFailException
     */
    public function buildAndRegister(string $pocketMineCommandMapPrefix) {
        $build = $this->build();
        SmartCommandAPI::register($pocketMineCommandMapPrefix, $build);
        return $build;
    }

    /**
     * @return CallbackCooldownSmartCommand|CallbackSmartCommand
     */
    protected function create()
    {
        if ($this->useCooldown) {
            return new CallbackCooldownSmartCommand(
                $this->commandName,
                $this->commandDescription,
                $this->permission,
                $this->closure,
                $this->usagePrefix,
                $this->aliases,
                $this->messages,
                $this->getRules(),
                $this->arguments
            );
        }
        return new CallbackSmartCommand(
            $this->commandName,
            $this->commandDescription,
            $this->closure,
            $this->permission,
            $this->usagePrefix,
            $this->aliases,
            $this->messages,
            $this->getRules(),
            $this->arguments
        );
    }

    /**
     * @param callable(CommandSender $sender,string $label,CommandArguments $args) $closure
     * @return $this
     */
    public function listen(callable $closure): ExecutableCallbackBuilder
    {
        return parent::listen($closure);
    }

    protected function putAditionalParams($command)
    {}

    /**
     * Ignore it, it's from SubCommandHolderTrait
     *
     * @param CommandSender $sender
     * @param SubCommand $subCommand
     * @param string $commandLabel
     * @param string $subCommandLabel
     * @param array $args
     * @return void
     */
    protected function executeSubCommand(CommandSender $sender, SubCommand $subCommand, string $commandLabel, string $subCommandLabel, array $args)
    {}
}