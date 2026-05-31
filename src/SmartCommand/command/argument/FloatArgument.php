<?php

declare (strict_types=1);

/***
 *   
 * Rajador Developer 
 * 
 *  ██████╗  █████╗      ██╗ █████╗ ██████╗  ██████╗ ██████╗ 
 *  ██╔══██╗██╔══██╗     ██║██╔══██╗██╔══██╗██╔═══██╗██╔══██╗
 *  ██████╔╝███████║     ██║███████║██║  ██║██║   ██║██████╔╝
 *  ██╔══██╗██╔══██║██   ██║██╔══██║██║  ██║██║   ██║██╔══██╗
 *  ██║  ██║██║  ██║╚█████╔╝██║  ██║██████╔╝╚██████╔╝██║  ██║
    ╚═╝  ╚═╝╚═╝  ╚═╝ ╚════╝ ╚═╝  ╚═╝╚═════╝  ╚═════╝ ╚═╝  ╚═╝
 * 
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * @copyright 2023 - 2027 Rajador Developer
 * 
 * Repository: https://github.com/rajadordev/SmartCommand
 * 
 * You can use AutoPluginUpdater to update SmartCommand automatically: https://github.com/rajadordev/AutoPluginUpdater
 * 
**/

namespace SmartCommand\command\argument;

class FloatArgument extends BaseArgument
{

    /**
     * @param string $name
     * @param boolean $required
     * @param boolean $strict If true, will not convert integers to float
     */
    public function __construct(string $name, bool $required = true, bool $strict = false)
    {
        parent::__construct($name, 'float', $required, static function (string &$given) use ($strict) : bool {
            if (is_numeric($given))
            {
                if (strpos($given, '.') === false && $strict)
                {
                    return false;
                }
                $given = (float) $given;
                return true;
            }
            return false;
        });
    }
}