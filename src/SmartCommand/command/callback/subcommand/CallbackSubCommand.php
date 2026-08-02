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

namespace SmartCommand\command\callback\subcommand;

use pocketmine\command\CommandSender;
use SmartCommand\command\callback\CallbackSmartCommandTrait;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;
use SmartCommand\command\argument\Argument;
use SmartCommand\libs\DaveRandom\CallbackValidator\CallbackType;
use SmartCommand\command\rule\CommandSenderRule;

class CallbackSubCommand extends BaseSubCommand
{

    use CallbackSmartCommandTrait;

    /**
     * @param SmartCommand $command
     * @param string $name
     * @param string $description
     * @param string $permission
     * @param callable $closure
     * @param string[] $aliases
     * @param CommandSenderRule[] $rules
     * @param Argument[] $arguments
     */
    public function __construct(
        SmartCommand $command, 
        string $name, 
        string $description,
        string $permission, 
        callable $closure,
        array $aliases = [],
        array $rules = [],
        array $arguments = []
    )
    {
        $this->registerArguments = $arguments;
        $this->registerRules = $rules;
        $this->setClosure($closure);
        parent::__construct($command, $name, $description, $aliases);
        $this->permission = $permission;
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        ($this->callback)($sender, $commandLabel, $subcommandLabel, $args);
    }

    public static function validateClosure(callable $closure): bool
    {
        $type_A = CallbackType::createFromCallable(
            function (CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args) {

            }
        );

        $type_B = CallbackType::createFromCallable(
            function (CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args, CallbackSubCommand $subcommand) {
                
            }
        );

        return $type_A->isSatisfiedBy($closure) || $type_B->isSatisfiedBy($closure);
    }
}