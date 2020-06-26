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

use pocketmine\Player;

final class TriggerQueue{

	public static $createQueue = [];

	public static function addCreateQueue(Player $player, string $name, string $message, string $type, string $actionType) : void{
		self::$createQueue[$player->getName()] = [
			"name" => $name,
			"message" => $message,
			"type" => $type,
			"actionType" => $actionType
		];
	}

	public static function setQueue(Player $player, array $data) : void{
		self::$createQueue[$player->getName()] = $data;
	}

	public static function removeCreateQueue(Player $player) : void{
		if(isset(self::$createQueue[$player->getName()]))
			unset(self::$createQueue[$player->getName()]);
	}

	public static function getQueue(Player $player) : ?array{
		return self::$createQueue[$player->getName()] ?? null;
	}
}