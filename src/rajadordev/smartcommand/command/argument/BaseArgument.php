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

namespace rajadordev\smartcommand\command\argument;

use rajadordev\smartcommand\message\CommandMessages;

class BaseArgument implements Argument
{

    /** @var string */
    protected string $name, $typeName;

    /** @var callable */
    protected $validCallback;

    /** @var bool */
    protected bool $required;

    /**
     * @param string $name
     * @param string $typeName
     * @param callable $validator `(string &$given) : bool` Returns true if the value is valid, you can transform the value to int by example
     */
    public function __construct(string $name, string $typeName, bool $required, callable $validator)
    {
        $this->name = $name;
        $this->typeName = $typeName;
        $this->required = $required;
        $this->validCallback = $validator;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function parse(string &$given): bool
    {
        return ($this->validCallback)($given);
    }

    public function isRequired() : bool 
    {
        return $this->required;
    }

    public function getTranslatedTypeName(CommandMessages $messages): string
    {
        if ($messages->exists($id = 'arguments.' . $this->getTypeName()))
        {
            return $messages->get($id, null, null, false);
        }
        return $this->getTypeName();
    }

    public function getFormat(CommandMessages $message = null) : string
    {
        $barriers = $this->required ? ['<', '>'] : ['[', ']'];
        return $barriers[0] . $this->name . ': ' . ($message ? $this->getTranslatedTypeName($message) : $this->typeName) . $barriers[1];
    }

    public function getWrongMessage(CommandMessages $commandMessages, string $argumentUsed): string
    {
        return $commandMessages->get(CommandMessages::INVALID_ARGUMENT, ['{name}', '{type_description}', '{used}'], [$this->getName(), $this->getTranslatedTypeName($commandMessages), $argumentUsed]);
    }

}