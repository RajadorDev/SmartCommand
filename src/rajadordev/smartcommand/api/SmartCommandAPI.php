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

use pocketmine\command\Command;
use Throwable;
use RuntimeException;
use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\serializer\AvailableCommandsPacketAssembler;
use pocketmine\network\mcpe\protocol\serializer\AvailableCommandsPacketDisassembler;
use pocketmine\network\mcpe\protocol\types\command\CommandHardEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\player\Player;
use rajadordev\smartcommand\command\argument\Argument;
use rajadordev\smartcommand\command\argument\StringListArgument;
use rajadordev\smartcommand\message\DefaultMessages;
use rajadordev\smartcommand\command\SmartCommand;
use rajadordev\smartcommand\command\subcommand\BaseSubCommand;
use rajadordev\smartcommand\listener\SmartCommandPacketHandler;
use ReflectionClass;
use rajadordev\smartcommand\command\argument\BoolArgument;

final class SmartCommandAPI
{

    const VERSION = '3.0.3';

    const POCKETMINE_API = ['^5.0.0'];

    const GLOBAL_PREFIX_ID = 'SmartCommand-';

    const PARAM_NETWORK_TYPE_IDS = [
        'text' => AvailableCommandsPacket::ARG_TYPE_RAWTEXT,
        'integer' => AvailableCommandsPacket::ARG_TYPE_INT,
        'float' => AvailableCommandsPacket::ARG_TYPE_FLOAT,
        /** It was write error fixed since 3.1.0, but plugins using SmartCommand lower than 3.1 will keep using this keyword */
        'interger' => AvailableCommandsPacket::ARG_TYPE_INT
    ];

    /** @var string */
    private static string $commandErrorFolder, $commandErrorFile;

    /** @var array{version:string,author:string,github:string,discord:string,api:string} */
    private static array $frameworkDescription;

    /** @var Plugin */
    private static ?Plugin $registeredBy = null;

    /** @return array{version:string,author:string,github:string,discord:string,api:string} */
    public static function getFrameworkDescription() : array 
    {
        return self::$frameworkDescription;
    }

    /**
     * @internal used by SmartCommand and SubCommands 
     * @throws RuntimeException
     * @return void
     */
    public static function checkIfRegistered() : void 
    {
        if (is_null(self::$registeredBy))
        {
            throw new RuntimeException("SmartCommand is not registered yet");
        }
    }

    /**
     * @return boolean
     */
    public static function isRegistered() : bool 
    {
        return self::$registeredBy instanceof Plugin;
    }

    /**
     * @param Plugin $plugin
     * @return void
     */
    public static function register(Plugin $plugin) : void
    {
        if (self::$registeredBy) {
            throw new RuntimeException('SmartCommand is already registered');
        }


        self::$registeredBy = $plugin;
        $folder = self::getFrameworkFolder();
        self::$commandErrorFolder = $folder . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR;
        $messagesFolder = $folder . DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR;
        foreach ([$folder, self::$commandErrorFolder, $messagesFolder] as $systemFolder)
        {
            if (!file_exists($systemFolder))
            {
                mkdir($systemFolder);
            }
        }
        self::$commandErrorFile = self::$commandErrorFolder . 'errors.log';
        self::$frameworkDescription = [
            'author' => 'Rajador',
            'version' => self::VERSION,
            'api' => implode(', ', self::POCKETMINE_API),
            'github' => 'https://github.com/rajadordev/SmartCommand',
            'discord' => 'rajadortv'
        ];
        if (DefaultMessages::tryLoadFromGlobal())
        {
            Server::getInstance()->getLogger()->debug("DefaultMessages loaded from cache suceffully");
        } else {
            Server::getInstance()->getLogger()->debug("Loading DefaultMessages from smartcommands files....");
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

        SmartCommandPacketHandler::register($plugin);
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
        $file = fopen(self::$commandErrorFile, 'a');
        $dateFormat = date('[d/m/Y H-i-s]');
        fwrite($file, "\n \n{$dateFormat}  {$sender->getName()} execute {$formatUsed}: " . ((string) $exception));
    }

    public static function debug(string $text) : void
    {
        Server::getInstance()->getLogger()->debug($text);
    }

    public static function getFrameworkFolder() : string 
    {
        return Server::getInstance()->getDataPath() . 'smartcommand' . DIRECTORY_SEPARATOR;
    }

    /**
     * @internal
     *
     * @param AvailableCommandsPacket $packet
     * @param Player $sender
     * @return AvailableCommandsPacket|null
     */
    public static function putCommandTips(AvailableCommandsPacket $packet, Player $sender) : ?AvailableCommandsPacket 
    {
        $changed = false;
        $disassemblePacket = AvailableCommandsPacketDisassembler::disassemble($packet);
        foreach ($disassemblePacket->commandData as $singleCommandData) {
            $commandName = $singleCommandData->getName();
            $command = Server::getInstance()->getCommandMap()->getCommand($commandName);

            if ($command instanceof Command && static::isSmartCommandInstance($command) && $command->testPermissionSilent($sender)) {
                $overloads = self::generateCommandOverloads($command, $sender);
                $singleCommandData->overloads = $overloads;
                $changed = true;
            }
        }

        if ($changed) {
            return AvailableCommandsPacketAssembler::assemble($disassemblePacket->commandData, [], []);
        }
        return null;
    }

    /**
     * @internal Check if the command given is a real SmartCommand instance even if in another namespace
     *
     * @param Command $command
     * @param-out SmartCommand $command
     * @return boolean
     */
    private static function isSmartCommandInstance(Command &$command) : bool 
    {
        return (method_exists($command, 'executeSubCommand') && method_exists($command, 'testPermission') && method_exists($command, 'onRun'));
    }

    /**
     * @internal
     * @param Command $command
     * @param Player $player
     * @return CommandOverload[]
     */
    private static function generateCommandOverloads(Command $command, Player $player) : array
    {
        $overloads = [];
        /** @var SmartCommand $command */
        foreach ($command->getSubCommands() as $subCommand) {
            if (!$player->hasPermission($subCommand->getPermission())) {
                continue;
            }

            $subCommandName = $subCommand->getName();

            $subCommandParam = CommandParameter::enum(
                $subCommandName,
                new CommandHardEnum($subCommandName, [$subCommandName]),
                flags: 0,
                optional: false
            );

            if (method_exists($subCommand, 'getArgument') && !is_null($argumentsOverloads = self::generateArgumentableOverloads($subCommand))) {
                foreach ($argumentsOverloads as $overload) {
                    $overloads[] = new CommandOverload(false, [$subCommandParam, ...$overload->getParameters()]);
                }
            } else {
                $overloads[] = new CommandOverload(false, [$subCommandParam]);
            }

        }

        if (empty($overloads)) {
            if ($commandOverloads = self::generateArgumentableOverloads($command)) {
                foreach ($commandOverloads as $commandOverload) {
                    $overloads[] = $commandOverload;
                }
            }
        }


        return $overloads;
    }

    /**
     * @internal 
     * @param SmartCommand|BaseSubCommand $command
     * @return CommandOverload[]|null
     */
    private static function generateArgumentableOverloads(mixed $command) : ?array
    {
        $params = [];
        for ($index = 0; true; $index++) {
            if ($argument = $command->getArgument($index)) {
                $params[] = self::generateArgumentNetworkParam($argument);
                continue;
            }
            break;
        }

        if (empty($params)) {
            return null;
        }
        return [new CommandOverload(false, $params)];
    }

    /**
     * @param Argument $argument
     * @return CommandParameter
     */
    private static function generateArgumentNetworkParam(mixed $argument) : CommandParameter
    {
        $argumentTypeName = $argument->getTypeName();
        $optionalArgument = !$argument->isRequired();

        if (!isset(self::PARAM_NETWORK_TYPE_IDS[$argumentTypeName])) {

            $hasStringList = str_contains($argument->getTypeName(), '|');
            if ($hasStringList) {
                $typeListName = $argument->getTypeName();
                $enumTypeList = explode('|', $typeListName);
                return CommandParameter::enum(
                    $argument->getName(), 
                    new CommandHardEnum(
                        $argument->getTypeName(),
                        $enumTypeList
                    ),
                    flags: 0,
                    optional: $optionalArgument
                );
            }
            $argumentTypeNetwork = AvailableCommandsPacket::ARG_TYPE_STRING;
        } else {
            $argumentTypeNetwork = self::PARAM_NETWORK_TYPE_IDS[$argumentTypeName];
        }

        return CommandParameter::standard($argument->getName(), $argumentTypeNetwork, 0, $optionalArgument);
    }
    
}