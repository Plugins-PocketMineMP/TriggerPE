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

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use function array_merge;
use function explode;
use function floatval;
use function implode;

class ClickTrigger extends Trigger{

	/** @var Block */
	protected $clickPos;

	public function __construct(string $name, string $actionType, string $message, string $type, Block $clockPos){
		parent::__construct($name, $actionType, $message, $type);
		$this->clickPos = $clockPos;
	}

	/**
	 * @param Player $player
	 * @param array $extraData 0 => Clicked block
	 * @return bool
	 */
	public function execute(Player $player, array $extraData = []) : bool{
		/** @var Block $clicked */
		$clicked = $extraData[0];
		$need = $this->clickPos;

		if($need->equals($clicked) && $need->getId() === $clicked->getId() && $need->getDamage() === $clicked->getDamage()){
			return true;
		}
		return false;
	}

	public function getClickPos() : Block{
		return $this->clickPos;
	}

	public function jsonSerialize() : array{
		return array_merge(
			parent::jsonSerialize(),
			[
				"block" => [
					"id" => $this->clickPos->getId(),
					"meta" => $this->clickPos->getDamage(),
					"pos" => implode(":", [$this->clickPos->getX(), $this->clickPos->getY(), $this->clickPos->getZ(), $this->clickPos->getLevel()->getFolderName()])
				]
			]
		);
	}

	public static function jsonDeserialize(array $data) : ClickTrigger{
		[$x, $y, $z, $world] = explode(":", $data["block"]["pos"]);
		$block = BlockFactory::get($data["block"]["id"], $data["block"]["meta"]);
		$block->position(new Position(floatval($x), floatval($y), floatval($z), Server::getInstance()->getLevelByName($world)));
		return new ClickTrigger(
			$data["name"],
			$data["actionType"],
			$data["message"],
			$data["type"],
			$block
		);
	}

	public function equals(Trigger $that) : bool{
		return parent::equals($that) && ($that instanceof ClickTrigger) && ($this->getClickPos()->equals($that->getClickPos()));
	}
}