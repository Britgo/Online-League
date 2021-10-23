<?php
//   Copyright 2010-2016 John Collins

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

class HistMatch extends MatchBase {
	public  $Seas;			// Season object
	public  $Hteam;		// "Home" team (class object)
	public  $Ateam;		// "Away" team (class object)

	public function __construct($s, $in = 0, $d = 1) {
		parent::__construct($in, $d);
		$this->Seas = $s;
		$this->Hteam = new Histteam($s);
		$this->Ateam = new Histteam($s);
	}

	public function set_hometeam($h) {
		$this->Hteam = new Histteam($this->Seas, $h);
	}

	public function set_awayteam($a) {
		$this->Ateam = new Histteam($this->Seas, $a);
	}

	// Find out match ind from a page with ?hmi=nnn

	public function fromget() {
		$this->Ind = intval($_GET["hmi"]);
	}

	// From match ind get season ind and season

	public function getseason() {
		$ret = $Connection->query("SELECT seasind FROM histmatch WHERE ind={$this->Ind}");
		if (!$ret || $ret->num_rows != 1)
			throw new MatchException("Cannot read database for season ind");
		$row = $ret->fetch_array();
		try  {
			$this->Seas = new Season($row[0]);
			$this->Seas->fetchdets();
		}
		catch (SeasonException $e)  {
			throw new MatchException($e->getMessage());
		}
	}

	// For generation of query string with match ind in

	public function urlof() {
		return "hmi={$this->Ind}";
	}

	// Fetch the rest of the stuff relating to a match
	// apart FROM the teams

	public function fetchdets() {
		$q = $this->queryof();
		$ret = $Connection->query("SELECT divnum,hteam,ateam,matchdate,hwins,awins,draws,result,defaulted FROM histmatch WHERE $q");
		if (!$ret)
			throw new MatchException("Cannot read database for match $q");
		if ($ret->num_rows == 0)
			throw new MatchException("Cannot find hist match record {$this->Ind}");
		$row = $ret->fetch_assoc();
		$this->Division = $row["divnum"];
		$this->Hteam = new Histteam($this->Seas, $row["hteam"]);
		$this->Ateam = new Histteam($this->Seas, $row["ateam"]);
		$this->Date->fromtabrow($row);
		$this->Hwins = $row["hwins"];
		$this->Awins = $row["awins"];
		$this->Draws = $row["draws"];
		$this->Result = $row["result"];
		$this->Defaulted = $row["defaulted"];
	}

	// Fetch the game list (not including score)

	public function fetchgames() {
		$result = array();
		if  (!$this->Defaulted)  {
			$ret = $Connection->query("SELECT ind FROM game WHERE {$this->queryof('match')} ORDER BY ind");
			if (!$ret)
				throw new MatchException("Game read fail " . $Connection->error);
			try  {
				while ($row = $ret->fetch_array())  {
					$g = new Game($row[0], $this->Ind, $this->Division);
					$g->fetchhistdets($this->Seas);
					array_push($result, $g);
				}
			}
			catch (GameException $e) {
				throw new MatchException($e->getMessage());
			}
		}
		$this->Games = $result;
	}

	public function create() {
		$qhome = $this->Hteam->queryname();
		$qaway = $this->Ateam->queryname();
		$qdate = $this->Date->queryof();
		$qres = $Connection->real_escape_string($this->Result);
		$qdef = $this->Defaulted? 1: 0;
		$ret = $Connection->query("INSERT INTO histmatch (ind,divnum,hteam,ateam,matchdate,hwins,awins,draws,result,seasind,defaulted) VALUES ({$this->Ind},{$this->Division},'$qhome','$qaway','$qdate',{$this->Hwins},{$this->Awins},{$this->Draws},'$qres',{$this->Seas->Ind},$qdef)");
		if (!$ret)
			throw new MatchException($Connection->error);
	}
}

// Return the number of matches for a division

function hist_count_matches_for($s, $divnum) {
	$ret = $Connection->query("SELECT COUNT(*) FROM histmatch WHERE seasind={$s->Ind} and divnum=$divnum");
	if (!$ret || $ret->num_rows == 0)
		return  0;
	$row = $ret->fetch_array();
	return $row[0];
}

?>
