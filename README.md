# SmartCommand Framework

SmartCommand is a mini **framework for PocketMine**. With SmartCommand, you can create commands in a much faster, more practical, and safer way by validating arguments, players, and subcommands in an organized and efficient manner.

## Community:
**Discord:**

<a href="https://discord.gg/HkfMbBN2AD"><img src="https://img.shields.io/discord/982037265075302551?label=discord&color=7289DA&logo=discord" alt="Discord"></a>

## Installation

**You need to init SmartCommand lib (if not registered yet)**
```php

use rajadordev\smartcommand\api\SmartCommandAPI;

if (!SmartCommandAPI::isRegistered()) {
    SmartCommandAPI::register($this) // $this need to be instance of pocketmine\plugin\Plugin
}
```

## Creating a new command

You can create a new command using the abstract class `rajadordev\smartcommand\command\SmartCommand`:

```php
use pocketmine\Server;
use pocketmine\command\CommandSender;
use rajadordev\smartcommand\command\SmartCommand;
use rajadordev\smartcommand\command\argument\TextArgument;
use rajadordev\smartcommand\command\CommandArguments;

class SayCommand extends SmartCommand
{
    /**
     * The command permission will be registered automatically by the SmartCommand constructor
     *
     * @see rajadordev\smartcommand\utils\AdminPermissionTrait
     * @see rajadordev\smartcommand\utils\MemberPermissionTrait
     * @see rajadordev\smartcommand\command\rule\defaults\PermissionCommandRule
     * @return string
     */
    protected static function getRuntimePermission(): string
    {
        return 'mypermission.perm';
    }

    /**
     * This method will be called inside __construct. Here you can register every SubCommand, Argument, and CommandSenderRule.
     *
     * @see rajadordev\smartcommand\command\subcommand\SubCommand
     * @see rajadordev\smartcommand\command\rule\CommandSenderRule
     * @see rajadordev\smartcommand\command\argument\Argument
     * @return void
     */
    protected function prepare() : void
    {
        /**
         * Arguments are optional to be registered.
         * Here I'm registering the text argument named 'message' (you can use any name you want).
         * The name and type will be generated automatically in the usage with SmartCommand::sendUsage.
         * The usage will be shown as: /say <message: text>
         * The true value sets the 'message' argument as required.
         */
        $this->registerArgument(0, new TextArgument('message', true));

        /** Setting a description (optional too) */
        $this->argumentsDescription = 'Send a message to everyone';

        /** Optional, but a nice touch */
        $this->setPrefix('§l§eSAY  §r');
    }

    /** This method will be called after all rules, arguments, and SubCommands have been processed */
    protected function onRun(CommandSender $sender, string $label, CommandArguments $args) : void
    {
        /** Access the argument directly without checking its existence :) */
        $message = $args->getString('message');
        Server::getInstance()->broadcastMessage("{$sender->getName()} $message");
        $sender->sendMessage('Message sent');
    }
}
```

### Arguments

You can create and use the default arguments. They will be validated and converted to the expected types.

Example:
```php
use rajadordev\smartcommand\command\SmartCommand;
use rajadordev\smartcommand\command\argument\BoolArgument;
use rajadordev\smartcommand\command\argument\StringArgument;
use rajadordev\smartcommand\command\argument\IntegerArgument;
use rajadordev\smartcommand\command\CommandArguments;

protected function prepare() : void
{
    $this->registerArguments(
        new IntegerArgument('amount'),
        new StringArgument('target'),
        new BoolArgument('warn_player')
    );
}

protected function onRun(CommandSender $sender, string $label, CommandArguments $args) : void
{
    /** If all arguments are valid, they will be converted to their correct types: **/
    var_dump($args->getInteger('amount')); // integer
    var_dump($args->getString('target')); // string
    var_dump($args->getBool('warn_player')); // bool
}
```

List of default arguments:
```php
use rajadordev\smartcommand\command\argument\BoolArgument;
use rajadordev\smartcommand\command\argument\FloatArgument;
use rajadordev\smartcommand\command\argument\IntegerArgument;
use rajadordev\smartcommand\command\argument\StringArgument;
// Will search the Player instance by the provided name
use rajadordev\smartcommand\command\argument\PlayerArgument;
// You can't register another argument after this one
use rajadordev\smartcommand\command\argument\TextArgument;
```

### SenderRules

The rules will be checked **before** `arguments`, `subcommands`, and `onRun` are processed.

By default, SmartCommand automatically registers `\rajadordev\smartcommand\rule\PermissionCommandRule` (in the constructor).

You can create your own `CommandSenderRule` to use across multiple commands in your plugin, like:

```php
use pocketmine\command\CommandSender;
use rajadordev\smartcommand\command\SmartCommand;
use rajadordev\smartcommand\command\subcommand\SubCommand;
use rajadordev\smartcommand\command\rule\CommandSenderRule;

class OnlyLowercaseName implements CommandSenderRule
{
    /**
     * @return bool True if the sender is allowed to use the command
     */
    public function parse(CommandSender $sender, SmartCommand|SubCommand $command): bool
    {
        return $sender->getName() === strtolower($sender->getName());
    }

    /**
     * Return the message shown when the rule fails
     * @see rajadordev\smartcommand\message\CommandMessages
     */
    public function getMessage($command, CommandSender $sender): string
    {
        return '§cHey, stop! You are using an uppercase character in your name';
    }
}
```

Then register it like this:

```php
protected function prepare() : void
{
    $this->registerRule(new OnlyLowercaseName);
}
```

Default rules:

```php
/** Command can only be executed from the console */
use rajadordev\smartcommand\command\rule\defaults\OnlyConsoleCommandRule;
/** Only players can execute the command */
use rajadordev\smartcommand\command\rule\defaults\OnlyInGameCommandRule;
/** @internal Automatically used by SmartCommand and BaseSubCommand */
use rajadordev\smartcommand\command\rule\defaults\PermissionCommandRule;
/** Adds a cooldown (in milliseconds) before the command or subcommand can be used again */
use rajadordev\smartcommand\command\rule\defaults\CooldownRule;
```

## SubCommand

You can create subcommands with arguments and rules the same way as `rajadordev\smartcommand\command\SmartCommand`:

**Example:**

```php
use pocketmine\Server;
use pocketmine\command\CommandSender;
use rajadordev\smartcommand\command\argument\TextArgument;
use rajadordev\smartcommand\command\CommandArguments;
use rajadordev\smartcommand\command\subcommand\BaseSubCommand;

class PopupSubCommand extends BaseSubCommand
{
    protected static function getRunTimePermission(): string
    {
        return 'subcommand.permission';
    }

    protected function prepare() : void
    {
        /** You can register arguments the same way as in SmartCommand **/
        $this->registerArgument(0, new TextArgument('text'));
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args) : void
    {
        $text = $args->getString('text');
        Server::getInstance()->broadcastPopup($text);
        $sender->sendMessage('Popup sent');
    }
}
```
