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

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\benchmark\AsyncCommandBenchmark;
use SmartCommand\command\AsyncExecutable;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;
use Throwable;

abstract class AsyncCommandTask extends AsyncTask
{

    const SENDER = 'sender';

    const COMMAND = 'command';

    const ARGUMENTS = 'args';

    const BENCHMARK = 'benchmark';

    const TAG_ERROR = '__error';

    /** @var array will be filtered and will accept only thread safe value (string, float, int, bool) */
    protected $rawArgs;

    /** @var string */
    protected $senderName;

    /** @var int|null */
    private $benchmarkProcessId = null;

    /**
     * @param CommandSender $sender
     * @param AsyncExecutable $command
     * @param CommandArguments $args
     */
    public function __construct(CommandSender $sender, AsyncExecutable $command, CommandArguments $args)
    {
        $this->senderName = $sender->getName();
        $this->saveToThreadStore($this->getInternalItemId(self::SENDER), $sender);
        $this->saveToThreadStore($this->getInternalItemId(self::COMMAND), $command);
        $this->saveToThreadStore($this->getInternalItemId(self::ARGUMENTS), $args);
        $this->rawArgs = array_filter(
            $args->raw(),
            static function ($value) : bool {
                return (is_string($value) || is_int($value) || is_float($value) || is_bool($value));
            }
        );
        if ($command instanceof SmartCommand || $command instanceof BaseSubCommand)
        {
            $benchmark = $command->getExecutionBenchmark();
            if ($benchmark instanceof AsyncCommandBenchmark)
            {
                $this->benchmarkProcessId = $benchmark->start();
                $this->saveToThreadStore($this->getInternalItemId(self::BENCHMARK), $benchmark);
            }
        }
    }

    protected function getInternalItemId(string $name) : string 
    {
        return spl_object_hash($this) . "-$name";
    }

    public function onRun()
    {
        try {
            $this->execute($this->senderName, $this->rawArgs);
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
        $sender = $this->getFromThreadStore($this->getInternalItemId(self::SENDER));
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
            if (is_int($this->benchmarkProcessId))
            {
                /** @var AsyncCommandBenchmark */
                $benchmark = $this->getFromThreadStore($this->getInternalItemId(self::BENCHMARK));
                $benchmark->stopProcess($this->benchmarkProcessId);
            }
            $result = $this->getResult();
            /** @var CommandArguments */
            $args = $this->getFromThreadStore($this->getInternalItemId(self::ARGUMENTS));
            /** @var AsyncExecutable */
            $command = $this->getFromThreadStore($this->getInternalItemId(self::COMMAND));
            if ($this->isValid())
            {
                /** @var CommandSender|Player */
                $sender = $this->getFromThreadStore($this->getInternalItemId(self::SENDER));
                
                if (is_array($result) && isset($result[self::TAG_ERROR]))
                {
                    $task = get_class($this);
                    SmartCommandAPI::errorLog("Error while executing AsyncCommandTask: ($task) " . $result[self::TAG_ERROR]);
                    $command->onTaskError($sender);
                } else {
                    $command->onCompleteTask($sender, $args, $result);
                }
            } else {
                $command->onInvalidComplete($this->senderName, $args, $result);
            }
        } catch (Throwable $error) {
            $task = get_class($this);
            SmartCommandAPI::errorLog("Error while executing AsyncCommandTask: ($task) " . ((string) $error));
        }
    }

}