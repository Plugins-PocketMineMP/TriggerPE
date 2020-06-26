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
use function array_merge;
use function max;
use function min;

class AreaTrigger extends Trigger{

	protected $posData = [];

	public function __construct(string $name, string $actionType, string $message, string $type, array $posData){
		parent::__construct($name, $actionType, $message, $type);
		$this->posData = $posData;
	}

	public function jsonSerialize() : array{
		return array_merge(
			parent::jsonSerialize(),
			["posData" => $this->posData]
		);
	}


	public static function jsonDeserialize(array $data) : AreaTrigger{
		return new AreaTrigger(
			$data["name"],
			$data["actionType"],
			$data["message"],
			$data["type"],
			$data["posData"]
		);
	}

	public function execute(Player $player, array $extraData = []) : bool{
		return (
		(
			$player->getX() >= $this->getMinX() && $player->getX() <= $this->getMaxX()
		) && (
			$player->getZ() >= $this->getMinZ() && $player->getZ() <= $this->getMaxZ()
		) && (
			$player->getLevel()->getFolderName() === $this->getWorld()
		)
		);
	}

	public function getMinX() : int{
		return (int) min($this->posData["x1"], $this->posData["x2"]);
	}

	public function getMaxX() : int{
		return (int) max($this->posData["x1"], $this->posData["x2"]);
	}

	public function getMinZ() : int{
		return (int) min($this->posData["z1"], $this->posData["z2"]);
	}

	public function getMaxZ() : int{
		return (int) max($this->posData["z1"], $this->posData["z2"]);
	}

	public function getWorld() : string{
		return $this->posData["world"];
	}

	public function getPosData() : array{
		return $this->posData;
	}

	public function equals(Trigger $that) : bool{
		return parent::equals($that) && ($that instanceof AreaTrigger) && (
			$this->getMinX() === $that->getMinX() && $this->getMaxX() === $that->getMaxX() && $this->getMinZ() === $that->getMinZ() && $this->getMaxZ() === $that->getMaxZ()
		);
	}
}