<?php

//   Copyright 2010-2021 John Collins

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

class Histteam extends Teambase {
	public $Seas;			// Season object

	public function __construct($s, $n = "") {
		parent::__construct($n);
		$this->Seas = $s;
	}

	public function fromget() {
		parent::fromget("htn");
	}

	public function queryof($colname = "name") {
		$qn = $Connection->real_escape_string($this->Name);
		return "$colname='$qn' and seasind={$this->Seas->Ind}";
	}

	public function urlof() {
		$n = urlencode($this->Name);
		return "htn=$n";
	}

	public function fetchdets() {
		$q = $this->queryof();
		$ret = $Connection->query("SELECT description,divnum,playing,sortrank,playedm,wonm,drawnm,lostm,wong,drawng,lostg FROM histteam WHERE $q");
		if (!$ret)
			throw new TeamException("Cannot read database for histteam {$this->Name}");
		if ($ret->num_rows == 0)
			throw new TeamException("Cannot find histteam {$this->Name}");
		$row = $ret->fetch_assoc();
		$this->Description = $row["description"];
		$this->Division = $row["divnum"];
		$this->Playing = $row["playing"];
		$this->Sortrank = $row["sortrank"];
		$this->Playedm = $row["playedm"];
		$this->Wonm = $row["wonm"];
		$this->Drawnm = $row["drawnm"];
		$this->Lostm = $row["lostm"];
		$this->Wong = $row["wong"];
		$this->Drawng = $row["drawng"];
		$this->Lostg = $row["lostg"];
	}
	public function create() {
		$qname = $Connection->real_escape_string($this->Name);
		$qdescr = $Connection->real_escape_string($this->Description);
		$qdiv = $this->Division;
		$qseas = $this->Seas->Ind;
		$qplaying = $this->Playing? 1: 0;
		$qsortrank = $this->Sortrank;
		$qplayedm = $this->Playedm;
		$qwonm = $this->Wonm;
		$qdrawnm = $this->Drawnm;
		$qlostm = $this->Lostm;
		$qwong = $this->Wong;
		$qdrawng = $this->Drawng;
		$qlostg = $this->Lostg;
		// Delete any team with the same name for the season
		$Connection->query("DELETE FROM histteam WHERE {$this->Seas->queryof()} and name='$qname'");
		$cols = "(name,description,divnum,seasind,playing,sortrank,playedm,wonm,drawnm,lostm,wong,drawng,lostg)";
		$vals = "('$qname','$qdescr',$qdiv,$qseas,$qplaying,$qsortrank,$qplayedm,$qwonm,$qdrawnm,$qlostm,$qwong,$qdrawng,$qlostg)";
		if (!$Connection->query("INSERT INTO histteam $cols VALUES $vals"))
			throw new TeamException($Connection->error);
	}

	public function count_members() {
		$ret = $Connection->query("SELECT COUNT(*) FROM histteammemb WHERE {$this->queryof('teamname')}");
		if (!$ret || $ret->num_rows == 0)
			return 0;
		$row = $ret->fetch_array();
		return $row[0];
	}

	public function list_members($order = "rank desc,tmlast,tmfirst") {
		$ret = $Connection->query("SELECT tmfirst,tmlast FROM histteammemb WHERE {$this->queryof('teamname')} ORDER BY $order");
		$result = array();
		if ($ret) {
			while ($row = $ret->fetch_array()) {
				array_push($result, new HistteamMemb($this, $row[0], $row[1]));
			}
		}
		return $result;
	}

	private function getcount($q) {
		$ret = $Connection->query("SELECT COUNT(*) FROM histmatch WHERE {$this->Seas->queryof()} and $q");
		if (!$ret || $ret->num_rows == 0)
			return  0;
		$row = $ret->fetch_array();
		return $row[0];
	}

	public function record_against($opp) {
		$res = new itrecord();
		if ($this->is_same($opp))
			$res->Isself = true;
		else  {
			$tth = $this->queryof('hteam');
			$tta = $this->queryof('ateam');
			$toh = $opp->queryof('hteam');
			$toa = $opp->queryof('ateam');
			$res->Won = $this->getcount("((result='H' and $tth and $toa) or (result='A' and $tta and $toh))");
			$res->Drawn = $this->getcount("result='D' and (($tth and $toa) or ($tta and $toh))");
			$res->Lost = $this->getcount("((result='A' and $tth and $toa) or (result='H' and $tta and $toh))");
		}
		return $res;
	}
}

function hist_list_teams($s, $div = 0, $order = "name", $pl = 1) {
	$divsel = $div == 0? "": " and divnum=$div";
	$i = $s->Ind;
	$ret = $Connection->query("SELECT name FROM histteam WHERE playing=$pl and seasind=$i$divsel ORDER BY $order");
	$result = array();
	if ($ret) {
		while ($row = $ret->fetch_array()) {
			array_push($result, new Histteam($s, $row[0]));
		}
	}
	return $result;
}

function hist_max_division($s) {
	$ret = $Connection->query("SELECT max(divnum) FROM histteam WHERE playing=1 and seasind={$s->Ind}");
	if ($ret && $ret->num_rows > 0) {
		$row = $ret->fetch_array();
		return $row[0];
	}
	return 1;
}

function hist_score_compare($teama, $teamb) {
	// Decide ordering when compiling PWDL then fall back on name order.
	if ($teama->Sortrank != $teamb->Sortrank)
		return $teama->Sortrank > $teamb->Sortrank? -1: 1;
	return strcasecmp($teama->Name, $teamb->Name);
}
?>
