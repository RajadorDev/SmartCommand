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

final class Loader extends PluginBase {

    public function onEnable()
    {
        if (!file_exists($dir = $this->getDataFolder()))
        {
            mkdir($dir);
        }
        $defaultMessagesList = [
            'English' => 'english-us.json',
            'Portuguese' => 'portuguese-br.json'
        ];
        $defaultMessagesDir = 'messages' . DIRECTORY_SEPARATOR;
        if (!file_exists($messagesPath = $dir . $defaultMessagesDir))
        {
            mkdir($messagesPath);
        }
        foreach ($defaultMessagesList as $resourceName)
        {
            $this->saveResource($defaultMessagesDir . $resourceName);
        }
        DefaultMessages::init($messagesPath, $defaultMessagesList, $this->getLogger());
        SmartCommandAPI::init($this);
    }

}