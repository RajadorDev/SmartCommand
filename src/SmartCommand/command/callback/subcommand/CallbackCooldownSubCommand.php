<?php

declare(strict_types=1);

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

namespace SmartCommand\command\callback\subcommand;

use pocketmine\command\CommandSender;
use SmartCommand\command\callback\CallbackSmartCommandTrait;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\cooldown\CooldownResult;
use SmartCommand\command\cooldown\subcommand\CooldownBaseSubCommand;
use SmartCommand\command\SmartCommand;
use SmartCommand\libs\DaveRandom\CallbackValidator\CallbackType;
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\command\argument\Argument;

class CallbackCooldownSubCommand extends CooldownBaseSubCommand
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

    protected function onAllowedRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args): CooldownResult
    {
        return ($this->callback)($sender, $commandLabel, $subcommandLabel, $args, $this);
    }

    public static function validateClosure(callable $closure): bool
    {
        $type_A = CallbackType::createFromCallable(
            function (CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args) : CooldownResult {
                return CooldownResult::IGNORE();
            }
        );

        $type_B = CallbackType::createFromCallable(
            function (CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args, CallbackCooldownSubCommand $subcommand) : CooldownResult {
                return CooldownResult::IGNORE();
            }
        );

        return $type_A->isSatisfiedBy($closure) || $type_B->isSatisfiedBy($closure);
    }
}
