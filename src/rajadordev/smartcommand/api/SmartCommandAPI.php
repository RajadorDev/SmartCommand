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

namespace rajadordev\smartcommand\api;

use Throwable;
use pocketmine\Server;
use rajadordev\smartcommand\Loader;
use pocketmine\command\CommandSender;
use rajadordev\smartcommand\command\SmartCommand;
use rajadordev\smartcommand\api\command\FrameworkCommand;
use rajadordev\smartcommand\message\DefaultMessages;

final class SmartCommandAPI
{

    const VERSION = '3.0.0';

    const POCKETMINE_API = ['^5.0.0'];

    /** @var string */
    private static string $commandErrorFolder, $commandErrorFile;

    /** @var array{version:string,author:string,github:string,discord:string,api:string} */
    private static array $frameworkDescription;

    /** @return array{version:string,author:string,github:string,discord:string,api:string} */
    public static function getFrameworkDescription() : array 
    {
        return self::$frameworkDescription;
    }

    /**
     * @return void
     */
    public static function init() : void
    {
        $folder = self::getFrameworkFolder();
        self::$commandErrorFolder = $folder . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR;
        if (!file_exists(self::$commandErrorFolder))
        {
            mkdir(self::$commandErrorFolder);
        }
        self::$commandErrorFile = self::$commandErrorFolder . 'errors.log';
        self::$frameworkDescription = [
            'author' => 'Rajador',
            'version' => self::VERSION,
            'api' => implode(', ', self::POCKETMINE_API),
            'github' => 'https://github.com/rajadordev/SmartCommand',
            'discord' => 'rajadortv'
        ];
        $defaultMessagesList = [
            'English' => [
                'file' => 'english-us.json',
                'data' => [
                    'subcommand-notfound' => '§cSub-command §f"§7{subcommand}§f" §cdoes not exist!',
                    'invalid-argument' => '§cArgument §f{name} §cmust be type §f{type_description}§c!',
                    'player-notfound' => '§cPlayer §f{name} §cnot found!',
                    'internal-error' => '§cAn internal error occurred while executing this command! Please try again later.',
                    'no-allowed' => '§cYou do not have permission to use this command',
                    'in-game-command' => '§cYou can only use this command in-game!',
                    'in-console-command' => '§cYou can only use this command in the console!',
                    'sender-cooldown' => '§cPlease wait §f{cooldown}§7s §cbefore using this command again!',
                    'invalid-world' => '§cWorld §f{name} §cnot found!',
                    'usage-line' => '§8-  §f{usage}',
                    'arguments' => [
                        'bool' => 'bool',
                        'number' => 'number',
                        'player' => 'player',
                        'float' => 'float',
                        'integer' => 'int',
                        'string' => 'string',
                        'text' => 'text'
                    ]
                ]
            ],
            'Portuguese' => [
                'file' => 'portuguese-br.json',
                'data' => [
                    'subcommand-notfound' => '§cSub-comando §f"§7{subcommand}§f" §cnão existe!',
                    'invalid-argument' => '§cArgumento §f{name} §cprecisa ser do tipo §f{type_description}§c!',
                    'player-notfound' => '§cJogador §f{name} §cnão encontrado!',
                    'internal-error' => '§cOcorreu um erro interno ao realizar este comando! Tente novamente mais tarde.',
                    'no-allowed' => '§cVocê não tem permissão para usar este comando',
                    'in-game-command' => '§cVocê só pode usar este comando dentro do jogo!',
                    'in-console-command' => '§cVocê só pode usar este comando no console!',
                    'usage-line' => '§8-  §f{usage}',
                    'sender-cooldown' => '§cDigite o comando novamente em §f{cooldown}§7s§c!',
                    'invalid-world' => '§cMapa §f{name} §cnão encontrado!',
                    'arguments' => [
                        'bool' => 'bool',
                        'number' => 'numero',
                        'player' => 'jogador',
                        'float' => 'float',
                        'integer' => 'int',
                        'string' => 'string',
                        'text' => 'texto'
                    ]
                ]
            ]
        ];
        DefaultMessages::init($folder . 'messages' . DIRECTORY_SEPARATOR, $defaultMessagesList);
    }

    /**
     * @param string $prefix
     * @param SmartCommand $command
     * @return void
     */
    public static function register(string $prefix, SmartCommand $command) : void
    {
        Server::getInstance()->getCommandMap()->register($prefix, $command);
    }

    /**
     * @param string $prefix
     * @param SmartCommand[] $commands
     * @return void
     */
    public static function registerCommands(string $prefix, array $commands) : void
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
    public static function commandErrorLog(CommandSender $sender, Throwable $exception, string $formatUsed) : void
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

    public static function debug(string $text) : void
    {
        Server::getInstance()->getLogger()->debug($text);
    }

    public static function getFrameworkFolder() : string 
    {
        return Server::getInstance()->getDataPath() . 'smartcommand' . DIRECTORY_SEPARATOR;
    }

    
}