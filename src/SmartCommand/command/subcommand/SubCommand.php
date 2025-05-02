<?php

declare (strict_types=1);

/***
 *   
 * Rajador Developer
 * 
 * ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
 * ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
 * ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█
 * 
 * GitHub: https://github.com/RajadorDev
 * 
 * Discord: rajadortv
 * 
 * 
**/

namespace SmartCommand\command\subcommand;

use pocketmine\command\CommandSender;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;

interface SubCommand 
{

    const DEFAULT_USAGE = '/{command_label} {subcommand_label}';

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return string
     */
    public function getDescription() : string;

    /**
     * @param string|null $commandLabel
     * @param string|null $usageLabel
     * @param bool $includeDescription
     * @return string
     */
    public function getUsage(string $commandLabel = null, string $usageLabel = null, bool $includeDescription = true) : string;

    /**
     * @return string
     */
    public function getPermission() : string;

    /**
     * @return SmartCommand
     */
    public function getCommand() : SmartCommand;

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string $subCommandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, string $subCommandLabel, array $args);

    /**
     * @return string[]
     */
    public function getAliases() : array;

    /**
     * @param string $input
     * @return bool
     */
    public function isReference(string $input) : bool;

    /**
     * @return CommandMessages
     */
    public function getMessages() : CommandMessages;

}