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

namespace SmartCommand\command\callback;

use SmartCommand\command\subcommand\SubCommand;
use SmartCommand\command\argument\Argument;
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\utils\PrepareCommandException;

trait CallbackSmartCommandTrait
{

    /** @var callable */
    private $callback;

    /** @var Argument[] */
    protected $registerArguments;

    /** @var SubCommand[] */
    protected $registerSubCommands;

    /** @var CommandSenderRule[] */
    protected $registerRules;

    protected static function getRuntimePermission(): string
    {
        /** It isn't used here */
        return 'none';
    }

    protected function prepare()
    {
        $this->registerArguments($this->registerArguments);
        if (count($this->registerRules)) {
            $this->registerRules(...$this->registerRules);
        }

        if (count($this->registerSubCommands)) {
            $this->registerSubCommands($this->registerSubCommands);
        }
    }

    private function setClosure(callable $callback)
    {
        if (!$this->validateClosure($callback)) {
            throw new PrepareCommandException("Invalid closure given");
        }
        $this->callback = $callback;
    }

    /**
     * @param callable $closure
     * @return boolean
     */
    abstract public static function validateClosure(callable $closure) : bool;

}