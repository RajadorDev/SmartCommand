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

namespace rajadordev\smartcommand\command;

use Throwable;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginOwned;
use pocketmine\command\CommandSender;
use rajadordev\smartcommand\utils\CommandUtils;
use rajadordev\smartcommand\api\SmartCommandAPI;
use rajadordev\smartcommand\message\CommandMessages;
use rajadordev\smartcommand\message\DefaultMessages;
use rajadordev\smartcommand\message\BaseCommandMessages;
use rajadordev\smartcommand\command\rule\RulesHolderTrait;
use rajadordev\smartcommand\command\subcommand\SubCommand;
use rajadordev\smartcommand\command\rule\CommandSenderRule;
use rajadordev\smartcommand\benchmark\SmartCommandBenchmark;
use rajadordev\smartcommand\command\subcommand\SubCommandHolderTrait;

abstract class SmartCommand extends Command implements PluginOwned
{

    const PERMISSION_MEMBER = 'member.command.use';

    const PERMISSION_ROOT_ADMIN = 'administrator.command.use';

    const DEFAULT_USAGE_PREFIX = " \n" . TextFormat::YELLOW . "§eUsage: ";

    use ArgumentableTrait, SubCommandHolderTrait, RulesHolderTrait;

    /** @var string */
    private string $prefix = '';

    /** @var SmartCommandBenchmark */
    private SmartCommandBenchmark $executionBenchmark;

    /** @var Plugin */
    protected Plugin $plugin;

    /** @var CommandMessages */
    protected CommandMessages $messages;

    /**
     * @param Plugin $plugin
     * @param string $name
     * @param string $description
     * @param string $usagePrefix
     * @param string[] $aliases
     */
    public function __construct(Plugin $plugin, string $name, string $description, string $usagePrefix = self::DEFAULT_USAGE_PREFIX, array $aliases = [], CommandMessages $messages = null)
    {
        $this->plugin = $plugin;
        SmartCommandAPI::checkIfRegistered();
        parent::__construct($name, $description, $usagePrefix . "\n", $aliases);
        $this->executionBenchmark = new SmartCommandBenchmark('Execution', $this);
        $this->setPermission($this->getRuntimePermission());
        $this->messages = $messages ?? DefaultMessages::ENGLISH();
        $this->prepare();
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function getPrefix() : string 
    {
        return $this->prefix;
    }

    protected function setPrefix(string $prefix) : SmartCommand
    {
        $this->prefix = $prefix;
        if ($this->messages instanceof BaseCommandMessages)
        {
            $this->messages->setPrefix($prefix);
        }
        return $this;
    }

    public function getMessages() : CommandMessages
    {
        return $this->messages;
    }

    final public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $this->executionBenchmark->start();
        try {
            if ($this->parseRules($sender, CommandSenderRule::RULE_PRE_EXECUTION))
            {
                CommandUtils::removeEmptyArgs($args, $this->getTextArgumentIndex());
                if (isset($args[0]))
                {
                    if (!$this->tryExecuteSubCommand($sender, $commandLabel, $args))
                    {
                        if (is_int($indexNeedle = $this->getArgNeedleIndex()))
                        {
                            if (isset($args[$indexNeedle]))
                            {
                                if ($this->formatArguments($args, $sender, $this->getMessages()) && $this->parseRules($sender, CommandSenderRule::RULE_EXECUTION))
                                {
                                    $this->onRun($sender, $commandLabel, $this->makeArguments($args));
                                }
                            } else {
                                $this->sendUsage($sender, $commandLabel);
                            }
                        } else if ($this->formatArguments($args, $sender, $this->getMessages()) && $this->parseRules($sender, CommandSenderRule::RULE_EXECUTION)) {
                            $this->onRun($sender, $commandLabel, $this->makeArguments($args));
                        }
                    }
                } else if (is_int($this->getArgNeedleIndex())) {
                    $this->sendUsage($sender, $commandLabel);
                } else if ($this->parseRules($sender, CommandSenderRule::RULE_EXECUTION)) {
                    $this->onRun($sender, $commandLabel, $this->makeArguments($args));
                }
            }
        } catch (Throwable $error) {
            Server::getInstance()->getLogger()->error("Command /$commandLabel error: " . ((string) $error));
            SmartCommandAPI::commandErrorLog($sender, $error, '/' . $commandLabel);
            $this->messages->send($sender, CommandMessages::GENERIC_INTERNAL_ERROR);
        }
        $this->executionBenchmark->stop();
    }

    public function getExecutionBenchmark() : SmartCommandBenchmark
    {
        return $this->executionBenchmark;
    }

    protected function makeArguments(array $args) : CommandArguments
    {
        return new CommandArguments(
            $args,
            $this->requiredMap
        );
    }

    /**
     * @param string $label
     * @param CommandSender $sender
     * @param integer $page
     * @param integer $maxPerPage
     * @return string
     */
    protected function generateUsage(string $label, CommandSender $sender, int $page = 0, int $maxPerPage = 0) : string 
    {
        return $this->getUsage() . implode(
            "\n",
            array_map(
                static function (string $usageLine) : string {
                    return $usageLine;
                },
                $this->generateUsageList($label,$sender, $page, $maxPerPage)
            )
        );
    }

    protected function sendUsage(CommandSender $commandSender, string $label = null, int $page = 0, int $maxPerPage = 0) : void
    {
        $commandSender->sendMessage($this->generateUsage($label, $commandSender, $page, $maxPerPage));
    }

    protected function generateUsageList(string $label, CommandSender $sender, int $page = null, int $maxPerPage = null) : array 
    {
        $subCommands = $this->generateSubCommandsUsages($label, $sender);
        $arguments = count($this->arguments) > 0 ? $this->generateArgumentsList($label, $this->getMessages()) : null;
        if ($arguments)
        {
            $list = array_merge([$arguments], $subCommands);
        } else {
            $list = $subCommands;
        }
        if (!is_null($page) && $page > 0)
        {
            assert(is_int($maxPerPage));
            $list = array_slice($list, ($page == 1 ? 0 : (($page - 1) * $maxPerPage)), $maxPerPage);
        }
        return $list;
    }

    protected function executeSubCommand(CommandSender $sender, SubCommand $subCommand, string $commandLabel, string $subCommandLabel, array $args) : bool 
    {
        if ($this->parseRules($sender, CommandSenderRule::RULE_EXECUTION))
        {
            $subCommand->execute($sender, $commandLabel, $subCommandLabel, $args);
            return true;
        }
        return false;
    }

    public function testPermission(CommandSender $target, ?string $permission = null): bool
    {
        if ($this->testPermissionSilent($target, $permission))
        {
            return true;
        }
        $target->sendMessage($this->messages->get(CommandMessages::NOT_ALLOWED));
        return false;
    }

    /**
     * Called after __construct
     * @return void
     */
    abstract protected function prepare() : void;

    /**
     * @return string
     */
    abstract protected function getRuntimePermission() : string;

    /**
     * Called when the command sender is 
     *
     * @param CommandSender|Player $sender
     * @param string $label
     * @param CommandArguments $args
     * @return void
     */
    abstract protected function onRun(CommandSender $sender, string $label, CommandArguments $args) : void;

}