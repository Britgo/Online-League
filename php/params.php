<?php

//   Copyright 2009-2021 John Collins

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

class ParamException extends Exception {}

class Params  {
	public $Played;
	public $Won;
	public $Drawn;
	public $Lost;
	public $Forg;
	public $Againstg;
	public $Drawng;
	public $Hdiv;
	public $Hreduct;
	public $Rankfuzz;

	public function __construct() {
		$this->Played = 0;
		$this->Won = 100;
		$this->Drawn = 50;
		$this->Lost = 0;
		$this->Forg = 1;
		$this->Againstg = 0;
		$this->Drawng = 0.5;
		$this->Hdiv = 1000;
		$this->Hreduct = 0;
		$this->Rankfuzz = 0;
	}

	public function fetchvalues() {
		global $Connection;
		$ret = $Connection->query("SELECT sc,val FROM params");
		if (!$ret)
			throw new ParamException($Connection->error);
		while ($row = $ret->fetch_assoc()) {
			$v = $row["val"];
			switch ($row["sc"])  {
			case 'p':
				$this->Played = $v;
				break;
			case 'w':
				$this->Won = $v;
				break;
			case 'd':
				$this->Drawn = $v;
				break;
			case 'l':
				$this->Lost = $v;
				break;
			case 'f':
				$this->Forg = $v;
				break;
			case 'a':
				$this->Againstg = $v;
				break;
			case 'j':
				$this->Drawng = $v;
				break;
			case 'hd':
				$this->Hdiv = $v;
				break;
			case 'hr':
				$this->Hreduct = $v;
				break;
			case 'fz':
				$this->Rankfuzz = $v;
				break;
			}
		}
	}

	public function putvalues() {
		global $Connection;
		if (!$Connection->query("DELETE FROM params"))
			throw new ParamException($Connection->error);
		$Connection->query("INSERT INTO params (sc,val) VALUES ('p', $this->Played)");
		$Connection->query("INSERT INTO params (sc,val) VALUES ('w', $this->Won)");
		$Connection->query("INSERT INTO params (sc,val) VALUES ('d', $this->Drawn)");
		$Connection->query("INSERT INTO params (sc,val) VALUES ('l', $this->Lost)");
		$Connection->query("INSERT INTO params (sc,val) VALUES ('f', $this->Forg)");
		$Connection->query("INSERT INTO params (sc,val) VALUES ('a', $this->Againstg)");
		$Connection->query("INSERT INTO params (sc,val) VALUES ('j', $this->Drawng)");
		$Connection->query("INSERT INTO params (sc,val) VALUES ('hd', $this->Hdiv)");
		$Connection->query("INSERT INTO params (sc,val) VALUES ('hr', $this->Hreduct)");
		$Connection->query("INSERT INTO params (sc,val) VALUES ('fz', $this->Rankfuzz)");
	}
}
?>
