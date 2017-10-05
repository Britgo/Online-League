<?php

//   Copyright 2009 John Collins

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

include 'teambase.php';

class Team extends Teambase  {
	public $Captain;		// A player object
	public $Paid;			// Paid
	public $Nonbga;		// Number of non-BGA members
	public $Subs;			// Subscription
	public $Playing;		// Playing
	
	public function __construct($n = "") {
		parent::__construct($n);
		$this->Subs = 0;
		$this->Nonbga = 0;
	}
	
	public function fromget() {
		parent::fromget("tn");
	}

	public function frompost($prefix = "") {
		$this->Name = $_POST["${prefix}tn"];
		if (strlen($this->Name) == 0)
			throw new TeamException("Null post name field"); 
	}				
	
	public function queryof($colname = "name") {
		$qn = mysql_real_escape_string($this->Name);
		return "$colname='$qn'";
	}
	
	public function urlof($id = "tn") {
		$n = urlencode($this->Name);
		return "$id=$n";
	}
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select description,divnum,captfirst,captlast,paid,playing from team where $q");
		if (!$ret)
			throw new TeamException("Cannot read database for team $q");
		if (mysql_num_rows($ret) == 0)
			throw new TeamException("Cannot find team {$this->Name}");
		$row = mysql_fetch_assoc($ret);
		$this->Description = $row["description"];
		$this->Division = $row["divnum"];
		$this->Paid = $row["paid"];
		$this->Playing = $row["playing"];
		try {
			$this->Captain = new Player($row["captfirst"], $row["captlast"]);
			$this->Captain->fetchdets();
		}
		catch (PlayerException $e) {
			$this->Captain = new Player("Unknown", "Captain");
		}
	}
	
	// Overrides teambase version
	
	public function display_name($displink = false) {
		$ret = htmlspecialchars($this->Name);
		if ($displink)
			return "<a href=\"teamdisp.php?{$this->urlof()}\" class=\"name\" title=\"Show team\">$ret</a>";
		return $ret;
	}
	
	// Trivial but room for expansion
	
	public function display_division() {
		return $this->Division;
	}
	
	public function display_captain($lnk = false) {
		return $this->Captain->display_name($lnk);
	}
	
	public function display_capt_email($l = true) {
		if (!$l)
			return "";
		$m = $this->Captain->display_email();
		if (strlen($m) < 2)
			return "";
		return $m;
	}
	
	public function save_hidden($prefix = "") {
		$f = $this->Name;
		return "<input type=\"hidden\" name=\"${prefix}tn\" value=\"$f\">";
	}
	
	public function create() {
		$qname = mysql_real_escape_string($this->Name);
		$qdescr = mysql_real_escape_string($this->Description);
		$qcfirst = $this->Captain->queryfirst();
		$qclast = $this->Captain->querylast();
		$qdiv = $this->Division;
		if (!mysql_query("insert into team (name,description,divnum,captfirst,captlast) values ('$qname','$qdescr',$qdiv,'$qcfirst','$qclast')"))
			throw new TeamException(mysql_error());
	}
	
	public function updatename($newt) {
		$qname = mysql_real_escape_string($newt->Name);
		mysql_query("update team set name='$qname' where {$this->queryof()}");
		// Need to change team in teammemb as well
		mysql_query("update teammemb set teamname='$qname' where {$this->queryof('teamname')}");
		// Also reset any matches
		mysql_query("update lgmatch set hteam='$qname' where {$this->queryof('hteam')}");
		mysql_query("update lgmatch set ateam='$qname' where {$this->queryof('ateam')}");
		// And games
		mysql_query("update game set wteam='$qname' where {$this->queryof('wteam')} and current=1");
		mysql_query("update game set bteam='$qname' where {$this->queryof('bteam')} and current=1");
		$this->Name = $newt->Name;
	}

	public function update() {
		$qdescr = mysql_real_escape_string($this->Description);
		$qcfirst = $this->Captain->queryfirst();
		$qclast = $this->Captain->querylast();
		$qdiv = $this->Division;
		if (!mysql_query("update team set description='$qdescr',divnum=$qdiv,captfirst='$qcfirst',captlast='$qclast' where {$this->queryof()}"))
			throw new TeamException(mysql_error());
	}
	
	// Update division only for when we've not read in the lot
	
	public function updatediv($newdiv) {
		if (!mysql_query("update team set divnum=$newdiv where {$this->queryof()}"))
			throw new TeamException(mysql_error());
		$this->Division = $newdiv;		
	}
	
	public function setpaid($v = true) {
		$vv = $v? 1: 0;
		mysql_query("update team set paid=$vv where {$this->queryof()}");
	}
	
	public function setplaying($v = true) {
		$vv = $v? 1: 0;
		mysql_query("update team set playing=$vv where {$this->queryof()}");
	}	
	
	public function divopt() {
		print "<select name=\"division\">\n";
		$maxt = max_division() + 1; // Allow for 1 more than number of existing
		for ($d = 1;  $d <= $maxt;  $d++)  {
			if ($d == $this->Division)
				print "<option selected>$d</option>\n";
			else
				print "<option>$d</option>\n";
		}
		print "</select>\n";
	}
	
	public function captainopt() {
		$plist = list_players();
		print "<select name=\"captain\">\n";
		foreach ($plist as $p) {
			$v = $p->selof();
			if ($p->is_same($this->Captain))
				print "<option value=\"$v\" selected>{$p->display_name(false)}</option>\n";
			else
				print "<option value=\"$v\">{$p->display_name(false)}</option>\n";
		}
		print "</select>\n";
	}
	
	public function get_n_from_matches($crit, $wot="count(*)") {
		$ret = mysql_query("select $wot from lgmatch where $crit");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	public function get_scores($p = Null) {
		$this->Playedm = $this->get_n_from_matches("result!='N' and result!='P' and ({$this->queryof('hteam')} or {$this->queryof('ateam')})");
		$this->Wonm = $this->get_n_from_matches("({$this->queryof('hteam')} and result='H') or ({$this->queryof('ateam')} and result='A')");
		$this->Lostm = $this->get_n_from_matches("({$this->queryof('hteam')} and result='A') or ({$this->queryof('ateam')} and result='H')");
		$this->Drawnm = $this->get_n_from_matches("result='D' and ({$this->queryof('hteam')} or {$this->queryof('ateam')})");
		$this->Wong = $this->get_n_from_matches("{$this->queryof('hteam')}", "sum(hwins)") +
						  $this->get_n_from_matches("{$this->queryof('ateam')}", "sum(awins)");
		$this->Drawng = $this->get_n_from_matches("{$this->queryof('hteam')}", "sum(draws)") +
							 $this->get_n_from_matches("{$this->queryof('ateam')}", "sum(draws)");
		$this->Lostg = $this->get_n_from_matches("{$this->queryof('hteam')}", "sum(awins)") +
							$this->get_n_from_matches("{$this->queryof('ateam')}", "sum(hwins)");
		if ($p)  {
			$this->Sortrank = $this->Playedm * $p->Played +
									$this->Wonm * $p->Won +
									$this->Drawnm * $p->Drawn +	
									$this->Lostm * $p->Lost +
									$this->Wong * $p->Forg +
									$this->Drawng * $p->Drawng +
									$this->Lostg * $p->Againstg;
			return $this->Sortrank;
		}
		return  0;
	}
	
	public function count_members() {
		$ret = mysql_query("select count(*) from teammemb where {$this->queryof('teamname')}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	public function list_members($order = "rank desc,tmlast,tmfirst") {
		$ret = mysql_query("select tmfirst,tmlast from teammemb where {$this->queryof('teamname')} order by $order");
		$result = array();
		if ($ret) {
			while ($row = mysql_fetch_array($ret)) {
				array_push($result, new TeamMemb($this, $row[0], $row[1]));
			}
		}
		return $result;			
	}
	
	private function getcount($q) {
		$ret = mysql_query("select count(*) from lgmatch where $q");
		if (!$ret || mysql_num_rows($ret) == 0)
			return  0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	// Get record for this team this season against opponent
	
	public function record_against($opp) {
		$res = new itrecord();
		if ($this->is_same($opp))
			$res->Isself = true;
		else  {
			$tth = $this->queryof('hteam');
			$tta = $this->queryof('ateam');
			$toh = $opp->queryof('hteam');
			$toa = $opp->queryof('ateam');
			$res->Won = $this->getcount("(result='H' and $tth and $toa) or (result='A' and $tta and $toh)");
			$res->Drawn = $this->getcount("result='D' and (($tth and $toa) or ($tta and $toh))");
			$res->Lost = $this->getcount("(result='A' and $tth and $toa) or (result='H' and $tta and $toh)");
		}
		return $res;
	}
}

// List teams a given player is captain of

function list_teams_captof($player) {
	$result = array();
	$ret = mysql_query("select name from team where {$player->queryof('capt')} order by name");
	if ($ret and mysql_num_rows($ret) > 0)  {
		while ($row = mysql_fetch_array($ret))  {
			$team = new Team($row[0]);
			$team->fetchdets();
			array_push($result, $team);
		}
	}
	return $result;
}

function list_teams($div = 0, $order = "name", $pl = 1) {
	$divsel = $div == 0? "": " and divnum=$div";
	$ret = mysql_query("select name from team where playing=$pl$divsel order by $order");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_array($ret))
			array_push($result, new Team($row[0]));
	}
	return $result;
}

// For when we want all teams playing or not

function list_all_teams() {
	$ret = mysql_query("select name from team order by playing desc,name");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_array($ret))
			array_push($result, new Team($row[0]));
	}
	return $result;
}

function max_division() {
	$ret = mysql_query("select max(divnum) from team where playing=1");
	if ($ret && mysql_num_rows($ret) > 0) {
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	return 1;	
}

function score_compare($teama, $teamb) {
	// Decide ordering when compiling PWDL then fall back on name order.
	if ($teama->Sortrank != $teamb->Sortrank)
		return $teama->Sortrank > $teamb->Sortrank? -1: 1;
	return strcasecmp($teama->Name, $teamb->Name);
}
?>
