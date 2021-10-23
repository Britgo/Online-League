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

	// Argument here not actually used but need to be consistent with parent version

	public function fromget($gf = NULL) {
		parent::fromget("tn");
	}

	public function frompost($prefix = "") {
		$this->Name = $_POST["${prefix}tn"];
		if (strlen($this->Name) == 0)
			throw new TeamException("Null post name field");
	}

	public function queryof($colname = "name") {
		global $Connection;
		$qn = $Connection->real_escape_string($this->Name);
		return "$colname='$qn'";
	}

	public function urlof($id = "tn") {
		$n = urlencode($this->Name);
		return "$id=$n";
	}

	public function fetchdets() {
		global $Connection;
		$q = $this->queryof();
		$ret = $Connection->query("SELECT description,divnum,captfirst,captlast,paid,playing FROM team WHERE $q");
		if (!$ret)
			throw new TeamException("Cannot read database for team $q");
		if ($ret->num_rows == 0)
			throw new TeamException("Cannot find team {$this->Name}");
		$row = $ret->fetch_assoc();
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
		$qname = $Connection->real_escape_string($this->Name);
		$qdescr = $Connection->real_escape_string($this->Description);
		$qcfirst = $this->Captain->queryfirst();
		$qclast = $this->Captain->querylast();
		$qdiv = $this->Division;
		if (!$Connection->query("INSERT INTO team (name,description,divnum,captfirst,captlast) VALUES ('$qname','$qdescr',$qdiv,'$qcfirst','$qclast')"))
			throw new TeamException($Connection->error);
	}

	public function updatename($newt) {
		global $Connection;
		$qname = $Connection->real_escape_string($newt->Name);
		$Connection->query("UPDATE team SET name='$qname' WHERE {$this->queryof()}");
		// Need to change team in teammemb as well
		$Connection->query("UPDATE teammemb SET teamname='$qname' WHERE {$this->queryof('teamname')}");
		// Also reset any matches
		$Connection->query("UPDATE lgmatch SET hteam='$qname' WHERE {$this->queryof('hteam')}");
		$Connection->query("UPDATE lgmatch SET ateam='$qname' WHERE {$this->queryof('ateam')}");
		// And games
		$Connection->query("UPDATE game SET wteam='$qname' WHERE {$this->queryof('wteam')} AND current=1");
		$Connection->query("UPDATE game SET bteam='$qname' WHERE {$this->queryof('bteam')} AND current=1");
		$this->Name = $newt->Name;
	}

	public function update() {
		global $Connection;
		$qdescr = $Connection->real_escape_string($this->Description);
		$qcfirst = $this->Captain->queryfirst();
		$qclast = $this->Captain->querylast();
		$qdiv = $this->Division;
		if (!$Connection->query("UPDATE team SET description='$qdescr',divnum=$qdiv,captfirst='$qcfirst',captlast='$qclast' WHERE {$this->queryof()}"))
			throw new TeamException($Connection->error);
	}

	// Update division only for when we've not read in the lot

	public function updatediv($newdiv) {
		global $Connection;
		if (!$Connection->query("UPDATE team SET divnum=$newdiv WHERE {$this->queryof()}"))
			throw new TeamException($Connection->error);
		$this->Division = $newdiv;
	}

	public function setpaid($v = true) {
		global $Connection;
		$vv = $v? 1: 0;
		$Connection->query("UPDATE team SET paid=$vv WHERE {$this->queryof()}");
	}

	public function setplaying($v = true) {
		global $Connection;
		$vv = $v? 1: 0;
		$Connection->query("UPDATE team SET playing=$vv WHERE {$this->queryof()}");
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

	public function get_n_from_matches($crit, $wot="COUNT(*)") {
		global $Connection;
		$ret = $Connection->query("SELECT $wot FROM lgmatch WHERE $crit");
		if (!$ret || $ret->num_rows == 0)
			return 0;
		$row = $ret->fetch_array();
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
		global $Connection;
		$ret = $Connection->query("SELECT COUNT(*) FROM teammemb WHERE {$this->queryof('teamname')}");
		if (!$ret || $ret->num_rows == 0)
			return 0;
		$row = $ret->fetch_array();
		return $row[0];
	}

	public function list_members($order = "rank desc,tmlast,tmfirst") {
		global $Connection;
		$ret = $Connection->query("SELECT tmfirst,tmlast FROM teammemb WHERE {$this->queryof('teamname')} ORDER BY $order");
		$result = array();
		if ($ret) {
			while ($row = $ret->fetch_array()) {
				array_push($result, new TeamMemb($this, $row[0], $row[1]));
			}
		}
		return $result;
	}

	private function getcount($q) {
		global $Connection;
		$ret = $Connection->query("SELECT COUNT(*) FROM lgmatch WHERE $q");
		if (!$ret || $ret->num_rows == 0)
			return  0;
		$row = $ret->fetch_array();
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
	global $Connection;
	$result = array();
	$ret = $Connection->query("SELECT name FROM team WHERE {$player->queryof('capt')} ORDER BY name");
	if ($ret and $ret->num_rows > 0)  {
		while ($row = $ret->fetch_array())  {
			$team = new Team($row[0]);
			$team->fetchdets();
			array_push($result, $team);
		}
	}
	return $result;
}

function list_teams($div = 0, $order = "name", $pl = 1) {
	global $Connection;
	$divsel = $div == 0? "": " and divnum=$div";
	$ret = $Connection->query("SELECT name FROM team WHERE playing=$pl$divsel ORDER BY $order");
	$result = array();
	if ($ret) {
		while ($row = $ret->fetch_array())
			array_push($result, new Team($row[0]));
	}
	return $result;
}

// For when we want all teams playing or not

function list_all_teams() {
	global $Connection;
	$ret = $Connection->query("SELECT name FROM team ORDER BY playing desc,name");
	$result = array();
	if ($ret) {
		while ($row = $ret->fetch_array())
			array_push($result, new Team($row[0]));
	}
	return $result;
}

function max_division() {
	global $Connection;
	$ret = $Connection->query("SELECT max(divnum) FROM team WHERE playing=1");
	if ($ret && $ret->num_rows > 0) {
		$row = $ret->fetch_array();
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
