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

namespace SmartCommand\task;

use Throwable;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;
use pocketmine\command\CommandSender;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\async\AsyncExecutable;
use SmartCommand\command\CommandArguments;
use SmartCommand\benchmark\AsyncCommandBenchmark;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\SubCommand;
use SmartCommand\message\CommandMessages;

abstract class AsyncCommandTask extends AsyncTask
{

    const SENDER = 'sender';

    const COMMAND = 'command';

    const ARGUMENTS = 'args';

    const TAG_ERROR = '__error';

    /** @var string json code that will be filtered and will accept only thread safe value (string, float, int, bool) */
    protected $rawArgs;

    /** @var string */
    protected $senderName;

    /** @var int|null */
    private $benchmarkProcessId = null;

    /** @var bool */
    private $executedByConsole;

    /**
     * @param CommandSender $sender
     * @param AsyncExecutable $command
     * @param CommandArguments $args
     */
    public function __construct(CommandSender $sender, AsyncExecutable $command, CommandArguments $args)
    {
        $this->senderName = $sender->getName();
        $this->executedByConsole = !($sender instanceof Player);
        $this->saveToThreadStore($this->getInternalItemId(self::SENDER), $sender);
        $this->saveToThreadStore($this->getInternalItemId(self::COMMAND), $command);
        $this->saveToThreadStore($this->getInternalItemId(self::ARGUMENTS), $args);
        $this->rawArgs = json_encode(array_filter(
            $args->raw(),
            static function ($value) : bool {
                return (is_string($value) || is_int($value) || is_float($value) || is_bool($value));
            }
        ));
        $this->init($sender, $command, $args);
        $this->benchmarkProcessId = $command->getAsyncBenchmark()->startTaskProcess();
        $command->onPrepareTask($this);
    }

    public function wasExecutedByConsole() : bool 
    {
        return $this->executedByConsole;
    }

    public function getSenderUsername() : string 
    {
        return $this->senderName;
    }

    /** @return CommandSender|Player|null */
    public function getSender()
    {
        return $this->getFromThreadStore($this->getInternalItemId(self::SENDER));
    }

    public function getCommand() : AsyncExecutable
    {
        return $this->getFromThreadStore($this->getInternalItemId(self::COMMAND));
    }

    public function getArguments() : CommandArguments
    {
        return $this->getFromThreadStore($this->getInternalItemId(self::ARGUMENTS));
    }

    /**
     * Called inside __construct method
     *
     * @param CommandSender $sender
     * @param AsyncExecutable $command
     * @param CommandArguments $args
     * @return void
     */
    abstract protected function init(CommandSender $sender, AsyncExecutable $command, CommandArguments $args);

    protected function getInternalItemId(string $name) : string 
    {
        return spl_object_hash($this) . "-$name";
    }

    public function onRun()
    {
        try {
            $this->execute($this->senderName, json_decode($this->rawArgs, true));
        } catch (Throwable $error) {
            $this->setResult([self::TAG_ERROR => (string) $error]);
        }
    }

    /**
     * @param string $commandSender
     * @param array $args
     * @return void
     */
    abstract protected function execute(string $commandSender, array $args);

    public function isValid() : bool 
    {
        $sender = $this->getSender();
        if ($sender instanceof CommandSender)
        {
            if ($sender instanceof Player)
            {
                return $sender->isOnline();
            }
            return true;
        }
        return false;
    }

    public function onCompletion(Server $server)
    {
        try {
            /** @var AsyncExecutable */
            $command = $this->getCommand();
            $command->getAsyncBenchmark()->startSyncCompleteTask();
            $command->onFinishTask($this);
            if (is_int($this->benchmarkProcessId))
            {
                /** @var AsyncCommandBenchmark */
                $benchmark = $command->getAsyncBenchmark();
                $benchmark->stopProcess($this->benchmarkProcessId);
            }
            $result = $this->getResult();
            /** @var CommandArguments */
            $args = $this->getArguments();
            if ($this->isValid())
            {
                /** @var CommandSender|Player */
                $sender = $this->getSender();
                
                if (is_array($result) && isset($result[self::TAG_ERROR]))
                {
                    $task = get_class($this);
                    SmartCommandAPI::errorLog("Error while executing AsyncCommandTask: ($task) " . $result[self::TAG_ERROR]);
                    $command->onTaskError($sender);
                } else {
                    try {
                        $command->onCompleteTask($sender, $args, $result);
                    } catch (Throwable $error) {
                        if ($command instanceof SmartCommand || $command instanceof SubCommand)
                        {
                            $sender->sendMessage($command->getMessages()->get(CommandMessages::GENERIC_INTERNAL_ERROR));
                        }
                        $error = (string) $error;
                        SmartCommandAPI::errorLog("Error trying to complete a AsyncCommandTask: " . $error);
                    }
                }
            } else {
                $command->onInvalidComplete($this->senderName, $args, $result);
            }
        } catch (Throwable $error) {
            $task = get_class($this);
            SmartCommandAPI::errorLog("Error while executing sync AsyncCommandTask: ($task) " . ((string) $error));
        }
        $command->getAsyncBenchmark()->stopSyncCompleteTask();
    }

}