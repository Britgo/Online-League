<?php
//   Copyright 2009-2016 John Collins

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

// For convenience (and easy later extension) we have a "home team"
// and an "away team" on each match.

class Match extends MatchBase {
	public  $Hteam;		// "Home" team (class object)
	public  $Ateam;		// "Away" team (class object)
	public  $Slackdays;	// Days to arrange match
	
	public function __construct($in = 0, $d = 1) {
		parent::__construct($in, $d);
		$this->Hteam = new Team();
		$this->Ateam = new Team();
		$this->Slackdays = 2;
	}

	public function set_hometeam($h) {
		$this->Hteam = new Team($h);
	}
	
	public function set_awayteam($a) {
		$this->Ateam = new Team($a);
	}

	// Find out match ind from a page with ?mi=nnn
		
	public function fromget() {
		$this->Ind = intval($_GET["mi"]);
	}

	// For when we leave a hidden input in form telling
	// the next page which prefix we're talking about
		
	public function frompost($prefix = "") {
		$this->Ind = $_POST["${prefix}mi"];
		if ($this->Ind == 0)
			throw new MatchException("Null post ind field"); 
	}

	// For saving input in form
	
	public function save_hidden($prefix = "") {
		$f = $this->Ind;
		return "<input type=\"hidden\" name=\"${prefix}mi\" value=\"$f\">";
	}

	// For generation of query string with match ind in
		
	public function urlof() {
		return "mi={$this->Ind}";
	}
	
	// For leaving a link for news etc
	
	public function showmatch() {
		return "showmtch.php?{$this->urlof()}";
	}

	// Fetch the rest of the stuff relating to a match
	// apart from the teams
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select divnum,hteam,ateam,matchdate,hwins,awins,draws,result,slackdays,defaulted from lgmatch where $q");
		if (!$ret)
			throw new MatchException("Cannot read database for match $q");
		if (mysql_num_rows($ret) == 0)  {
			if ($this->Ind == 0)
				throw new MatchException("No match id");
			else
				throw new MatchException("Cannot find match record {$this->Ind}", true, $this->Ind);
		}
		$row = mysql_fetch_assoc($ret);
		$this->Division = $row["divnum"];
		$this->Hteam = new Team($row["hteam"]);
		$this->Ateam = new Team($row["ateam"]);
		$this->Date->fromtabrow($row);
		$this->Slackdays = $row["slackdays"];
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
					$g->fetchdets();
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
		$ret = mysql_query("insert into lgmatch (divnum,hteam,ateam,matchdate,hwins,awins,draws,result,slackdays) values ({$this->Division},'$qhome','$qaway','$qdate',{$this->Hwins},{$this->Awins},{$this->Draws},'$qres',{$this->Slackdays})");
		if (!$ret)
			throw new MatchException(mysql_error());
		$ret = mysql_query("select last_insert_id()");
		if (!$ret || mysql_num_rows($ret) == 0)
			throw new MatchException("Cannot locate match record id");
		$row = mysql_fetch_array($ret);
		$this->Ind = $row[0];
	}
	
	public function dateupdate() {
		$qdate = $this->Date->queryof();
		$ret = mysql_query("update lgmatch set matchdate='$qdate',slackdays={$this->Slackdays} where {$this->queryof()}");
		if (!$ret)
			throw new MatchException(mysql_error());
		mysql_query("update game set matchdate='$qdate' where {$this->queryof('match')} and result='N'");
		foreach ($this->Games as $g) {
			if ($g->Result == 'N')
				$g->Date = $this->Date;
		}
	}
	
	public function delmatch() {
		$ret = mysql_query("delete from lgmatch where {$this->queryof()}");
		if (!$ret)
			throw new MatchException(mysql_error());
		//  We currently don't allow deletion of played games so this shouldn't
		//  lose anything
		mysql_query("delete from game where {$this->queryof('match')}");
	}
	
	// Adjust result of match for incoming score
	
	public function updscore() {
		$tot = $this->Hwins + $this->Awins + $this->Draws;
		$delmsgs = false;
		if ($tot <= 0)
			$this->Result = 'N';
		else  if ($tot < 3)
			$this->Result = 'P';
		else if ($this->Hwins == $this->Awins) {
			$this->Result = 'D';
			$delmsgs = true;
		}
		else if ($this->Hwins < $this->Awins) {
			$this->Result = 'A';
			$delmsgs = true;
		}
		else  {
			$this->Result = 'H';
			$delmsgs = true;
		}
		mysql_query("update lgmatch set result='{$this->Result}',hwins={$this->Hwins},awins={$this->Awins},draws={$this->Draws} where {$this->queryof()}");
		if ($delmsgs)
			mysql_query("delete from message where {$this->queryof('match')}");
	}
	
	public function set_defaulted($hora) {
		switch  ($hora)  {
		default:
			return;
		case  'H':
			$this->Result= 'A';
			$this->Hwins = 0;
			$this->Draws = 0;
			$this->Awins = 3;
			break;
		case  'A':
			$this->Result = 'H';
			$this->Hwins = 3;
			$this->Draws = 0;
			$this->Awins = 0;
			break;
		}
		$this->Defaulted = true;
		mysql_query("update lgmatch set defaulted=1,result='{$this->Result}',hwins={$this->Hwins},awins={$this->Awins},draws={$this->Draws} where {$this->queryof()}");
		mysql_query("delete from game where {$this->queryof('match')}");
	}	

	// Push out a selection option for the number of spare days
			
	public function slackdopt()
	{
		print "<select name=\"slackd\">\n";
		for ($i = 1;  $i <= 21; $i++) {
			if ($i == $this->Slackdays)
				print "<option selected>$i</option>\n";
			else
				print "<option>$i</option>\n";
		}
		print "</select>\n";
	}
	
	// Is given guy a captain for this match
	// Return N if not H for "Home" A for "away"
	
	public function is_captain($name) {
		try  {
			$possp = new Player($name);
		}
		catch (PlayerException $e) {
			return 'N';
		}
		if ($this->Hteam->Captain->is_same($possp))  {
			if  ($this->Ateam->Captain->is_same($possp))
				return  'B';
			return 'H';
		}
		if ($this->Ateam->Captain->is_same($possp))
			return 'A';
		return 'N';
	}
}

// Return the number of matches for a division

function count_matches_for($divnum) {
	$ret = mysql_query("select count(*) from lgmatch where divnum=$divnum");
	if (!$ret || mysql_num_rows($ret) == 0)
		return  0;
	$row = mysql_fetch_array($ret);
	return $row[0];
}

?>
