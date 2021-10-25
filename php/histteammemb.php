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

class HistteamMembException extends Exception {}

class HistteamMemb extends Player  {
	public $Team;		// A histteam object

	public function __construct($t, $f = "", $l = "") {
		parent::__construct($f, $l);
		$this->Team = $t;
	}

	//  Fetch the rank which might be different for that season

	public function fetchrank()  {
		global $Connection;
		$qsind = $this->Team->Seas->queryof();
		$qname = $this->queryof('tm');
		$ret = $Connection->query("SELECT rank FROM histteammemb WHERE $qsind and $qname");
		if ($ret && $ret->num_rows > 0)  {
			$row = $ret->fetch_array();
			$this->Rank = new Rank($row[0]);
		}
	}

	public function create() {
		global $Connection;
		$qsind = $this->Team->Seas->queryof();
		$qindn = $this->Team->Seas->Ind;
		$qname = $this->queryof('tm');
		$qfirst = $this->queryfirst();
		$qlast = $this->querylast();
		$qteam = $this->Team->queryname();
		$qrank = $this->Rank->Rankvalue;
		// Save messing around by deleting any same named individual in the same season
		$Connection->query("DELETE FROM histteammemb WHERE $qsind and $qname");
		if (!$Connection->query("INSERT INTO histteammemb (seasind,teamname,tmfirst,tmlast,rank) VALUES ($qindn,'$qteam','$qfirst','$qlast',$qrank)"))
			throw new HistteamMembException($Connection->error);
	}
}
?>
