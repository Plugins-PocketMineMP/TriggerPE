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

use pocketmine\Player;
use pocketmine\Server;
use function array_merge;
use function str_replace;

class CommandTrigger extends Trigger{

	/** @var string */
	protected $command;

	public function __construct(string $name, string $actionType, string $message, string $type, string $command){
		parent::__construct($name, $actionType, $message, $type);
		$this->command = $command;
	}

	public function getCommand() : string{
		return $this->command;
	}

	/**
	 * @param Player $player
	 * @param array $extraData 0 => command
	 * @return bool
	 */
	public function execute(Player $player, array $extraData = []) : bool{
		$command = str_replace("/", "", $extraData[0]);
		$found = false;
		foreach(Server::getInstance()->getCommandMap()->getCommands() as $c){
			if($c->getName() === $command)
				$found = true;
		}
		return !$found and $command === $this->command;
	}

	public function jsonSerialize() : array{
		return array_merge(
			parent::jsonSerialize(),
			["command" => $this->command]
		);
	}

	public static function jsonDeserialize(array $data) : CommandTrigger{
		return new CommandTrigger(
			$data["name"],
			$data["actionType"],
			$data["message"],
			$data["type"],
			$data["command"]
		);
	}
}