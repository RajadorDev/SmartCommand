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

use InvalidArgumentException;
use RuntimeException;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\ArgumentableTrait;
use SmartCommand\command\callback\builder\utils\CallbackBuildFailException;
use SmartCommand\command\rule\RulesHolderTrait;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\message\DefaultMessages;
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\command\subcommand\SubCommand;

/**
 * @phpstan-type Executable SmartCommand|SubCommand
 */
abstract class ExecutableCallbackBuilder 
{

    use ArgumentableTrait {
        ArgumentableTrait::registerArgument as public addArgument;
        ArgumentableTrait::registerArguments as public addArguments;
    }

    use RulesHolderTrait;

    /** @var string */
    protected $commandName, $commandDescription, $permission;

    /** @var string */
    protected $usagePrefix = SmartCommand::DEFAULT_USAGE_PREFIX;

    /** @var string[] */
    protected $aliases = [];

    /** @var CommandMessages */
    protected $messages;

    /** @var callable */
    protected $closure;

    /** @var string|null */
    protected $messagesPrefix = null;

    public function __construct(
        string $name,
        string $description
    )
    {
        $this->commandName = $name;
        $this->commandDescription = $description;
        $this->messages = DefaultMessages::ENGLISH();
    }

    /**
     * @param string $usagePrefix
     * @return $this
     */
    public function usagePrefix(string $usagePrefix) : ExecutableCallbackBuilder
    {
        $this->usagePrefix = $usagePrefix;
        return $this;
    }

    /**
     * @param string|string[] $aliases
     * @return $this 
     */
    public function aliases($aliases) : ExecutableCallbackBuilder
    {
        if (is_array($aliases)) {
            $this->aliases = $aliases;
        } else if (is_string($aliases)) {
            $this->aliases[] = $aliases;
        } else {
            throw new InvalidArgumentException("Invalid aliases type " . gettype($aliases));
        }
        return $this;
    }

    /**
     * @param string $permissionName
     * @return $this
     */
    public function permission(string $permissionName) : ExecutableCallbackBuilder
    {
        $this->permission = $permissionName;
        return $this;
    }

    /**
     * @param CommandMessages $messages
     * @return $this
     */
    public function messages(CommandMessages $messages) : ExecutableCallbackBuilder
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @param string|null $prefix
     * @return $this
     */
    public function messagesPrefix($prefix) : ExecutableCallbackBuilder
    {
        $this->messagesPrefix = $prefix;
        return $this;
    }

    /**
     * @param CommandSenderRule|CommandSenderRule[] $rules
     * @return $this 
     */
    public function rules($rules) : ExecutableCallbackBuilder
    {
        if (is_array($rules)) {
            $this->registerRules(...$rules);
        } else if ($rules instanceof CommandSenderRule) {
            $this->registerRule($rules);
        } else {
            throw new InvalidArgumentException("Type " . gettype($rules) . ' is not valid CommandSenderRule');
        }
        return $this;
    }


    /**
     * @return Executable
     * @throws CallbackBuildFailException
     */
    public function build()
    {
        $checkErrorsResult = $this->checkBuildErrors();

        if ($checkErrorsResult->hasSomeError()) {
            throw new CallbackBuildFailException($checkErrorsResult);
        }

        $created = $this->create();

        if ((!$created instanceof SmartCommand) && (!$created instanceof SubCommand)) {
            throw new RuntimeException("Instance returned from " . get_called_class() . '::create is not a valid executable');
        }

        $this->putAditionalParams($created);

        return $created;
    }

    /**
     * @param callable $closure
     * @return $this
     */
    public function listen(callable $closure) : ExecutableCallbackBuilder
    {
        $this->closure = $closure;
        return $this;
    }

    /**
     * @return Executable
     */
    abstract protected function create();

    /**
     * @param Executable $command
     * @return void
     */
    abstract protected function putAditionalParams($command);

    
    protected function checkBuildErrors() : BuildErrorList
    {
        $errors = [];
        if (!isset($this->permission)) {
            $errors[] = "Executable \"$this->commandName\" can't be without permission";
        }

        if (!isset($this->closure)) {
            $errors[] = "Executable \"$this->commandName\" without closure";
        }

        return new BuildErrorList($errors);
    }


    
}