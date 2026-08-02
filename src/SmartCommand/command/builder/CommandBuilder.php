<?php

namespace SmartCommand\command\builder;

use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\builder\BuildErrorList;
use SmartCommand\command\builder\CommandBuildFailException;
use SmartCommand\command\SmartCommand;
use SmartCommand\libs\DaveRandom\CallbackValidator\CallbackType;
use SmartCommand\message\CommandMessages;
use SmartCommand\message\DefaultMessages;

class CommandBuilder
{

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var string[] */
    protected $aliases = [];

    /** @var string */
    protected $usagePrefix = SmartCommand::DEFAULT_USAGE_PREFIX;

    /** @var CommandMessages */
    protected $messages;

    /** @var class-string<SmartCommand> */
    protected $class;

    /**
     * @param class-string<SmartCommand> $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
        $this->messages = DefaultMessages::ENGLISH();
    }

    /**
     * @param string $name
     * @return static
     */
    public function name(string $name) : CommandBuilder
    { 
        $this->name = $name;
        
        return $this;
    }

    /**
     * @param string $description
     * @return static
     */
    public function description(string $description) : CommandBuilder 
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     * @param string $usagePrefix
     * @return static
     */
    public function usagePrefix(string $usagePrefix) : CommandBuilder
    {
        $this->usagePrefix = $usagePrefix;
        
        return $this;
    }

    /**
     * @param string[] $aliases
     * @return static
     */
    public function aliases(array $aliases) : CommandBuilder
    {
        $this->aliases = $aliases;
        
        return $this;
    }

    /**
     * @param \SmartCommand\message\CommandMessages
     * @return static
     */
    public function messages(CommandMessages $messages) : CommandBuilder
    {
        $this->messages = $messages;   

        return $this;
    }

    /**
     * @return SmartCommand
     */
    public function build() : SmartCommand
    {
        $errors = $this->checkForBuildErrors();
        if ($errors->hasSomeError()) {
            throw new CommandBuildFailException($errors);
        }

        $className = $this->class;
        $command = new $className($this->name, $this->description, $this->usagePrefix, $this->aliases, $this->messages);

        return $command;
    }

    /**
     * @param string $commandMapPrefix PocketMine's command map prefix
     * @return SmartCommand
     */
    public function buildAndRegister(string $commandMapPrefix) : SmartCommand
    {
        $build = $this->build();
        SmartCommandAPI::register($commandMapPrefix, $build);
        return $build;
    }

    public function checkForBuildErrors() : BuildErrorList {
        $lines = [];

        $class = $this->class;
        if (!static::checkConstructionStructure($class)) {
            $lines[] = "Invalid $class::__construct structure for " . get_called_class() . ' builder';
        }

        if (!isset($this->name)) {
            $lines[] = "Command must have a name";
        }

        if (!isset($this->description)) {
            $lines[] = "Command must have a description";
        }

        return new BuildErrorList($lines);
    }

    /**
     * @param string $className
     * @return boolean
     */
    public static function checkConstructionStructure(string $className) : bool 
    {
        $type = CallbackType::createFromCallable(
            function (string $name, string $description, string $prefix = SmartCommand::DEFAULT_USAGE_PREFIX, array $aliases = [], CommandMessages $messages = null) {}
        );

        return $type->isSatisfiedBy([$className, '__construct']);
    }
}
