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
namespace alvin0319\TriggerPE\selector;

use pocketmine\Player;
use function str_replace;
use function strpos;

abstract class Selector{

	abstract public function getSymbol() : string;

	abstract public function find(Player $player) : ?Player;

	public function replaceMessage(Player $player, string $message) : string{
		return str_replace("@" . $this->getSymbol(), $player->getName(), $message);
	}

	public function hasSymbol(string $message) : bool{
		return strpos($message, "@" . $this->getSymbol()) !== false;
	}
}