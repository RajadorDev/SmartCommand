<?php

namespace SmartCommand\utils;

use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;

class CommandBuild
{

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string[] */
    private $aliases = [];

    /** @var string */
    private $usagePrefix = SmartCommand::DEFAULT_USAGE_PREFIX;

    /** @var CommandMessages */
    private $messages = null;

    /** @var class-string<SmartCommand> */
    private $class;

    /**
     * @param class-string<SmartCommand> $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @param string $name
     * @return static
     */
    public function setName(string $name)
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * @param string $description
     * @return static
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     * @param string $usagePrefix
     * @return static
     */
    public function setUsagePrefix(string $usagePrefix = SmartCommand::DEFAULT_USAGE_PREFIX)
    {
        $this->usagePrefix = $usagePrefix;
        
        return $this;
    }

    /**
     * @param string[] $aliases
     * @return static
     */
    public function setAliases(array $aliases = [])
    {
        $this->aliases = $aliases;
        
        return $this;
    }

    /**
     * @param \SmartCommand\message\CommandMessages|null $messages
     * @return static
     */
    public function setMessages(CommandMessages $messages = null)
    {
        $this->messages = $messages;   

        return $this;
    }

    /**
     * @param string $prefix
     * @return void
     */
    public function build(string $prefix)
    {
        SmartCommandAPI::register($prefix, (new $this->class(
            $this->name,
            $this->description,
            $this->usagePrefix,
            $this->aliases,
            $this->messages
        )));
    }
}
