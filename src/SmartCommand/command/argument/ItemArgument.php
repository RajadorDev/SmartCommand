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

namespace SmartCommand\command\argument;

use pocketmine\item\Item;
use SmartCommand\message\CommandMessages;

class ItemArgument extends BaseArgument
{

    /**
     * @param string $name
     * @param boolean $allowUnknow If true unknow item will be parsed
     * @param boolean $allowAir If true Air will be parsed
     * @param boolean $required
     */
    public function __construct(string $name, bool $allowUnknow = false, bool $allowAir = false, bool $required = true)
    {
        parent::__construct($name, 'string', $required, 
            static function (string &$given) use ($allowUnknow, $allowAir) : bool {
                $item = Item::fromString($given);
                if ($item instanceof Item)
                {
                    if (!$allowUnknow && $item->getName() == 'Unknown')
                    {
                        return false;
                    } else if (!$allowAir && $item->getId() == Item::AIR) {
                        return false;
                    }
                    $given = $item;
                    return true;
                }
                return false;
            }
        );
    }

    public function getWrongMessage(CommandMessages $commandMessages, string $argumentUsed): string
    {
        return $commandMessages->get(CommandMessages::INVALID_ITEM, '{given}', $argumentUsed);
    }

}