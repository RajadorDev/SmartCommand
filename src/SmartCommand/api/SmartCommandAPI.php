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
        $currentFileData = '';
        if (file_exists(self::$commandErrorFile))
        {
            $currentFileData = file_get_contents(self::$commandErrorFile);
        }
        $dateFormat = date('[d/m/Y H-i-s]');
        file_put_contents(
            self::$commandErrorFile,
            $currentFileData . "\n \n{$dateFormat}  {$sender->getName()} execute {$formatUsed}:" . ((string) $exception)
        );
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
     * @return void
     */
    public static function onViolation(SmartCommandBenchmark $benchMark, float $time)
    {
        $time = number_format($time, 2);
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

    
}