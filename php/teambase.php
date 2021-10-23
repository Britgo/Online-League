<?php

//   Copyright 2016-2021 John Collins

// *********************************************************************
// Please do not edit the live file directly as it will break the "Git"
// mechanism to update the live files automatically when a new version
// is pushed. Thanks!
// *********************************************************************

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
		$this->Playedm = 0;
		$this->Wonm = 0;
		$this->Drawnm = 0;
		$this->Lostm = 0;
		$this->Wong = 0;
		$this->Drawng = 0;
		$this->Lostg = 0;
		$this->Playing = true;
	}

	public function fromget($gf) {
		$this->Name = $_GET[$gf];
		if (strlen($this->Name) == 0)
			throw new TeamException("Null name field");
	}

	public function queryname() {
		return $Connection->real_escape_string($this->Name);
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
