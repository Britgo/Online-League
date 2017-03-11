<?php

//   Copyright 2010 John Collins

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
		$qsind = $this->Team->Seas->queryof();
		$qname = $this->queryof('tm');
		$ret = mysql_query("select rank from histteammemb where $qsind and $qname");
		if ($ret && mysql_num_rows($ret) > 0)  {
			$row = mysql_fetch_array($ret);
			$this->Rank = new Rank($row[0]);
		}
	}
	
	public function create() {
		$qsind = $this->Team->Seas->queryof();
		$qindn = $this->Team->Seas->Ind;
		$qname = $this->queryof('tm');
		$qfirst = $this->queryfirst();
		$qlast = $this->querylast();
		$qteam = $this->Team->queryname();
		$qrank = $this->Rank->Rankvalue;
		// Save messing around by deleting any same named individual in the same season
		mysql_query("delete from histteammemb where $qsind and $qname");
		if (!mysql_query("insert into histteammemb (seasind,teamname,tmfirst,tmlast,rank) values ($qindn,'$qteam','$qfirst','$qlast',$qrank)"))
			throw new HistteamMembException(mysql_error());
	}
}
?>
