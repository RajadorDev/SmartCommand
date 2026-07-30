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
 * This system is protected by laws! Anyone who shares or resells it will be held accountable
 *
 * Edição, compartilhamento ou revenda é proibido por LEI! Quem fizer será responsabilizado judicialmente
 * 
**/

namespace SmartCommand\command\callback\builder\utils;

use RuntimeException;
use SmartCommand\command\callback\builder\BuildErrorList;
use SmartCommand\utils\CommandUtils;

class CallbackBuildFailException extends RuntimeException
{
   
    public function __construct(BuildErrorList $errors)
    {
        parent::__construct(
            implode(';  ', $errors->errors)
        );
    }
    
}