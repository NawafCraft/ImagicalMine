<?php

/*
 *
 *  _                       _           _ __  __ _             
 * (_)                     (_)         | |  \/  (_)            
 *  _ _ __ ___   __ _  __ _ _  ___ __ _| | \  / |_ _ __   ___  
 * | | '_ ` _ \ / _` |/ _` | |/ __/ _` | | |\/| | | '_ \ / _ \ 
 * | | | | | | | (_| | (_| | | (_| (_| | | |  | | | | | |  __/ 
 * |_|_| |_| |_|\__,_|\__, |_|\___\__,_|_|_|  |_|_|_| |_|\___| 
 *                     __/ |                                   
 *                    |___/                                                                     
 * 
 * This program is a third party build by ImagicalMine.
 * 
 * PocketMine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ImagicalMine Team
 * @link http://forums.imagicalcorp.ml/
 * 
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;

use pocketmine\Player;
use pocketmine\math\Vector3;

class TripwireHook extends Flowable{
	protected $id = self::TRIPWIRE_HOOK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : int{
		return 0;
	}

	public function isSolid() : bool{
		return false;
	}

	public function getName() : string{
		return "Tripwire Hook";
	}

	public function getBoundingBox(){
		return null;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null) : bool{
		if($face !== 0 && $face !== 1){
			$ret = $this->setFacingDirection($face);
			$this->getLevel()->setBlock($block, $this, true);
			return $ret;
		}
		
		return false;
	}
	
	public function getDrops(Item $item) : array{
		return [
			[Item::TRIPWIRE_HOOK, 0, 1],
		];
	}


	/**
	 * Test if tripwire is connected
	 *
	 * @return true if connected, false if not
	 */
	public function isConnected() {
		return ($this->getDamage() & 0x04) != 0;
	}
	
	/**
	 * Set tripwire connection state
	 *
	 * @param connected - true if connected, false if not
	 */
	public function setConnected($connected) {
		$dat = $this->getDamage() & (0x08 | 0x03);
		if ($connected) {
			$dat |= 0x04;
		}
		$this->setDamage($dat);
	}
	
	/**
	 * Test if hook is currently activated
	 *
	 * @return true if activated, false if not
	 */
	public function isActivated() {
		return ($this->getDamage() & 0x08) != 0;
	}
	
	/**
	 * Set hook activated state
	 *
	 * @param act - true if activated, false if not
	 */
	public function setActivated($act) {
		$dat = $this->getDamage() & (0x04 | 0x03);
		if ($act) {
			$dat |= 0x08;
		}
		$this->setDamage($dat);
	}
	
	public function setFacingDirection($face) {
		$dat = $this->getDamage() & 0x0C;
		switch ($face) {
			case Vector3::SIDE_WEST:
				$dat |= 0x01;
				break;
			case Vector3::SIDE_NORTH:
				$dat |= 0x02;
				break;
			case Vector3::SIDE_EAST:
				$dat |= 0x03;
				break;
			case Vector3::SIDE_SOUTH:
			default:
				return false;
				break;
		}
		$this->setDamage($dat);
	}
	
	public function getAttachedFace() {
		switch ($this->getDamage() & 0x03) {
			case 0:
				return Vector3::SIDE_NORTH;
			case 1:
				return Vector3::SIDE_EAST;
			case 2:
				return Vector3::SIDE_SOUTH;
			case 3:
				return Vector3::SIDE_WEST;
		}
		return null;
	}
	
	public function isPowered() {
		return $this->isActivated();
	}
	
	public function __toString() : string{
		return $this->getName() . " facing " . $this->getFacing() . ($this->isActivated()?" Activated":"") . ($this->isConnected()?" Connected":"");
	}
}