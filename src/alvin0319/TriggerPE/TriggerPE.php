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

use alvin0319\TriggerPE\command\AreaTriggerCommand;
use alvin0319\TriggerPE\command\ClickTriggerCommand;
use alvin0319\TriggerPE\command\CommandTriggerCommand;
use alvin0319\TriggerPE\selector\SelectorFactory;
use BadFunctionCallException;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class TriggerPE extends PluginBase{

	/** @var TriggerPE */
	private static $instance = null;

	/** @var TriggerFactory */
	protected $triggerFactory = null;

	public static function message(CommandSender $sender, string $message) : void{
		$sender->sendMessage("§b§l[TriggerPE] §r§7" . $message);
	}

	public function onLoad() : void{
		self::$instance = $this;
	}

	public static function getInstance() : TriggerPE{
		if(!(self::$instance) instanceof TriggerPE){
			throw new BadFunctionCallException("Tried to get instance when plugin is not loaded.");
		}
		return self::$instance;
	}

	public function onEnable() : void{
		SelectorFactory::init();
		$this->triggerFactory = new TriggerFactory($this);
		$this->triggerFactory->initialize();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

		$this->getServer()->getCommandMap()->registerAll("triggerpe", [
			new AreaTriggerCommand(),
			new ClickTriggerCommand(),
			new CommandTriggerCommand()
		]);
	}

	public function onDisable() : void{
		self::$instance = null;
		$this->triggerFactory->save();
		$this->triggerFactory = null;
	}

	public function getTriggerFactory() : TriggerFactory{
		return $this->triggerFactory;
	}
}