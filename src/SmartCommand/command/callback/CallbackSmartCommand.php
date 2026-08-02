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

namespace SmartCommand\command\callback;

use pocketmine\command\CommandSender;
use SmartCommand\command\callback\builder\CommandCallbackBuilder;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\libs\DaveRandom\CallbackValidator\CallbackType;
use SmartCommand\message\CommandMessages;

class CallbackSmartCommand extends SmartCommand
{

    use CallbackSmartCommandTrait;

    /**
     * @param string $name
     * @param string $description
     * @param callable $closure 
     * @param string $usagePrefix
     * @param string[] $aliases
     * @param CommandMessages|null $messages
     * @param CommandSenderRule[] $rules
     * @param Argument[] $arguments
     */
    public function __construct(
        string $name, 
        string $description,
        callable $closure,
        string $permission,
        string $usagePrefix = self::DEFAULT_USAGE_PREFIX, 
        array $aliases = [], 
        CommandMessages $messages = null,
        array $rules = [],
        array $arguments = []
    )
    {
        $this->setClosure($closure);
        $this->registerRules = $rules;
        $this->registerArguments = $arguments;
        parent::__construct($name, $description, $usagePrefix, $aliases, $messages);
        $this->setPermission($permission);
    }

    /**
     * @param string $name
     * @param string $description
     * @return CommandCallbackBuilder
     */
    public static function create(string $name, string $description) : CommandCallbackBuilder
    {
        return (new CommandCallbackBuilder($name, $description));
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        ($this->callback)($sender, $label, $args, $this);
    }

    public static function validateClosure(callable $closure): bool
    {
        $type_A = CallbackType::createFromCallable(
            function (CommandSender $sender, string $label, CommandArguments $args) {}
        );

        $type_B = CallbackType::createFromCallable(
            function (CommandSender $sender, string $label, CommandArguments $args, CallbackSmartCommand $command) {}
        );

        return $type_A->isSatisfiedBy($closure) || $type_B->isSatisfiedBy($closure);
    }

}