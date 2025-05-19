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

namespace rajadordev\smartcommand\command;

use Exception;
use pocketmine\world\World;
use pocketmine\player\Player;

class CommandArguments
{

    /** @var array<int|string,mixed> */
    protected array $argumentsGiven;

    /** @var array<string,bool> */
    protected array $requiredMap = [];

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
    public function has(string|int $index) : bool 
    {
        return isset($this->argumentsGiven[$index]);
    }

    /**
     * @see rajadordev\smartcommand\command\argument\PlayerArgument
     * @param string $argumentName
     * @return Player
     */
    public function getPlayer(string $argumentName) : Player
    {
        return $this->getValue($argumentName);
    }

    /**
     * This method will return string from StringArgument and TextArgument
     * @see rajadordev\smartcommand\command\argument\StringArgument
     * @see rajadordev\smartcommand\command\argument\TextArgument
     * @see rajadordev\smartcommand\command\argument\StringListArgument
     * @param string $argumentName
     * @return string
     */
    public function getString(string $argumentName) : string
    {
        return $this->getValue($argumentName);
    }

    /**
     * @see rajadordev\smartcommand\command\argument\BoolArgument
     * @param string $argumentName
     * @return boolean
     */
    public function getBool(string $argumentName) : bool
    {
        return $this->getValue($argumentName);
    }

    /**
     * @see rajadordev\smartcommand\command\argument\NumberArgument
     * @param string $name
     * @return int|float
     */
    public function getNumber(string $name) :int|float
    {
        return $this->getValue($name);
    }

    /**
     * @see rajadordev\smartcommand\command\argument\IntegerArgument
     * @param string $argumentName
     * @return integer
     */
    public function getInteger(string $argumentName) : int 
    {
        return $this->getValue($argumentName);
    }

    /**
     * @see rajadordev\smartcommand\command\argument\FloatArgument
     * @param string $argumentName
     * @return float
     */
    public function getFloat(string $argumentName) : float
    {
        return $this->getValue($argumentName);
    }

    /**
     * @see rajadordev\smartcommand\command\argument\WorldArgument
     * @param string $argumentName
     * @return World
     */
    public function getWorld(string $argumentName) : World 
    {
        return $this->getValue($argumentName);
    }

    /**
     * @param string|int $index
     * @return mixed
     */
    public function getValue(string|int $index) : mixed
    {
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