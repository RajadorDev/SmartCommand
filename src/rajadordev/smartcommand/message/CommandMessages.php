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

use pocketmine\command\CommandSender;

interface CommandMessages 
{

    const INVALID_SUBCOMMAND = 'subcommand-notfound';

    const INVALID_ARGUMENT = 'invalid-argument';

    const PLAYER_NOT_FOUND = 'player-notfound';

    const GENERIC_INTERNAL_ERROR = 'internal-error';

    const NOT_ALLOWED = 'no-allowed';

    const ONLY_PLAYER_ALLOWED = 'in-game-command';

    const ONLY_CONSOLE_ALLOWED = 'in-console-command';

    const USAGE_LINE_FORMAT = 'usage-line';

    const SENDER_IN_COOLDOWN = 'sender-cooldown';

    const ARGUMENT_LONG = 'argument-too-long';

    const ARGUMENT_SHORT = 'argument-too-short';

    const INVALID_WORLD = 'invalid-world';

    /**
     * @param string $id
     * @return boolean
     */
    public function exists(string $id) : bool;

    /**
     * @param string $id
     * @param array|string|null $replace
     * @param array|string|null $to
     * @param bool $usePrefix
     * @return string
     */
    public function get(string $id, array|string|null $replace = null, array|string|null $to = null, bool $usePrefix = true) : string;

    /**
     * @param string $id
     * @param string $text
     * @return CommandMessages
     */
    public function set(string $id, string $text) : CommandMessages;

    /**
     * @param array<string,string> $messages
     * @return CommandMessages
     */
    public function add(array $messages) : CommandMessages;

    /**
     * @param CommandSender $sender
     * @param string $id
     * @param array|string|null $replace
     * @param array|string|null $to
     * @return CommandMessages
     */
    public function send(CommandSender $sender, string $id, array|string|null $replace = null, array|string|null $to = null) : CommandMessages;

}