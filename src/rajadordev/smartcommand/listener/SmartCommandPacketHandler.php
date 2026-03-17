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

namespace rajadordev\smartcommand\listener;

use pocketmine\event\HandlerListManager;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use rajadordev\smartcommand\api\SmartCommandAPI;
use RuntimeException;

final class SmartCommandPacketHandler implements Listener
{

    public const VERSION = '1.0.0';

    public const HANDLER_VERSION_IDENTIFIER = '__SmartCommand-Handler';
    
    /** @var Plugin */
    private static Plugin $registeredBy;

    /** @var boolean */
    private static bool $sendingAvaliableCommands = false;

    /**
     * @param Plugin $plugin
     * @return void
     * @throws RuntimeException
     */
    public static function register(Plugin $plugin) : void
    {
        if (isset(self::$registeredBy)) {
            throw new RuntimeException("Can't register SmartCommandPacketHandler cause it's already registered");
        }
        self::$registeredBy = $plugin;
        $logger = Server::getInstance()->getLogger();
        if (isset($GLOBALS[self::HANDLER_VERSION_IDENTIFIER])) {
            if (!version_compare(self::VERSION, $GLOBALS[self::HANDLER_VERSION_IDENTIFIER]::VERSION, '>')) {
                $version = $GLOBALS[self::HANDLER_VERSION_IDENTIFIER]::VERSION;
                $logger->debug("A SmartCommandPacket handler $version is already registered");
                return;
            }
            /** Replacing the outdated PacketHandler */
            $oldPacketHandler = $GLOBALS[self::HANDLER_VERSION_IDENTIFIER];
            $oldVersion = $oldPacketHandler::VERSION;
            $logger->debug("Unregistering old handler version ($oldVersion)...");
            HandlerListManager::global()->unregisterAll($oldPacketHandler);
        }
        Server::getInstance()->getPluginManager()->registerEvents($handlerInstance = new self, $plugin);
        $GLOBALS[self::HANDLER_VERSION_IDENTIFIER] = $handlerInstance;
        $version = self::VERSION;
        $logger->debug("SmartCommandPacket handler $version registered");
    }

    /**
     * @priority HIGH
     * @ignoreCancelled TRUE
     */
    public function sendCommandPacket(DataPacketSendEvent $event) : void
    {
        if (self::$sendingAvaliableCommands) {
            return;
        }

        $packets = $event->getPackets();
        foreach ($packets as $index => $packet) {
            if ($packet instanceof AvailableCommandsPacket) {
                unset($packets[$index]);
                foreach ($event->getTargets() as $target) {
                    if (is_null($newPacket = SmartCommandAPI::putCommandTips($packet, $target->getPlayer()))) {
                        $newPacket = $packet;
                    }
                    self::$sendingAvaliableCommands = true;
                    $target->sendDataPacket($newPacket, false);
                    self::$sendingAvaliableCommands = false;
                }
            }
        }
        $event->setPackets($packets);
    }
}