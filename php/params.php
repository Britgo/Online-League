<?php

//   Copyright 2009 John Collins

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
		$ret = mysql_query("select sc,val from params");
		if (!$ret)
			throw new ParamException(mysql_error());
		while ($row = mysql_fetch_assoc($ret)) {
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
		if (!mysql_query("delete from params"))
			throw new ParamException(mysql_error());
		mysql_query("insert into params (sc,val) values ('p', $this->Played)");
		mysql_query("insert into params (sc,val) values ('w', $this->Won)");
		mysql_query("insert into params (sc,val) values ('d', $this->Drawn)");
		mysql_query("insert into params (sc,val) values ('l', $this->Lost)");
		mysql_query("insert into params (sc,val) values ('f', $this->Forg)");
		mysql_query("insert into params (sc,val) values ('a', $this->Againstg)");
		mysql_query("insert into params (sc,val) values ('j', $this->Drawng)");
		mysql_query("insert into params (sc,val) values ('hd', $this->Hdiv)");
		mysql_query("insert into params (sc,val) values ('hr', $this->Hreduct)");
		mysql_query("insert into params (sc,val) values ('fz', $this->Rankfuzz)");
	}
}
?>
