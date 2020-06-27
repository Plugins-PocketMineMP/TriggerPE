# TriggerPE
A Trigger plugin for PocketMine-MP ported from [VariableTriggers](https://github.com/lyokofirelyte/VariableTriggers)

# Functions

* [x] ClickTrigger
* [x] CommandTrigger
* [x] AreaTrigger

# Commands

|command|description|permission|
|---|---|---|
|/vtc|click trigger command|triggerpe.command.vtc|
|/vta|area trigger command|triggerpe.command.vta|
|/vtcmd|command trigger command|triggerpe.command.vtcmd|

|args|usage|
|---|---|
|add|/&lt;command&gt; add &lt;name&gt; &lt;actionType&gt; &lt;action&gt;|
|remove|/&lt;command&gt; remove &lt;name&gt;

|ActionType|description|
|---|---|
|msg|send message to player|
|broadcast|broadcast message|
|cmd|execute the command|
|cmdop|execute the command as op|


# Permissions
|name|permission|
|---|---|
|triggerpe.command.vtc|op|
|triggerpe.command.vta|op|
|triggerpe.command.vtcmd|op|

# API

* Get plugin instance
```php
$plugin = \alvin0319\TriggerPE\TriggerPE::getInstance();
```

* Add custom trigger (not supported yet)
```php
$triggerClass = class CustomTrigger extends \alvin0319\TriggerPE\triggers\Trigger{
    public function execute(\pocketmine\Player $player, array $extraData = []) : bool{
        // return true if succeed or false on failed
        return false;
    }

    public static function jsonDeserialize(array $data) : CustomTrigger{
        return new CustomTrigger(...$data);
    }
};
$plugin->getTriggerFactory()->registerTrigger(CustomTrigger::class);

$plugin->getTriggerFactory()->addTrigger($triggerClass);
```

* Get trigger

```php
$trigger = $plugin->getTriggerFactory()->getTrigger("trigger name");
```

# You can use Target selector!

You can use @r, @p, @s inside action fields...

or

You can add your custom selector.

```php
\alvin0319\TriggerPE\selector\SelectorFactory::registerSelector(new class extends \alvin0319\TriggerPE\selector\Selector{
                                                                    
    public function getSymbol() : string{
        return "b";
    }
                                                                    
    public function find(\pocketmine\Player $player) : ?\pocketmine\Player{
        return $player;
    }
});
```

# License

~~I love GPL~~

[GPL (GNU General Public License)](https://github.com/alvin0319/TriggerPE/blob/master/LICENSE)