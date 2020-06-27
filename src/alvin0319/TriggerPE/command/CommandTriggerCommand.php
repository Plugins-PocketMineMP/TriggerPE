<?php

/*

 *  _____      _                        ___  __
 * /__   \_ __(_) __ _  __ _  ___ _ __ / _ \/__\
 *   / /\/ '__| |/ _` |/ _` |/ _ \ '__/ /_)/_\
 *  / /  | |  | | (_| | (_| |  __/ | / ___//__
 *  \/   |_|  |_|\__, |\__, |\___|_| \/   \__/
 *               |___/ |___/
 * Copyright (C) 2020 alvin0319
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);
namespace alvin0319\TriggerPE\command;

use alvin0319\TriggerPE\TriggerPE;
use alvin0319\TriggerPE\triggers\CommandTrigger;
use alvin0319\TriggerPE\triggers\Trigger;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use function implode;
use function in_array;
use function trim;

class CommandTriggerCommand extends Command implements PluginIdentifiableCommand{

	public function __construct(){
		parent::__construct("vtcmd", "CommandTrigger comamnd");
		$this->setPermission("triggerpe.command.vtcmd");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		switch($args[0] ?? "x"){
			case "add":
				if(trim($args[1] ?? "") !== ""){
					if(!(TriggerPE::getInstance()->getTriggerFactory()->getTrigger($args[1])) instanceof Trigger){
						if(in_array($args[2] ?? "", Trigger::ACTION_LIST)){
							if(trim($args[3] ?? "") !== ""){
								if(trim($args[4] ?? "") !== ""){
									$trigger = TriggerPE::getInstance()->getTriggerFactory()->createTrigger(CommandTrigger::class, [
										"name" => $args[1],
										"actionType" => $args[2],
										"message" => $args[3],
										"type" => Trigger::TYPE_COMMAND,
										"command" => $args[4]
									]);
									TriggerPE::getInstance()->getTriggerFactory()->addTrigger($trigger);
									TriggerPE::message($sender, "Successfully created trigger {$trigger->getName()}.");
								}else{
									TriggerPE::message($sender, "You must provide a <command> value.");
								}
							}else{
								TriggerPE::message($sender, "You must provide a <action> value.");
							}
						}else{
							TriggerPE::message($sender, "No action with that name exists.");
							TriggerPE::message($sender, "Available actions: " . implode(", ", Trigger::ACTION_LIST));
						}
					}else{
						TriggerPE::message($sender, "A trigger with that name already exists.");
					}
				}else{
					TriggerPE::message($sender, "Usage: /vtcmd add <name> <actionType> <action> <command>");
				}
				break;
			case "remove":
				if(trim($args[1] ?? "") !== ""){
					if(($trigger = TriggerPE::getInstance()->getTriggerFactory()->getTrigger($args[1])) instanceof Trigger){
						if($trigger instanceof CommandTrigger){
							TriggerPE::getInstance()->getTriggerFactory()->removeTrigger($trigger);
							TriggerPE::message($sender, "The {$trigger->getName()} trigger was removed.");
						}else{
							TriggerPE::message($sender, "That trigger is not a command trigger.");
						}
					}else{
						TriggerPE::message($sender, "No trigger with that name exists.");
					}
				}else{
					TriggerPE::message($sender, "Usage: /vtcmd remove <name>");
				}
				break;
			case "list":
				$list = [];
				foreach(TriggerPE::getInstance()->getTriggerFactory()->getTriggers() as $trigger){
					if($trigger instanceof CommandTrigger){
						$list[] = $trigger->getName();
					}
				}
				TriggerPE::message($sender, implode(", ", $list));
				break;
			default:
				TriggerPE::message($sender, "/vtcmd add <name> <actionType> <action> <command>");
				TriggerPE::message($sender, "/vtcmd remove <name>");
				TriggerPE::message($sender, "/vtcmd list");
		}
		return true;
	}

	/**
	 * @return TriggerPE
	 */
	public function getPlugin() : Plugin{
		return TriggerPE::getInstance();
	}
}