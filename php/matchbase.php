<?php
//   Copyright 2016-2021 John Collins

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

class MatchException extends Exception {
	public $Nfound;		// Couldn't find match
	public $Mid;			// Match id

	public function __construct($msg, $nf = false, $mid = 0) {
		parent::__construct($msg);
		$this->Nfound = $nf;
		$this->Mid = $mid;
	}
}

// For convenience we have a "home team" and an "away team" on each match.

class MatchBase {
	protected $Ind;		// Ind from database
	public  $Division;	// Division number
	public  $Date;			// Matchdate class object
	public  $Hwins;		// "Home" game wins
	public  $Awubs;		// "Away" game wins
	public  $Draws;		// Drawn games
	public  $Result;		// N (not played) P (part played) D Draw H Home Win A Away win
	public  $Games;		// Array of game objects
	public  $Defaulted;	// Boolean if whole match defaulted

	public function __construct($in = 0, $d = 1) {
		$this->Ind = $in;
		$this->Division = $d;
		$this->Date = new Matchdate();
		$this->Hwins = 0;
		$this->Awins = 0;
		$this->Draws = 0;
		$this->Result = 'N';
		$this->Games = array();
		$this->Defaulted = false;
	}

	// Just a few places where we need the ind but we don't want people fiddling
	// so we make it private

	public function query_ind() {
		return $this->Ind;
	}

	// Use for generating a database query component referring to the match
	// $prefix is set to a non-empty string where the column name has some
	// prefix to "ind" mostly for game where column is "matchind"

	public function queryof($prefix="") {
		return "{$prefix}ind={$this->Ind}";
	}

	// Get the team details for a match

	public function fetchteams() {
		try  {
			$this->Hteam->fetchdets();
			$this->Ateam->fetchdets();
		}
		catch (TeamException $e) {
			throw new MatchException($e->getMessage());
		}
	}

	public function newgame() {
		$g = new Game(0, $this->Ind, $this->Division);
		$g->Date = $this->Date;
		array_push($this->Games, $g);
		return $g;
	}

	public function ngames()  {
		return count($this->Games);
	}

	// Indicate if both teams are allocated

	public function is_allocated() {
		if ($this->Defaulted)
			return true;
		if ($this->ngames() < 3)
			return false;
		foreach ($this->Games as $game)
			if (!$game->is_allocated())
				return false;
		return true;
	}

	// Indicate if given team is allocated

	public function team_allocated($t) {
		if  ($this->Defaulted)
			return true;
		if ($this->ngames() < 3)
			return false;
		foreach ($this->Games as $game)
			if (!$game->team_allocated($t))
				return false;
		return true;
	}

	public function teamalloc()  {
		if  ($this->Defaulted)
			return true;
		$ret = $Connection->query("SELECT COUNT(*) FROM game WHERE {$this->queryof('match')}");
		if (!$ret || $ret->num_rows == 0)
			return false;
		$row = $ret->fetch_array();
		return $row[0] != 0;
	}

	public function convhalf($sc) {
		if ($sc == 0.5)
			return '&frac12;';
		return  preg_replace('/\.5/', '&frac12;', $sc);
	}

	public function summ_score() {
		$d = $this->Draws * 0.5;
		return  $this->convhalf($this->Hwins+$d) . "-" . $this->convhalf($this->Awins+$d);
	}
}
?>
