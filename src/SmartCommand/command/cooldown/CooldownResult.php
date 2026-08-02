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

namespace SmartCommand\command\cooldown;

use InvalidArgumentException;
use SmartCommand\command\ExecutionResult;

class CooldownResult implements ExecutionResult
{

    /** @var integer|null */
    public $cooldownMs;

    /** @var string|null */
    public $permission;

    /** @var boolean */
    public $ignoreConsole;

    /**
     * @param integer|null
     * @param string $permission
     * @param boolean $ignoreConsole
     */
    protected function __construct(int $cooldownMs = null, $permission = null, $ignoreConsole = true)
    {
        $this->cooldownMs = $cooldownMs;
        if (is_int($cooldownMs) && $cooldownMs <= 0) {
            throw new InvalidArgumentException("Cooldown must to be higher than zero");
        }
        $this->permission = $permission;
        $this->ignoreConsole = $ignoreConsole;
    }

    public static function secondsToMs(int $seconds) : int 
    {
        return $seconds * 1000;
    }

    /**
     * It will ignore the execution and WON'T add the sender to cooldown
     *
     * @return CooldownResult
     */
    public static function IGNORE() : CooldownResult
    {
        return new self;
    }

    /**
     * @param integer $cooldownMs
     * @param string $byPassPermission
     * @param boolean $ignoreConsole
     * @return CooldownResult
     */
    public static function ADD(int $cooldownMs, string $byPassPermission = null, bool $ignoreConsole = true) : CooldownResult
    {
        return new self($cooldownMs, $byPassPermission, $ignoreConsole);
    }




}