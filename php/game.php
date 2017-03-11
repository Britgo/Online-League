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

class GameException extends Exception {}

class Game {
	private $Ind;			// Ind from database
	public  $Division;	// Division number
	public  $Date;			// Matchdate class object
	public  $Wteam;		// "White" team (class object)
	public  $Bteam;		// "Black" team (class object)
	public  $Wplayer;		// White player object
	public  $Bplayer;		// Black player object
	public  $Result;		// N (not played) W white B black J Jigo
	public  $Resultdet;	// Score as W+10.5 or B+R etc
	public  $Sgf;			// Sgf file
	public  $Matchind;	// Ind in match table
	public  $League;		// Which league T=team I=individual P=pair
	
	public function __construct($in = 0, $min = 0, $d = 1, $l = 'T') {
		$this->Ind = $in;
		$this->Division = $d;
		$this->Date = new Matchdate();
		//  We don't allocate team structures any more as we can do this in pieces
		//  $this->Wteam = new Team();
		//  $this->Bteam = new Team();
		$this->Result = 'N';
		$this->Resultdet = "";
		$this->Sgf = "";
		$this->Matchind = $min;
		$this->League = $l;
		switch  ($l)  {
		case  'I':
			$this->Wteam = new Team('Individual');
			$this->Bteam = new Team('Individual');
			break;
		case  'P':
			$this->Wteam = new Team('Pairs');
			$this->Bteam = new Team('Pairs');
			break;
		default:
			$this->Wteam = $this->Bteam = Null;
		}
	}
	
	public function query_ind() {
		return $this->Ind;
	}

	public function fromget() {
		$this->Ind = intval($_GET["gn"]);
	}
	
	public function frompost($prefix = "") {
		$this->Ind = $_POST["${prefix}gn"];
		if ($this->Ind == 0)
			throw new GameException("Null post ind field"); 
	}
	
	public function urlof() {
		return "gn={$this->Ind}";
	}
	
	public function queryof($prefix="") {
		return "{$prefix}ind={$this->Ind}";
	}
	
	public function save_hidden($prefix = "") {
		$f = $this->Ind;
		return "<input type=\"hidden\" name=\"${prefix}gn\" value=\"$f\">";
	}
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select divnum,matchdate,wteam,bteam,wfirst,wlast,bfirst,blast,result,reshow,matchind,league from game where $q");
		if (!$ret)
			throw new GameException("Cannot read database for game $q");
		if (mysql_num_rows($ret) == 0)
			throw new GameException("Cannot find game record {$this->Ind}");
		$row = mysql_fetch_assoc($ret);
		$this->Division = $row["divnum"];
		$this->Date->fromtabrow($row);
		$this->League = $row["league"];

		// We may only have allocated one side of this game
		// If this is the case the unallocated team name will be blank
		// But protect against mangled names

		try  {
			switch ($this->League)  {
			case 'T':
				$wt = $row['wteam'];		
				if (strlen($wt) != 0)  {
					$this->Wteam = new Team($wt);
					$this->Wplayer = new Player($row["wfirst"], $row["wlast"]);
					$this->Wplayer->fetchdets();
				}
				$bt = $row["bteam"];
				if (strlen($bt) != 0)  {
					$this->Bteam = new Team($bt);
					$this->Bplayer = new Player($row["bfirst"], $row["blast"]);
					$this->Bplayer->fetchdets();
				}
				$this->Matchind = $row["matchind"];
				break;
			default:
				$t = $this->League == 'P'? "Pairs": "Individual";
				$this->Wteam = new Team($t);
				$this->Bteam = new Team($t);
				$this->Wplayer = new Player($row["wfirst"], $row["wlast"]);
				$this->Wplayer->fetchdets();
				$this->Bplayer = new Player($row["bfirst"], $row["blast"]);
				$this->Bplayer->fetchdets();
				break;
			}
		}
		catch (PlayerException $e) {
			throw new GameException($e->getMessage());
		}
		
		$this->Result = $row["result"];
		$this->Resultdet = $row["reshow"];	
	}
	
	public function fetchhistdets($seas) {
		$q = $this->queryof();
		$ret = mysql_query("select divnum,matchdate,wteam,bteam,wfirst,wlast,bfirst,blast,result,reshow,matchind,league from game where $q");
		if (!$ret)
			throw new GameException("Cannot read database for game $q");
		if (mysql_num_rows($ret) == 0)
			throw new GameException("Cannot find game record {$this->Ind}");
		$row = mysql_fetch_assoc($ret);
		$this->Division = $row["divnum"];
		$this->Date->fromtabrow($row);
		$this->League = $row["league"];
		switch ($this->League) {
		case  'T':
			$wt = $row['wteam'];
			$bt = $row['bteam'];
			break;
		case  'P':
			$wt = $bt = "Pairs";
			break;
		default:
			$wt = $bt = "Individual";
			break;
		}
		$this->Wteam = new Histteam($seas, $wt);
		$this->Bteam = new Histteam($seas, $bt);
		$this->Wplayer = new Player($row["wfirst"], $row["wlast"]);
		$this->Bplayer = new Player($row["bfirst"], $row["blast"]);
		$this->Result = $row["result"];
		$this->Resultdet = $row["reshow"];
		$this->Matchind = $row["matchind"];
	}
	
	public function game_name() {
		return "bga_gm{$this->Ind}.sgf";
	}
	
	public function create_game() {
	
		// Set these to some numeric value in case not defined
		// Only invoked for team league.

		$qwrank = 0;
		$qbrank = 0;
		
		//  Teams are not defined if only partly allocated
		
		if ($this->Wteam) {
			$qwteam = $this->Wteam->queryname();
			$qwfirst = $this->Wplayer->queryfirst();
			$qwlast = $this->Wplayer->querylast();
			$qwrank = $this->Wplayer->Rank->Rankvalue;
		}
		if ($this->Bteam) {
			$qbteam = $this->Bteam->queryname();
			$qbfirst = $this->Bplayer->queryfirst();
			$qblast = $this->Bplayer->querylast();
			$qbrank = $this->Bplayer->Rank->Rankvalue;
		}
		
		$qdate = $this->Date->queryof();
		
		// These are always going to be 'N' and null but let's be consistent.
		// (Except for individual league where we get them right first time)
		
		$qres = mysql_real_escape_string($this->Result);
		$qresdat = mysql_real_escape_string($this->Resultdet);
		$qsgf = mysql_real_escape_string($this->Sgf);
		$qmi = $this->Matchind;
		
		if (!mysql_query("insert into game (matchdate,wfirst,wlast,wteam,wrank,bfirst,blast,bteam,brank,result,reshow,sgf,matchind,divnum,league) values ('$qdate','$qwfirst','$qwlast','$qwteam',$qwrank,'$qbfirst','$qblast','$qbteam',$qbrank,'$qres','$qresdat','$qsgf',{$this->Matchind},{$this->Division},'{$this->League}')"))
			throw new GameException(mysql_error());
		$ret = mysql_query("select last_insert_id()");
		if (!$ret || mysql_num_rows($ret) == 0)
			throw new GameException("Cannot locate game record id");
		$row = mysql_fetch_array($ret);
		$this->Ind = $row[0];					
	}
	
	public function update_players()  {

		// Set these to some numeric value in case not defined

		$qwrank = 0;
		$qbrank = 0;

		//  Teams are not defined if only partly allocated
		
		if ($this->Wteam) {
			$qwteam = $this->Wteam->queryname();
			$qwfirst = $this->Wplayer->queryfirst();
			$qwlast = $this->Wplayer->querylast();
			$qwrank = $this->Wplayer->Rank->Rankvalue;
		}
		if ($this->Bteam) {
			$qbteam = $this->Bteam->queryname();
			$qbfirst = $this->Bplayer->queryfirst();
			$qblast = $this->Bplayer->querylast();
			$qbrank = $this->Bplayer->Rank->Rankvalue;
		}
		
		if (!mysql_query("update game set wfirst='$qwfirst',wlast='$qwlast',bfirst='$qbfirst',blast='$qblast',wteam='$qwteam',bteam='$qbteam',wrank=$qwrank,brank=$qbrank where {$this->queryof()}"))
			throw new GameException(mysql_error()); 
	}
	
	// Indicate if both teams are allocated
	
	public function is_allocated() {
		return $this->Wteam && $this->Bteam;
	}

	// Indicate if specified team is allocated
		
	public function team_allocated($t) {
		return ($this->Wteam && $this->Wteam->is_same($t)) ||
				 ($this->Bteam && $this->Bteam->is_same($t));
	}
	
	private function has_sgf() {
		$ret = mysql_query("select length(sgf) from game where {$this->queryof()}");
		if (!$ret)
			return false;
		$row = mysql_fetch_array($ret);
		return $row[0] != 0;
	}
	
	public function date_played() {
		if ($this->Result == 'N')
			return "";
		return $this->Date->disp_abbrev();
	}
	
	public function playerin($u)  {
		try {
			$pp = new Player($u);
			return $pp->is_same($this->Wplayer) || $pp->is_same($this->Bplayer);
		}
		catch (PlayerException $e) {
			return false;
		}
	}

	public function reversecolours() {
		// For when colours are reversed - assumes everything loaded OK
		$tmp = $this->Wteam;
		$this->Wteam = $this->Bteam;
		$this->Bteam = $tmp;
		$tmp = $this->Wplayer;
		$this->Wplayer = $this->Bplayer;
		$this->Bplayer = $tmp;
		$qwteam = $this->Wteam->queryname();
		$qwfirst = $this->Wplayer->queryfirst();
		$qwlast = $this->Wplayer->querylast();
		$qwrank = $this->Wplayer->Rank->Rankvalue;
		$qbteam = $this->Bteam->queryname();
		$qbfirst = $this->Bplayer->queryfirst();
		$qblast = $this->Bplayer->querylast();
		$qbrank = $this->Bplayer->Rank->Rankvalue;
		if (!mysql_query("update game set wfirst='$qwfirst',wlast='$qwlast',bfirst='$qbfirst',blast='$qblast',wteam='$qwteam',bteam='$qbteam',wrank=$qwrank,brank=$qbrank where {$this->queryof()}"))
			throw new GameException(mysql_error()); 
	}

	public function display_result($addunpl = false) {
		if ($this->Result == 'N')  {
			if  ($addunpl)
				return  "<a href=\"addresult.php?{$this->urlof()}\">Add Result</a>";
			return  "Not played";
		}
		if (strlen($this->Resultdet) != 0)
			$res = htmlspecialchars($this->Resultdet);
		else  switch  ($this->Result) {
		case 'W':
			$res = "White Win";
			break;
		case 'B':
			$res = "Black Win";
			break;
		case 'J':
			$res = "Jigo";
			break;
		}
		if ($this->has_sgf())
			$res = "<a href=\"downloadsgf.php?{$this->urlof()}\">$res</a>";
		return $res;
	}
	
	public function reset_date($dat) {
		$this->Date = $dat;
		mysql_query("update game set matchdate='{$dat->queryof()}' where {$this->queryof()}"); 
	}
	
	public function adj_match($mtch, $mult) {
		switch ($this->Result) {
		default:
			return;
		case 'J':
			$mtch->Draws += $mult;
			break;
		case 'W':	
			if ($this->Wteam->is_same($mtch->Hteam))
				$mtch->Hwins += $mult;
			else
				$mtch->Awins += $mult;
			break;
		case 'B':
			if ($this->Wteam->is_same($mtch->Hteam))
				$mtch->Awins += $mult;
			else
				$mtch->Hwins += $mult;
			break;
		}
		// If we have a result, delete any messages specific to that game
		if  ($mult > 0)
			mysql_query("delete from message where {$this->queryof('game')}");
	}
	
	//  Setup Resultdet as W+whatever or B+whatever or Jigo

	public function setup_restype($res, $restype) {
		if (preg_match('/\d+/', $restype))
			$restype .= '.5';
		if ($res != 'J')
			$restype = "$res+$restype";
		else
			$restype = "Jigo";
		$this->Resultdet = $restype;
		$this->Result = $res;
	}
	
	//  This is for recording a result and adjusting the match details
	//  Only valid for team league.
	
	public function set_result($res, $restype) {
		$mtch = new Match($this->Matchind);
		$mtch->fetchdets();
		// Undo whatever we had before (to cope with corrections
		$this->adj_match($mtch, -1);
		// Now set the new values
		$this->setup_restype($res, $restype);
		// Update match accordingly
		$this->adj_match($mtch, 1);
		$mtch->updscore();
		$qres = mysql_real_escape_string($res);
		$qrest = mysql_real_escape_string($this->Resultdet);
		mysql_query("update game set result='$qres',reshow='$qrest' where {$this->queryof()}");
		return $mtch;	// For benefit of news		
	}
	
	// Delete wrongly entered result
	
	public function delete_result() {
		if ($this->League == 'I')  {
			mysql_query("delete from game where {$this->queryof()}");
		}
		else  {
			$mtch = new Match($this->Matchind);
			$mtch->fetchdets();
			$this->adj_match($mtch, -1);
			$this->Result = 'N';
			$this->Resultdet = '';
			$mtch->updscore();
			mysql_query("update game set result='N',reshow='',sgf='' where {$this->queryof()}");
		}
	}
	
	public function get_sgf() {
		$ret = mysql_query("select sgf from game where {$this->queryof()}");
		if (!$ret  ||  mysql_num_rows($ret) == 0)
			return;
		$row = mysql_fetch_array($ret);
		$this->Sgf = $row[0];
	}
	
	public function set_sgf($sgfdata) {
		$qsgfdata = mysql_real_escape_string($sgfdata);
		mysql_query("update game set sgf='$qsgfdata' where {$this->queryof()}");
		$this->Sgf = $sgfdata;
	}
	
	public function set_current($v = false, $si = 0) {
		$vi = $v? 1: 0;
		mysql_query("update game set current=$vi,seasind=$si where {$this->queryof()}");
	}
}

function list_nosgf_games() {
	$result = array();
	$ret = mysql_query("select ind from game where result!='N' and length(sgf)=0 and current=1 order by matchdate,wrank desc,brank desc");
	if ($ret) {
		while ($row = mysql_fetch_array($ret)) {
			$r = new game($row[0]);
			$r->fetchdets();
			array_push($result, $r);
		}
	}
	return $result;
}

function list_played_games() {
	$result = array();
	// Fix this to only do current season games (only used in fixres.php)
	$ret = mysql_query("select ind from game where result!='N' and seasind=0 order by matchdate,wlast,wfirst,blast,bfirst");
	if ($ret) {
		while ($row = mysql_fetch_array($ret)) {
			$r = new game($row[0]);
			$r->fetchdets();
			array_push($result, $r);
		}
	}
	return $result;
}
?>
