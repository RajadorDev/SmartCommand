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

use SmartCommand\message\CommandMessages;

interface Argument 
{

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return string
     */
    public function getTypeName() : string;

    /**
     * @param string $given
     * @return boolean
     */
    public function parse(string &$given) : bool;

    /**
     * @param CommandMessages $messages
     * @return string
     */
    public function getTranslatedTypeName(CommandMessages $messages) : string;

    /**
     * @return boolean
     */
    public function isRequired() : bool;

    /**
     * @param CommandMessages|null $messages
     * @return string
     */
    public function getFormat(CommandMessages $messages = null) : string;

}