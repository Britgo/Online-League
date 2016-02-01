<?php

//   Copyright 2016 John Collins

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.

class TeamException extends Exception {}

class TeamBase {
	public $Name;			// Team short name
	public $Description;	// Team full name
	public $Division;		// League division
	public $Playedm;		// Played matches
	public $Wonm;			// Won matches
	public $Drawnm;		// Drawn matches
	public $Lostm;			// Lost matches
	public $Wong;			// Won games
	public $Drawng;		// Drawn games
	public $Lostg;			// Lost games
	public $Sortrank;		// Ranking for league sort
	public $Playing;		// Playing in relevant season
	
	public function __construct($n = "") {
		if (strlen($n) != 0)
			$this->Name = $n;
		$this->Division = 1;
		$this->Sortrank = 0;
		$this->Playing = true;
	}
	
	public function fromget($gf) {
		$this->Name = $_GET[$gf];
		if (strlen($this->Name) == 0)
			throw new TeamException("Null name field"); 
	}
	
	public function queryname() {
		return mysql_real_escape_string($this->Name);
	}
	
	public function noquote() {
		$p = array('/"/', "/'/");
		$r = array("", "");
		return preg_replace($p, $r, $this->Name);
	}

	// Overridden in team.php not histteam.php
		
	public function display_name() {
		return htmlspecialchars($this->Name);
	}
	
	public function display_description() {
		return htmlspecialchars($this->Description);
	}
	
	// Trivial but room for expansion
	
	public function display_division() {
		return $this->Division;
	}
	
	public function is_same($tm) {
		return $this->Name == $tm->Name;
	}
}
?>
