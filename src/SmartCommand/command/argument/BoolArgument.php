<?php

declare (strict_types=1);

/***
 *   
 * Rajador Developer Diamond API
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

use InvalidArgumentException;

class BoolArgument extends BaseArgument
{

    /** @var string|string[] */
    protected $trueName, $falseName;

    /**
     * @param string $name
     * @param boolean $required
     * @param string|string[] $trueName
     * @param string|string[] $falseName
     * @param boolean $showAsList
     */
    public function __construct(string $name, bool $required = true, $trueName = 'true', $falseName = 'false', bool $showAsList = false)
    {
        
        self::assertBooleanParam($trueName);
        self::assertBooleanParam($falseName);

        
        $this->trueName = $trueName;
        $this->falseName = $falseName;

        $typeName = 'bool';

        if ($showAsList) {
            $names = [];
            foreach ([$trueName, $falseName] as $booleanName) {
                if (is_array($booleanName)) {
                    $names[] = array_shift($booleanName);
                    continue;
                }
                $names[] = $booleanName;
            }
            
            $typeName = implode('|', $names);
        }
        parent::__construct(
            $name, 
            $typeName, 
            $required, 
            function (string &$given) : bool {
                $input = strtolower($given);
                $result = $this->getBooleanValue($input);
                if (is_null($result)) {
                    return false;
                }
                $given = $result;
                return true;
            }
        );
    }

    /**
     * @param string $input
     * @return boolean|null
     */
    public function getBooleanValue(string $input)
    {
        if ((is_array($this->trueName) && in_array($input, $this->trueName)) || $input === $this->trueName) {
            return true;
        } else if ((is_array($this->falseName) && in_array($input, $this->falseName)) || $input === $this->falseName) {
            return false;
        }
        return null;
    }

    public static function assertBooleanParam($param) 
    {
        if (is_string($param)) {
            return true;
        } else if (!is_array($param)) {
            throw new InvalidArgumentException("Invalid boolean names param, type " . gettype($param) . ' given. string[] expected');
        } else if (empty($param)) {
            throw new InvalidArgumentException("Boolean names list cannot be empty");
        }

        foreach ($param as $index => $name) {
            if (!is_string($name)) {
                throw new InvalidArgumentException("Boolean argument param index '$index' must to be of type string");
            }
        }
    }
    
}