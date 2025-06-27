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

namespace SmartCommand\api;

use Throwable;
use pocketmine\Server;
use SmartCommand\Loader;
use SmartCommand\utils\CommandUtils;
use pocketmine\command\CommandSender;
use SmartCommand\command\SmartCommand;
use pocketmine\scheduler\FileWriteTask;
use SmartCommand\api\command\FrameworkCommand;
use SmartCommand\benchmark\SmartCommandBenchmark;
use SmartCommand\command\subcommand\BaseSubCommand;

final class SmartCommandAPI
{

    /** @var string */
    private static $commandErrorFolder, $commandErrorFile, $commandViolationsFolder, $statisticsFolder;

    /** @var array{version:string,author:string,github:string,discord:string,api:string} */
    private static $frameworkDescription;

    /** @var Loader */
    private static $plugin;

    /** @return array{version:string,author:string,github:string,discord:string,api:string} */
    public static function getFrameworkDescription() : array 
    {
        return self::$frameworkDescription;
    }

    public static function getFolder() : string 
    {
        return self::$plugin->getDataFolder();
    }

    public static function getStatisticFolder() : string 
    {
        return self::$statisticsFolder;
    }

    /**
     * @internal Used by Loader class
     * @param Loader $loader
     * @return void
     */
    public static function init(Loader $loader)
    {
        self::$plugin = $loader;
        $mainDir = $loader->getDataFolder();
        self::$commandErrorFolder = $mainDir . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR;
        self::$commandViolationsFolder = $mainDir . DIRECTORY_SEPARATOR . 'violations' . DIRECTORY_SEPARATOR;
        self::$statisticsFolder = $mainDir . DIRECTORY_SEPARATOR . 'statistics' . DIRECTORY_SEPARATOR;
        
        foreach ([self::$commandErrorFolder, self::$commandViolationsFolder, self::$statisticsFolder] as $systemFolder)
        {
            CommandUtils::openFolder($systemFolder);
        }

        self::$commandErrorFile = self::$commandErrorFolder . 'errors.log';
        $pluginDescription = $loader->getDescription();
        self::$frameworkDescription = [
            'author' => 'Rajador',
            'version' => $pluginDescription->getVersion(),
            'api' => implode(', ', $pluginDescription->getCompatibleApis()),
            'github' => 'https://github.com/RajadorDev/SmartCommand',
            'discord' => 'rajadortv'
        ];
        SmartCommandAPI::register('smartcommand', new FrameworkCommand('smartcommand', 'SmartCommand framework', Loader::PREFIX . "\n", ['sc']));
    }

    /**
     * @param string $prefix
     * @param SmartCommand $command
     * @return void
     */
    public static function register(string $prefix, SmartCommand $command)
    {
        Server::getInstance()->getCommandMap()->register($prefix, $command);
    }

    /**
     * @param string $prefix
     * @param SmartCommand[] $commands
     * @return void
     */
    public static function registerCommands(string $prefix, array $commands)
    {
        Server::getInstance()->getCommandMap()->registerAll($prefix, $commands);
    }

    /**
     * @internal Called when some exception happen with the commands/subcommands
     * @param CommandSender $sender
     * @param Throwable $exception
     * @param string $formatUsed
     * @return void
     */
    public static function commandErrorLog(CommandSender $sender, Throwable $exception, string $formatUsed)
    {
        self::errorLog("{$sender->getName()} execute {$formatUsed}:" . ((string) $exception), false);
    }

    /**
     * @internal Called by crashed async tasks
     * @param string $error
     * @param bool $showInConsole
     * @return void
     */
    public static function errorLog(string $text, bool $showInConsole = true)
    {
        $currentFileData = '';
        if (file_exists(self::$commandErrorFile))
        {
            $currentFileData = file_get_contents(self::$commandErrorFile);
        }
        $dateFormat = date('[d/m/Y H-i-s]');
        file_put_contents(
            self::$commandErrorFile,
             $currentFileData . "\n \n$dateFormat  " . $text
        );
        if ($showInConsole)
        {
            self::$plugin->getLogger()->error($text);
        }
    }


    /**
     * @param string $text
     * @return void
     */
    public static function debug(string $text)
    {
        self::$plugin->getLogger()->debug($text);
    }

    /**
     * @param SmartCommandBenchmark $benchMark
     * @param float $time
     * @return void
     */
    public static function onViolation(SmartCommandBenchmark $benchMark, float $time)
    {
        $time = number_format($time * 1000, 2);
        self::$plugin->getLogger()->alert("{$benchMark->getCommandFormat()} violated a tick and ended in §c{$time}ms");
        $name = $benchMark->getCommand()->getName();
        $filePath = self::$commandViolationsFolder . strtolower($name) . '_violations.txt';
        if (file_exists($filePath))
        {
            $fileData = file_get_contents($filePath);
        } else {
            $fileData = '';
        }
        $dateFormat = date('[d/m/Y H-i-s]');
        $fileData .= " \n \n$dateFormat: {$benchMark->getCommandFormat()} ends in {$time}ms";
        Server::getInstance()->getScheduler()->scheduleAsyncTask(
            new FileWriteTask($filePath, $fileData)
        );
    }

    /**
     * @internal Called by Loader in method onDisable
     * @return void
     */
    public static function saveAllStatistics()
    {
        $started = microtime(true);
        $commandsChecked = [];
        $logger = self::$plugin->getLogger();
        CommandUtils::openFolder(self::$statisticsFolder);
        $logger->debug("Saving every command statistics...");
        $count = 0;
        foreach (Server::getInstance()->getCommandMap()->getCommands() as $command)
        {
            if ($command instanceof SmartCommand && !in_array($command, $commandsChecked))
            {
                $commandsChecked[] = $command;
                try {
                    $logger->debug("Saving {$command->getName()} statistics...");
                    self::saveStatistics($command->getExecutionBenchmark());
                    $count++;
                    foreach ($command->getSubCommands() as $subcommand)
                    {
                        if ($subcommand instanceof BaseSubCommand)
                        {
                            self::saveStatistics($subcommand->getExecutionBenchmark());
                            $count++;
                        }
                    }
                } catch (Throwable $error) {
                    $logger->error("Error ocurred while saving {$command->getName()} statistics: " . ((string) $error));
                }
            }
        }
        $finishedMs = (microtime(true) - $started) * 1000;
        $finishedMs = number_format($finishedMs, 2);
        $logger->debug("$count Statistics saved in {$finishedMs}ms");
    }

    /**
     * @param SmartCommandBenchmark $benchmark
     * @return void
     */
    public static function saveStatistics(SmartCommandBenchmark $benchmark)
    {
        if ($benchmark->getBenchmarkTimes() > 0)
        {
            $path = self::$statisticsFolder . str_replace(['/', ' '], ['', '_'], $benchmark->getCommandFormat()) . '.json';
            if (file_exists($path))
            {
                $data = json_decode(file_get_contents($path), true);
                if (!is_array($data))
                {
                    $data = [];
                }
            } else {
                $data = [];
            }
            $data[] = $benchmark->jsonSerialize();
            file_put_contents($path, json_encode($data));
        }
    }

    
}