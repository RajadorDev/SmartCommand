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

namespace SmartCommand\command;

use Exception;
use pocketmine\Player;
use SmartCommand\utils\CommandUtils;

class CommandArguments
{

    /** @var array<int|string,mixed> */
    protected $argumentsGiven;

    /** @var array<string,bool> */
    protected $requiredMap = [];

    /**
     * @param array $argumentsGiven
     * @param array<string,bool> $argumentsRequired
     */
    public function __construct(array $argumentsGiven, array $requiredMap)
    {
        $this->argumentsGiven = $argumentsGiven;
        $this->requiredMap = $requiredMap;
    }

    /**
     * Check if argument is passed by his name or numeric index
     * NOTE: You don't need to check if the argument exists if you registered it as required
     *
     * @param string|int $index
     * @return boolean
     */
    public function has($index) : bool 
    {
        assert(CommandUtils::validIndexType($index));
        return isset($this->argumentsGiven[$index]);
    }

    /**
     * @see SmartCommand\command\argument\PlayerArgument
     * @param string $argumentName
     * @return Player
     */
    public function getPlayer(string $argumentName) : Player
    {
        return $this->getValue($argumentName);
    }

    /**
     * This method will return string from StringArgument and TextArgument
     * @see SmartCommand\command\argument\StringArgument
     * @see SmartCommand\command\argument\TextArgument
     * @see SmartCommand\command\argument\StringListArgument
     * @param string $argumentName
     * @return string
     */
    public function getString(string $argumentName) : string
    {
        return $this->getValue($argumentName);
    }

    /**
     * @see SmartCommand\command\argument\BoolArgument
     * @param string $argumentName
     * @return boolean
     */
    public function getBool(string $argumentName) : bool
    {
        return $this->getValue($argumentName);
    }

    /**
     * @see SmartCommand\command\argument\NumberArgument
     * @param string $name
     * @return int|float
     */
    public function getNumber(string $name)
    {
        return $this->getValue($name);
    }

    /**
     * @see SmartCommand\command\argument\IntegerArgument
     * @param string $argumentName
     * @return integer
     */
    public function getInteger(string $argumentName) : int 
    {
        return $this->getValue($argumentName);
    }

    /**
     * @see SmartCommand\command\argument\FloatArgument
     * @param string $argumentName
     * @return float
     */
    public function getFloat(string $argumentName) : float
    {
        return $this->getValue($argumentName);
    }

    /**
     * @param string|int $index
     * @return mixed
     */
    public function getValue($index)
    {
        assert(CommandUtils::validIndexType($index));
        if (isset($this->argumentsGiven[$index]))
        {
            return $this->argumentsGiven[$index];
        } else if (isset($this->requiredMap[$index]) && $this->requiredMap[$index]) {
            throw new Exception("Argument required $index does not exists");
        }
        return null;
    }

    public function raw() : array 
    {
        return $this->argumentsGiven;
    }

}