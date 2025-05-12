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

namespace SmartCommand\command;

use pocketmine\Player;
use pocketmine\command\Command;
use SmartCommand\utils\CommandUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\rule\defaults\PermissionCommandRule;
use SmartCommand\command\rule\RulesHolderTrait;
use SmartCommand\command\subcommand\SubCommand;
use SmartCommand\command\subcommand\SubCommandHolderTrait;
use SmartCommand\message\BaseCommandMessages;
use SmartCommand\message\CommandMessages;
use SmartCommand\message\DefaultMessages;
use Throwable;

abstract class SmartCommand extends Command
{

    const PERMISSION_MEMBER = 'member.command.use';

    const PERMISSION_ROOT_ADMIN = 'administrator.command.use';

    const DEFAULT_USAGE_PREFIX = " \n" . TextFormat::YELLOW . "§eUsage: \n";

    use ArgumentableTrait, SubCommandHolderTrait, RulesHolderTrait;

    /** @var string */
    private $prefix = '';

    /** @var SubCommand[] */
    protected $subCommands = [];

    /** @var CommandMessages */
    protected $messages;

    /**
     * @param string $name
     * @param string $description
     * @param string $usagePrefix
     * @param string[] $aliases
     */
    public function __construct(string $name, string $description, string $usagePrefix = self::DEFAULT_USAGE_PREFIX, array $aliases = [], CommandMessages $messages = null)
    {
        parent::__construct($name, $description, $usagePrefix, $aliases);
        $this->setPermission($this::getRuntimePermission());
        $this->registerRule(new PermissionCommandRule);
        $this->messages = $messages ?? DefaultMessages::ENGLISH();
        $this->prepare();
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

    final public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        try {
            if ($this->parseRules($sender))
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
                                if ($this->formatArguments($args, $sender, $this->getMessages()))
                                {
                                    $this->onRun($sender, $commandLabel, $this->makeArguments($args));
                                }
                            } else {
                                $this->sendUsage($sender, $commandLabel);
                            }
                        } else if ($this->formatArguments($args, $sender, $this->getMessages())) {
                            $this->onRun($sender, $commandLabel, $this->makeArguments($args));
                        }
                    }
                } else if (is_int($this->getArgNeedleIndex())) {
                    $this->sendUsage($sender, $commandLabel);
                } else {
                    $this->onRun($sender, $commandLabel, $this->makeArguments($args));
                }
            }
        } catch (Throwable $error) {
            Server::getInstance()->getLogger()->error("Command /$commandLabel error: " . ((string) $error));
            SmartCommandAPI::commandErrorLog($sender, $error, '/' . $commandLabel);
            $this->messages->send($sender, CommandMessages::GENERIC_INTERNAL_ERROR);
        }
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

    protected function sendUsage(CommandSender $commandSender, string $label = null, int $page = 0, int $maxPerPage = 0)
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

    /**
     * Called after __construct
     * @return void
     */
    abstract protected function prepare();

    /**
     * @return string
     */
    abstract protected static function getRuntimePermission() : string;

    /**
     * Called when the command sender is 
     *
     * @param CommandSender|Player $sender
     * @param string $label
     * @param CommandArguments $args
     * @return void
     */
    abstract protected function onRun(CommandSender $sender, string $label, CommandArguments $args);

}