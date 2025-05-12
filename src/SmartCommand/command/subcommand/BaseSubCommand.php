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
use SmartCommand\command\ArgumentableTrait;
use SmartCommand\command\SmartCommand;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\PermissionCommandRule;
use SmartCommand\command\rule\RulesHolderTrait;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\CommandUtils;
use Throwable;

abstract class BaseSubCommand implements SubCommand
{

    use ArgumentableTrait, RulesHolderTrait;

    /** @var string */
    protected $name, $description, $permission, $descriptionColor = TextFormat::GRAY;

    /** @var array */
    protected $aliases;

    /** @param SmartCommand */
    protected $command;

    /**
     * @param SmartCommand $command
     * @param string $name
     * @param string $description
     * @param string $usage
     * @param array $aliases
     */
    public function __construct(SmartCommand $command, string $name, string $description, array $aliases = [])
    {
        $this->command = $command;
        $this->name = $name;
        $this->description = $description;
        $this->aliases = $aliases;
        $this->permission = $this::getRuntimePermission();
        $this->registerRule(new PermissionCommandRule);
        $this->prepare();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->descriptionColor . $this->description;
    }

    public function getPermission(): string
    {
        return $this->permission;
    }

    public function getUsage(string $commandLabel = null, string $usageLabel = null, bool $includeDescription = true, bool $usePrefix = false): string
    {
        $format = str_replace(
            ['{command_label}', '{subcommand_label}'],
            [$commandLabel ?? $this->command->getName(), $usageLabel ?? $this->getName()],
            $this->generateArgumentsList(ltrim(self::DEFAULT_USAGE, '/'), $this->getMessages(), false, true)
        ) . ($includeDescription ? " {$this->getDescription()}" : '');
        if ($usePrefix)
        {
            $format = $this->getCommand()->getPrefix() . $format;
        } else {
            $format = $this->getMessages()->get(CommandMessages::USAGE_LINE_FORMAT, '{usage}', $format, false);
        }
        return $format;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function isReference(string $input): bool
    {
        return in_array($input, array_merge([$this->name], $this->getAliases()));
    }

    public function getCommand(): SmartCommand
    {
        return $this->command;
    }

    public function getMessages() : CommandMessages
    {
        return $this->getCommand()->getMessages();
    }

    public function execute(CommandSender $sender, string $commandLabel, string $subCommandLabel, array $args)
    {
        try {
            if ($this->parseRules($sender))
            {
                CommandUtils::removeEmptyArgs($args, $this->getTextArgumentIndex());
                if (is_int($argsNeedle = $this->getArgNeedleIndex()))
                {
                    if (isset($args[$argsNeedle]))
                    {
                        if ($this->formatArguments($args, $sender, $this->getMessages()))
                        {
                            $this->onRun($sender, $commandLabel, $subCommandLabel, $this->makeArguments($args));
                        }
                    } else {
                        $this->sendUsage($sender, $commandLabel, $subCommandLabel);
                    }
                } else {
                    $this->onRun($sender, $commandLabel, $subCommandLabel, $this->makeArguments($args));
                }
            }
        } catch (Throwable $error) {
            $format = "/{$commandLabel} {$subCommandLabel}";
            Server::getInstance()->getLogger()->error("Command execution error, {$sender->getName()} used: \"{$format}...\" ");
            SmartCommandAPI::commandErrorLog($sender, $error, $format);
            $this->getMessages()->send($sender, CommandMessages::GENERIC_INTERNAL_ERROR);
        }
    }

    protected function makeArguments(array $args) : CommandArguments
    {
        return new CommandArguments($args, $this->requiredMap);
    }

    protected function sendUsage(CommandSender $sender, string $commandLabel, string $subCommandLabel) 
    {
        $sender->sendMessage($this->getUsage($commandLabel, $subCommandLabel, true, true));
    }

    public function testPermission(CommandSender $sender, string $customPermission = null)
    {
        if (!$sender->hasPermission($customPermission ?? $this->permission))
        {
            $this->getCommand()->getMessages()->send($sender, CommandMessages::NOT_ALLOWED);
            return false; 
        }
    }

    /**
     * Returns the subcommand permission
     *
     * @return string
     */
    abstract protected static function getRuntimePermission() : string;

    /**
     * Called after __construct
     * 
     * @return void
     */
    abstract protected function prepare();

    /**
     * Undocumented function
     *
     * @param CommandSender|Player $sender
     * @param string $commandLabel
     * @param string $subcommandLabel
     * @param CommandArguments $args
     * @return void
     */
    abstract protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args);


}