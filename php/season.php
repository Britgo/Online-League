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

class SeasonException extends Exception {}

class Season  {
	public $Ind;			// Unique historic season id
	public $Name;			// Season name
	public $Startdate;	// First date for season
	public $Enddate;		// Last date for season
	public $League;
	
	public function __construct($id = 0, $l = 'T') {
		$this->Ind = $id;
		$this->Name = "";
		$this->Startdate = new Matchdate();
		$this->Enddate = new Matchdate();
		$this->League = $l;
	}
	
	public function fromget() {
		$this->Ind = $_GET["si"];
		if (!$this->Ind)
			throw new SeasonException("Null id field"); 
	}

	//  We have the auto-inc column in the season table as 'ind' but in the other
	//  tables as 'in' so remember to pass 'ind' as a parameter when fetching.
	
	public function queryof($colname = "seasind") {
		return "$colname=$this->Ind";
	}
	
	public function urlof() {
		return "si={$this->Ind}";
	}
	
	public function fetchdets() {
		$ret = mysql_query("select name,startdate,enddate,league from season where {$this->queryof('ind')}");
		if (!$ret)
			throw new SeasconException("Cannot read database for season");
		if (mysql_num_rows($ret) == 0)
			throw new SeasonException("Cannot find season id={$this->Ind}");
		$row = mysql_fetch_assoc($ret);
		$this->Name = $row["name"];
		$this->Startdate->enctime($row["startdate"]);
		$this->Enddate->enctime($row["enddate"]);
		$this->League = $row["league"];
	}
		
	public function display_name() {
		return htmlspecialchars($this->Name);
	}
	
	public function display_start() {
		return $this->Startdate->display_month();
	}
	public function display_end() {
		return $this->Enddate->display_month();
	}
	
	public function create() {
		$qname = mysql_real_escape_string($this->Name);
		$qstart = $this->Startdate->queryof();
		$qend = $this->Enddate->queryof();
		if (!mysql_query("insert into season (name,startdate,enddate,league) values ('$qname','$qstart','$qend','{$this->League}')"))
			throw new SeasonException(mysql_error());
		$ret = mysql_query("select last_insert_id()");
		if (!$ret || mysql_num_rows($ret) == 0)
			throw new SeasonException("Cannot locate season record id");
		$row = mysql_fetch_array($ret);
		$this->Ind = $row[0];
		return  $this->Ind;
	}
	
	public function update() {
		$qname = mysql_real_escape_string($this->Name);
		$qstart = $this->Startdate->queryof();
		$qend = $this->Enddate->queryof();
		if (!mysql_query("update season set name='$qname',startdate='$qstart',enddate='$qend' where {$this->queryof('ind')}"))
			throw new SeasonException(mysql_error());
	}
}

function list_seasons($l = 'T', $desc = false) {
	$ord = $desc? " desc": "";
	$ret = mysql_query("select ind from season where league='$l' order by enddate$ord");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_array($ret)) {
			array_push($result, new Season($row[0]));
		}
	}
	return $result;
}
?>
