<?php

declare (strict_types=1);
 
/***
 *   
 * Rajador Developer Diamond API
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
use pocketmine\command\CommandSender;
use pocketmine\Player;
use SmartCommand\command\cooldown\rule\CheckCooldownRule;
use SmartCommand\command\rule\CommandSenderRule;
use SmartCommand\command\rule\defaults\CooldownRule;
use SmartCommand\utils\CommandUtils;

trait ExecutableCooldownHolderTrait
{

    /** @var array<string,float> */
    protected $waitingCooldown = [];

    /** @var boolean */
    private $allowToRegisterCheckRule = true;
    
    protected function applyCooldownResult(CommandSender $sender, CooldownResult $result) : bool
    {
        if (is_int($result->cooldownMs) && (!is_string($result->permission) || !$sender->hasPermission($result->permission)) && (!$result->ignoreConsole || $sender instanceof Player)) {
            $this->addToCooldown($sender, $result->cooldownMs);
            return true;
        }
        return false;
    }

    public function inCooldown(CommandSender $sender) : bool 
    {
        return $this->getCooldown($sender) !== null;
    }

    /**
     * @param CommandSender $sender
     * @return float|null
     */
    public function getCooldown(CommandSender $sender)
    {
        $hash = CommandUtils::hashSender($sender);
        if (isset($this->waitingCooldown[$hash])) {
            $diff = $this->waitingCooldown[$hash] - microtime(true);
            if ($diff > 0) {
                return $diff;
            }
            unset($this->waitingCooldown[$hash]);
        }
        return null;
    }

    public function addToCooldown(CommandSender $sender, int $mileseconds)
    {
        $finishAt = microtime(true) + ($mileseconds / 1000);

        $this->waitingCooldown[CommandUtils::hashSender($sender)] = $finishAt;
    }

    public function removeFromCooldown(CommandSender $sender) : bool 
    {
        if (isset($this->waitingCooldown[$hash = CommandUtils::hashSender($sender)])) {
            unset($this->waitingCooldown[$hash]);
            return true;
        }
        return false;
    }

    protected function registerRule(CommandSenderRule $rule)
    {
        if ($rule instanceof CooldownRule) {
            throw new InvalidArgumentException("Can't register a deprecated CooldownRule in the new CooldownSmartCommand");
        } else if ($rule instanceof CheckCooldownRule) {
            if (!$this->allowToRegisterCheckRule) {
                throw new InvalidArgumentException("CheckCooldownRule is already registered");
            }
            $this->allowToRegisterCheckRule = false;
        }
        parent::registerRule($rule);
    }

}