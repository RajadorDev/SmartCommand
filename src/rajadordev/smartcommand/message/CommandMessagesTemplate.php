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

namespace rajadordev\smartcommand\message;

class CommandMessagesTemplate extends BaseCommandMessages
{

    /** @var CommandMessages */
    private $template;

    public function __construct(CommandMessages $template, array $messages = [], string $prefix = null)
    {
        $this->template = $template;
        parent::__construct($messages, $prefix ?? '');
    }

    public function exists(string $id): bool
    {
        return $this->template->exists($id) || $this->existsHere($id);
    }

    protected function existsHere(string $id) : bool 
    {
        return isset($this->messages[$id]);
    }

    public function get(string $id, array|string|null $replace = null, array|string|null $to = null, bool $usePrefix = true): string
    {
        if ($this->existsHere($id))
        {
            return parent::get($id, $replace, $to, $usePrefix);
        }
        return ($usePrefix ? $this->prefix : '') . $this->template->get($id, $replace, $to, false);
    }

}