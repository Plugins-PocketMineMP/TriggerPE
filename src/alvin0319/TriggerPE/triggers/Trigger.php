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
namespace alvin0319\TriggerPE\triggers;

use alvin0319\TriggerPE\selector\SelectorFactory;
use pocketmine\Player;

abstract class Trigger{

	public const TYPE_CLICK = "click";

	public const TYPE_COMMAND = "cmd";

	public const TYPE_AREA = "area";

	public const ACTION_TYPE_COMMAND = "cmd";

	public const ACTION_TYPE_MESSAGE = "msg";

	public const ACTION_TYPE_COMMAND_AS_OP = "cmdop";

	public const ACTION_TYPE_BROADCAST = "broadcast";

	public const ACTION_LIST = [
		self::ACTION_TYPE_COMMAND,
		self::ACTION_TYPE_MESSAGE,
		self::ACTION_TYPE_COMMAND_AS_OP,
		self::ACTION_TYPE_BROADCAST
	];

	protected $name;

	protected $actionType;

	protected $message;

	protected $type;

	public function __construct(string $name, string $actionType, string $message, string $type){
		$this->name = $name;
		$this->actionType = $actionType;
		$this->message = $message;
		$this->type = $type;
	}

	final public function getName() : string{
		return $this->name;
	}

	final public function getActionType() : string{
		return $this->actionType;
	}

	final public function getType() : string{
		return $this->type;
	}

	final public function getMessage() : string{
		return $this->message;
	}

	public function executeAction(Player $player) : void{
		$message = $this->getMessage();
		foreach(SelectorFactory::getSelectors() as $selector){
			if($selector->hasSymbol($message)){
				$find = $selector->find($player);
				if($find instanceof Player){
					$message = $selector->replaceMessage($find, $message);
				}
			}
		}
		switch($this->getActionType()){
			case Trigger::ACTION_TYPE_COMMAND:
				$player->getServer()->dispatchCommand($player, $message);
				break;
			case Trigger::ACTION_TYPE_MESSAGE:
				$player->sendMessage($message);
				break;
			case Trigger::ACTION_TYPE_BROADCAST:
				$player->getServer()->broadcastMessage($message);
				break;
			case Trigger::ACTION_TYPE_COMMAND_AS_OP:
				$bool = $player->isOp();
				$player->setOp(true);
				$player->getServer()->dispatchCommand($player, $message);
				$player->setOp($bool);
				break;
			}
	}

	/**
	 * @param Player $player
	 * @param array $extraData
	 * @return bool if succeed to execute
	 */
	abstract public function execute(Player $player, array $extraData = []) : bool;

	public function jsonSerialize() : array{
		return [
			"name" => $this->name,
			"actionType" => $this->actionType,
			"message" => $this->message,
			"type" => $this->type
		];
	}

	/**
	 * @param array $data
	 * @return Trigger
	 */
	abstract public static function jsonDeserialize(array $data);

	public function equals(Trigger $that) : bool{
		return (
		(
			$this->getName() === $that->getName()
		) && (
			$this->getActionType() === $that->getActionType()
		) && (
			$this->getType() === $that->getActionType()
		)
		);
	}
}