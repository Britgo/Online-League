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

class TeamMembException extends Exception {}

class TeamMemb extends Player  {
	public $Team;		// A team object
	
	public function __construct($t, $f = "", $l = "") {
		parent::__construct($f, $l);
		$this->Team = $t;
	}
	
	public function create() {
		if (!mysql_query("insert into teammemb set tmfirst='{$this->queryfirst()}',tmlast='{$this->querylast()}',{$this->Team->queryof('teamname')},rank={$this->Rank->Rankvalue}"))
			throw new TeamMembException(mysql_error());
	}
}

function del_team_membs($team) {
	if  (!mysql_query("delete from teammemb where {$team->queryof('teamname')}"))
		throw new TeamMembException(mysql_error());
}
?>
