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

class TeamMembException extends Exception {}

class TeamMemb extends Player  {
	public $Team;		// A team object

	public function __construct($t, $f = "", $l = "") {
		parent::__construct($f, $l);
		$this->Team = $t;
	}

	public function create() {
		if (!$Connection->query("INSERT INTO teammemb SET tmfirst='{$this->queryfirst()}',tmlast='{$this->querylast()}',{$this->Team->queryof('teamname')},rank={$this->Rank->Rankvalue}"))
			throw new TeamMembException($Connection->error);
	}
}

function del_team_membs($team) {
	if  (!$Connection->query("DELETE FROM teammemb WHERE {$team->queryof('teamname')}"))
		throw new TeamMembException($Connection->error);
}
?>
