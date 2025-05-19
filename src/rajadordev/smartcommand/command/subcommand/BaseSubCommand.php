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
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * 
**/

namespace rajadordev\smartcommand\command\subcommand;

use Throwable;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use rajadordev\smartcommand\utils\CommandUtils;
use rajadordev\smartcommand\api\SmartCommandAPI;
use rajadordev\smartcommand\command\SmartCommand;
use rajadordev\smartcommand\message\CommandMessages;
use rajadordev\smartcommand\command\CommandArguments;
use rajadordev\smartcommand\command\ArgumentableTrait;
use rajadordev\smartcommand\command\rule\RulesHolderTrait;
use rajadordev\smartcommand\command\rule\CommandSenderRule;
use rajadordev\smartcommand\benchmark\SmartCommandBenchmark;
use rajadordev\smartcommand\command\rule\defaults\PermissionCommandRule;

abstract class BaseSubCommand implements SubCommand
{

    use ArgumentableTrait, RulesHolderTrait;

    /** @var string */
    protected string $name, $description, $permission, $descriptionColor = TextFormat::GRAY;

    /** @var array */
    protected array $aliases;

    /** @param SmartCommand */
    protected SmartCommand $command;

    /** @var SmartCommandBenchmark */
    private SmartCommandBenchmark $executionBenchmark;

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
        $this->executionBenchmark = new SmartCommandBenchmark('Execution', $this);
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

    public function execute(CommandSender $sender, string $commandLabel, string $subCommandLabel, array $args) : void
    {
        $this->executionBenchmark->start();
        try {
            if ($this->parseRules($sender, CommandSenderRule::RULE_PRE_EXECUTION))
            {
                CommandUtils::removeEmptyArgs($args, $this->getTextArgumentIndex());
                if (is_int($argsNeedle = $this->getArgNeedleIndex()))
                {
                    if (isset($args[$argsNeedle]))
                    {
                        if ($this->formatArguments($args, $sender, $this->getMessages()) && $this->parseRules($sender, CommandSenderRule::RULE_EXECUTION))
                        {
                            $this->onRun($sender, $commandLabel, $subCommandLabel, $this->makeArguments($args));
                        }
                    } else {
                        $this->sendUsage($sender, $commandLabel, $subCommandLabel);
                    }
                } else if ($this->parseRules($sender, CommandSenderRule::RULE_EXECUTION)) {
                    $this->onRun($sender, $commandLabel, $subCommandLabel, $this->makeArguments($args));
                }
            }
        } catch (Throwable $error) {
            $format = "/{$commandLabel} {$subCommandLabel}";
            Server::getInstance()->getLogger()->error("Command execution error, {$sender->getName()} used: \"{$format}...\": " . ((string) $error));
            SmartCommandAPI::commandErrorLog($sender, $error, $format);
            $this->getMessages()->send($sender, CommandMessages::GENERIC_INTERNAL_ERROR);
        }
        $this->executionBenchmark->stop();
    }

    public function getExecutionBenchmark() : SmartCommandBenchmark
    {
        return $this->executionBenchmark;
    }

    protected function makeArguments(array $args) : CommandArguments
    {
        return new CommandArguments($args, $this->requiredMap);
    }

    protected function sendUsage(CommandSender $sender, string $commandLabel, string $subCommandLabel) 
    {
        $sender->sendMessage($this->getUsage($commandLabel, $subCommandLabel, true, true));
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
    abstract protected function prepare() : void;

    /**
     * Undocumented function
     *
     * @param CommandSender|Player $sender
     * @param string $commandLabel
     * @param string $subcommandLabel
     * @param CommandArguments $args
     * @return void
     */
    abstract protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args) : void;


}