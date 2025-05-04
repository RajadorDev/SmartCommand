# SmartCommand Framework

SmartCommand is a mini **framework for PocketMine**. With SmartCommand, you can create commands in a much faster, more practical, and safer way by validating arguments, players, and subcommands in an organized and efficient manner.

## Community:
**Discord:**

<a href="https://discord.gg/HkfMbBN2AD"><img src="https://img.shields.io/discord/982037265075302551?label=discord&color=7289DA&logo=discord" alt="Discord"></a>

## Installation

First, install the `phar` file from [here](https://github.com/RajadorDev/SmartCommand/releases).

Put the `phar` file in your PocketMine server inside the `plugins/` folder.

Set it as your plugin dependency in:
`YourPlugin/plugin.yml`

```yml
depend: SmartCommand
```

## Creating a new command

You can create a new command using the abstract class `SmartCommand\command\SmartCommand`:

```php
<?php

use pocketmine\Server;
use pocketmine\command\CommandSender;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\argument\TextArgument;

class SayCommand extends SmartCommand
{
    /**
     * The command permission will be registered automatically by the SmartCommand constructor
     *
     * @see SmartCommand\utils\AdminPermissionTrait
     * @see SmartCommand\utils\MemberPermissionTrait
     * @see SmartCommand\command\rule\defaults\PermissionCommandRule
     * @return string
     */
    protected static function getRuntimePermission(): string
    {
        return 'mypermission.perm';
    }

    /**
     * This method will be called inside __construct. Here you can register every SubCommand, Argument, and CommandSenderRule.
     *
     * @see SmartCommand\command\subcommand\SubCommand
     * @see SmartCommand\command\rule\CommandSenderRule
     * @see SmartCommand\command\argument\Argument
     * @return void
     */
    protected function prepare()
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
    protected function onRun(CommandSender $sender, string $label, array $args)
    {
        /** Access the argument directly without checking its existence :) */
        $message = $args['message'];
        Server::getInstance()->broadcastMessage("{$sender->getName()} $message");
        $sender->sendMessage('Message sent');
    }
}
```

### Arguments

You can create and use the default arguments. They will be validated and converted to the expected types.

Example:
```php
use SmartCommand\command\SmartCommand;
use SmartCommand\command\argument\BoolArgument;
use SmartCommand\command\argument\StringArgument;
use SmartCommand\command\argument\IntegerArgument;

protected function prepare()
{
    $this->registerArguments([
        new IntegerArgument('amount'),
        new StringArgument('target'),
        new BoolArgument('warn_player')
    ]);
}

protected function onRun(CommandSender $sender, string $label, array $args)
{
    /** If all arguments are valid, they will be converted to their correct types: **/
    var_dump($args['amount']); // integer
    var_dump($args['target']); // string
    var_dump($args['warn_player']); // bool
}
```

List of default arguments:
```php
use SmartCommand\command\argument\BoolArgument;
use SmartCommand\command\argument\FloatArgument;
use SmartCommand\command\argument\IntegerArgument;
use SmartCommand\command\argument\StringArgument;
// Will search the Player instance by the provided name
use SmartCommand\command\argument\PlayerArgument;
// You can't register another argument after this one
use SmartCommand\command\argument\TextArgument;
```

### SenderRules

The rules will be checked **before** `arguments`, `subcommands`, and `onRun` are processed.

By default, SmartCommand automatically registers `\SmartCommand\rule\PermissionCommandRule` (in the constructor).

You can create your own `CommandSenderRule` to use across multiple commands in your plugin, like:

```php
use pocketmine\command\CommandSender;
use SmartCommand\command\rule\CommandSenderRule;

class OnlyLowercaseName implements CommandSenderRule
{
    /**
     * @return bool True if the sender is allowed to use the command
     */
    public function parse(CommandSender $sender, $command): bool
    {
        return $sender->getName() === strtolower($sender->getName());
    }

    /**
     * Return the message shown when the rule fails
     * @see SmartCommand\message\CommandMessages
     */
    public function getMessage($command, CommandSender $sender): string
    {
        return '§cHey, stop! You are using an uppercase character in your name';
    }
}
```

Then register it like this:

```php
protected function prepare()
{
    $this->registerRule(new OnlyLowercaseName);
}
```

Default rules:

```php
/** Command can only be executed from the console */
use SmartCommand\command\rule\defaults\OnlyConsoleCommandRule;
/** Only players can execute the command */
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
/** @internal Automatically used by SmartCommand and BaseSubCommand */
use SmartCommand\command\rule\defaults\PermissionCommandRule;
/** Adds a cooldown (in milliseconds) before the command or subcommand can be used again */
use SmartCommand\command\rule\defaults\CooldownRule;
```

## SubCommand

You can create subcommands with arguments and rules the same way as `SmartCommand\command\SmartCommand`:

**Example:**

```php
use pocketmine\Server;
use pocketmine\command\CommandSender;
use SmartCommand\command\argument\TextArgument;
use SmartCommand\command\subcommand\BaseSubCommand;

class PopupSubCommand extends BaseSubCommand
{
    protected static function getRunTimePermission(): string
    {
        return 'subcommand.permission';
    }

    protected function prepare()
    {
        /** You can register arguments the same way as in SmartCommand **/
        $this->registerArgument(0, new TextArgument('text'));
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, array $args)
    {
        $text = $args['text'];
        Server::getInstance()->broadcastPopup($text);
        $sender->sendMessage('Popup sent');
    }
}
```
