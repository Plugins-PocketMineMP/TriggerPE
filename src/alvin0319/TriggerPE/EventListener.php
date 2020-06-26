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
namespace alvin0319\TriggerPE;

use alvin0319\TriggerPE\triggers\AreaTrigger;
use alvin0319\TriggerPE\triggers\ClickTrigger;
use alvin0319\TriggerPE\triggers\CommandTrigger;
use alvin0319\TriggerPE\triggers\Trigger;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\CommandEnum;
use pocketmine\network\mcpe\protocol\types\CommandParameter;
use function implode;
use function is_array;

class EventListener implements Listener{

	/** @var Trigger[] */
	protected $moveQueue = [];

	public function onPlayerInteract(PlayerInteractEvent $event) : void{
		if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
			$block = $event->getBlock();
			$player = $event->getPlayer();

			if(is_array($data = TriggerQueue::getQueue($player))){
				if($player->getInventory()->getItemInHand()->getId() === ItemIds::BONE){
					switch($data["type"]){
						case Trigger::TYPE_CLICK:
							$data["block"] = [];
							$data["block"]["id"] = $block->getId();
							$data["block"]["meta"] = $block->getDamage();
							$data["block"]["pos"] = implode(":", [$block->getX(), $block->getY(), $block->getZ(), $block->getLevel()->getFolderName()]);
							$trigger = TriggerPE::getInstance()->getTriggerFactory()->createTrigger(ClickTrigger::class, $data);
							TriggerPE::getInstance()->getTriggerFactory()->addTrigger($trigger);
							TriggerPE::message($player, "Successfully created trigger {$trigger->getName()}.");
							TriggerQueue::removeCreateQueue($player);
							break;
						case Trigger::TYPE_AREA:
							if(isset($data["posData"])){
								$data["posData"]["x2"] = $block->getX();
								$data["posData"]["z2"] = $block->getZ();

								$trigger = TriggerPE::getInstance()->getTriggerFactory()->createTrigger(AreaTrigger::class, $data);
								TriggerPE::getInstance()->getTriggerFactory()->addTrigger($trigger);
								TriggerPE::message($player, "Successfully created trigger {$trigger->getName()}.");
								TriggerQueue::removeCreateQueue($player);
							}else{
								$data["posData"] = [];
								$data["posData"]["x1"] = $block->getX();
								$data["posData"]["z1"] = $block->getZ();
								$data["posData"]["world"] = $player->getLevel()->getFolderName();
								TriggerQueue::setQueue($player, $data);
								TriggerPE::message($player, "Please touch other areas.");
							}
							break;
					}
					return;
				}
			}

			foreach(TriggerPE::getInstance()->getTriggerFactory()->getTriggers() as $trigger){
				if($trigger instanceof ClickTrigger){
					if($trigger->execute($player, [$block])){
						$trigger->executeAction($player);
					}
				}
			}
		}
	}

	public function onPlayerCommandPreprocessEvent(PlayerCommandPreprocessEvent $event) : void{
		$player = $event->getPlayer();
		$command = $event->getMessage();
		foreach(TriggerPE::getInstance()->getTriggerFactory()->getTriggers() as $trigger){
			if($trigger instanceof CommandTrigger){
				if($trigger->execute($player, [$command])){
					$trigger->executeAction($player);
					if(!$event->isCancelled())
						$event->setCancelled();// don't show message 'unknown command'
				}
			}
		}
	}

	public function onPlayerMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();
		$from = $event->getFrom();
		$to = $event->getTo();
		if($to->asVector3()->equals($from))
			return; // yaw or pitch move
		foreach(TriggerPE::getInstance()->getTriggerFactory()->getTriggers() as $trigger){
			if($trigger instanceof AreaTrigger){
				if(isset($this->moveQueue[$player->getName()])){
					if($trigger->execute($player)){
						$otherTrigger = $this->moveQueue[$player->getName()];
						if($otherTrigger instanceof AreaTrigger){
							if($trigger !== $otherTrigger){
								$trigger->executeAction($player);
								$this->moveQueue[$player->getName()] = $trigger;
							}
						}else{
							$trigger->executeAction($player);
							$this->moveQueue[$player->getName()] = $trigger;
						}
					}else{
						$this->moveQueue[$player->getName()] = null;
					}
				}else{
					if($trigger->execute($player)){
						$trigger->executeAction($player);
						$this->moveQueue[$player->getName()] = $trigger;
					}
				}
			}
		}
	}

	public function onDataPacketSend(DataPacketSendEvent $event) : void{
		$packet = $event->getPacket();
		if($packet instanceof AvailableCommandsPacket){
			$typeParameter = new CommandParameter();
			$typeParameter->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_TYPE_STRING;
			$typeParameter->isOptional = false;
			$typeParameter->paramName = "type";

			$typeEnum = new CommandEnum();
			$typeEnum->enumName = "type";
			$typeEnum->enumValues = ["add", "remove", "list"];
			$typeParameter->enum = $typeEnum;


			$nameParameter = new CommandParameter();
			$nameParameter->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_TYPE_STRING;
			$nameParameter->isOptional = true;
			$nameParameter->paramName = "name";

			$actionTypeParameter = new CommandParameter();
			$actionTypeParameter->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_TYPE_STRING;
			$actionTypeParameter->isOptional = true;
			$actionTypeParameter->paramName = "actionType";
			$actionTypeEnum = new CommandEnum();
			$actionTypeEnum->enumName = "actionType";
			$actionTypeEnum->enumValues = Trigger::ACTION_LIST;
			$actionTypeParameter->enum = $actionTypeEnum;

			$actionParameter = new CommandParameter();
			$actionParameter->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_TYPE_STRING;
			$actionParameter->isOptional = true;
			$actionParameter->paramName = "action";

			foreach(["vtcmd", "vtc", "vta"] as $cmd){
				if(isset($packet->commandData[$cmd])){
					$data = $packet->commandData[$cmd];
					$data->overloads = [[$typeParameter, $nameParameter, $actionTypeParameter, $actionParameter]];
					$packet->commandData[$cmd] = $data;
				}
			}
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		if(isset($this->moveQueue[$player->getName()]))
			unset($this->moveQueue[$player->getName()]);
		if(is_array(TriggerQueue::getQueue($player)))
			TriggerQueue::removeCreateQueue($player);
	}
}