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

namespace SmartCommand;

use pocketmine\plugin\PluginBase;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\message\DefaultMessages;
use SmartCommand\utils\CommandUtils;

final class Loader extends PluginBase 
{

    const PREFIX = '§e§lSMART§bCOMMAND§r§7  ';

    public function onEnable()
    {
        $dir = $this->getDataFolder();
        CommandUtils::openFolder($dir);
        $defaultMessagesList = [
            'English' => 'english-us.json',
            'Portuguese' => 'portuguese-br.json'
        ];
        $defaultMessagesDir = 'messages' . DIRECTORY_SEPARATOR;
        $messagesPath = $dir . $defaultMessagesDir;
        CommandUtils::openFolder($messagesPath);
        foreach ($defaultMessagesList as $resourceName)
        {
            $this->saveResource($defaultMessagesDir . $resourceName);
        }
        DefaultMessages::init($messagesPath, $defaultMessagesList, $this->getLogger());
        SmartCommandAPI::init($this);
    }

    public function onDisable()
    {
        SmartCommandAPI::saveAllStatistics();
    }

}