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
use function array_map;
use function array_values;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function in_array;
use function is_a;
use function json_decode;
use function json_encode;

class TriggerFactory{

	/** @var Trigger[] */
	protected $triggers = [];

	protected $plugin;

	protected $registeredTriggers = [];

	public function __construct(TriggerPE $plugin){
		$this->plugin = $plugin;
	}

	public function registerTrigger(string $class) : void{
		if(is_a($class, Trigger::class, true)){
			if(!in_array($class, $this->registeredTriggers)){
				$this->registeredTriggers[] = $class;
			}
		}
	}

	/**
	 * @param Trigger|string $class
	 * @param array $data
	 * @return Trigger|null
	 */
	public function createTrigger(string $class, array $data) : ?Trigger{
		if(in_array($class, $this->registeredTriggers)){
			return $class::jsonDeserialize($data);
		}
		return null;
	}

	public function initialize() : void{
		$this->registerTrigger(AreaTrigger::class);
		$this->registerTrigger(ClickTrigger::class);
		$this->registerTrigger(CommandTrigger::class);
		$plugin = $this->plugin;

		if(file_exists($path = $plugin->getDataFolder() . "triggers.json")){
			$data = json_decode(file_get_contents($path), true);
			foreach($data as $name => $triggerData){
				switch($triggerData["type"]){
					case Trigger::TYPE_COMMAND:
						$trigger = CommandTrigger::jsonDeserialize($triggerData);
						break;
					case Trigger::TYPE_CLICK:
						$trigger = ClickTrigger::jsonDeserialize($triggerData);
						break;
					case Trigger::TYPE_AREA:
						$trigger = AreaTrigger::jsonDeserialize($triggerData);
						break;
				}
				if(isset($trigger)){
					$this->addTrigger($trigger);
				}
			}
		}
	}

	public function save() : void{
		$plugin = $this->plugin;

		$data = [];
		foreach($this->getTriggers() as $trigger){
			$data[$trigger->getName()] = $trigger->jsonSerialize();
		}
		file_put_contents($plugin->getDataFolder() . "triggers.json", json_encode($data));
	}

	/**
	 * @return Trigger[]
	 */
	public function getTriggers() : array{
		return array_values($this->triggers);
	}

	public function getTriggerNames() : array{
		return array_map(function(Trigger $trigger) : string{
			return $trigger->getName();
		}, $this->getTriggers());
	}

	public function getTrigger(string $name) : ?Trigger{
		return $this->triggers[$name] ?? null;
	}

	public function addTrigger(Trigger $trigger) : void{
		$this->triggers[$trigger->getName()] = $trigger;
	}

	public function removeTrigger(Trigger $trigger) : void{
		if(isset($this->triggers[$trigger->getName()])){
			unset($this->triggers[$trigger->getName()]);
		}
	}
}