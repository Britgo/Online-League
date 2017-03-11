<?php
//   Copyright 2010-2016 John Collins

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

include 'matchbase.php';

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
		$ret = mysql_query("select seasind from histmatch where ind={$this->Ind}");
		if (!$ret || mysql_num_rows($ret) != 1)
			throw new MatchException("Cannot read database for season ind");
		$row = mysql_fetch_array($ret);
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
	// apart from the teams
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select divnum,hteam,ateam,matchdate,hwins,awins,draws,result,defaulted from histmatch where $q");
		if (!$ret)
			throw new MatchException("Cannot read database for match $q");
		if (mysql_num_rows($ret) == 0)
			throw new MatchException("Cannot find hist match record {$this->Ind}");
		$row = mysql_fetch_assoc($ret);
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
			$ret = mysql_query("select ind from game where {$this->queryof('match')} order by ind");
			if (!$ret)
				throw new MatchException("Game read fail " . mysql_error());
			try  {
				while ($row = mysql_fetch_array($ret))  {
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
		$qres = mysql_real_escape_string($this->Result);
		$qdef = $this->Defaulted? 1: 0;
		$ret = mysql_query("insert into histmatch (ind,divnum,hteam,ateam,matchdate,hwins,awins,draws,result,seasind,defaulted) values ({$this->Ind},{$this->Division},'$qhome','$qaway','$qdate',{$this->Hwins},{$this->Awins},{$this->Draws},'$qres',{$this->Seas->Ind},$qdef)");
		if (!$ret)
			throw new MatchException(mysql_error());
	}
}

// Return the number of matches for a division

function hist_count_matches_for($s, $divnum) {
	$ret = mysql_query("select count(*) from histmatch where seasind={$s->Ind} and divnum=$divnum");
	if (!$ret || mysql_num_rows($ret) == 0)
		return  0;
	$row = mysql_fetch_array($ret);
	return $row[0];
}

?>
