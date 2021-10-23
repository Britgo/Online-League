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

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/news.php';

$Connection = opendatabase(true);
$div = $_POST["div"];
if (strlen($div) == 0)
	wrongentry("Expecting to be invoked via form");

$dat = new Matchdate();
$dat->frompost();
$mintnum = $_POST["mintnum"];
$mint = $_POST["mint"];
lg_html_header("Initialise matches");
lg_html_nav();
print <<<EOT
<h1>Initialise Matches</h1>

EOT;

class MatchData {
	public $hteam;				//  Home team number
	public $ateam;				//  Away team number

	public function __construct($h, $a) {
		$this->hteam = $h;
		$this->ateam = $a;
	}
}

// Scramble an array of things by attaching random numbers to each element and then sorting on that

class Unsort {
	public $datum;
	public $key;

	public function __construct($d)  {
		$this->datum = $d;
		$this->key = rand(0, 1000000);
	}
}

// Sort of sort to unsort with

function unsort_sort($a, $b) {
	return $a->key - $b->key;
}

// Scramble the supplied array

function unsort(&$arr) {
	$sa = array();
	foreach ($arr as $ap)
		array_push($sa, new Unsort($ap));
	usort($sa, 'unsort_sort');
	$arr = array();
	foreach ($sa as $u)
		array_push($arr, $u->datum);
}

// Recursive function to allocate matches to period
// Return true if we succeed

function placeteams($level, $period) {
	global $crosstable, $athome, $nteams, $assigned;

	$hteam = -1;
	for  (;;)  {

		//  Select a home team

		do  $hteam++;
		while  ($hteam < $nteams && $assigned[$hteam]);
		if  ($hteam >= $nteams)
			break;

		// Look at each possible team to play against

		foreach ($athome[$hteam] as $pm)  {
			$ateam = $pm->ateam;

			// If we have already allocated that team this period or have a match set up, try again

			if  ($assigned[$ateam] || $crosstable[$hteam][$ateam] >= 0)
				continue;

			//  Mark teams set up

			$crosstable[$hteam][$ateam] = $period;
			$assigned[$hteam] = $assigned[$ateam] = true;
			if ($level <= 0  ||  placeteams($level-1, $period))
				return true;

			//  Failed try next one

			$crosstable[$hteam][$ateam] = -1;
			$assigned[$hteam] = $assigned[$ateam] = false;
		}
	}
	return  false;
}

function match_sort($a, $b) {
	$dd = $a->Date->sortby($b->Date);
	if  ($dd != 0)
		return  $dd;
	$dd = strcasecmp($a->Hteam->Name, $b->Hteam->Name);
	if  ($dd != 0)
		return  $dd;
	return  strcasecmp($a->Ateam->Name, $b->Ateam->Name);
}

$teams = list_teams($div);
$nteams = count($teams);

if ($nteams <= 2)  {
	print <<<EOT
<p>
Sorry but not enough teams in Division $div for a match yet.
</p>

EOT;
}
else  {

	// Shuffle teams around

	unsort($teams);

	// Add a team if we have an odd number

	$byeteam = $nteams;

	if (($nteams & 1) == 1)  {
		array_push($teams, new Team('Bye'));
		$nteams++;
	}

	$nmatches = $nteams * ($nteams - 1);		// Total number of matches
	$matchespp = $nteams / 2;						// Matches per period
	$periods = $nmatches / $matchespp;			// Number of periods

	// Set up cross table who plays who with period number 0 ... $periods-1

	$crosstable = array_pad(array(), $nteams, array_pad(array(), $nteams, -1));

	// Initialise the "$athome" array

	$athome = array();

	for ($h = 0;  $h < $nteams;  $h++)  {
		$hf = array();
		for ($a = 0;  $a < $nteams;  $a++)
			if  ($h != $a)
				array_push($hf, new MatchData($h, $a));
		array_push($athome, $hf);
	}

	// Main loop to generate everyone playing everyone

	$Period = 0;
	for ($Hometeam = 0;  $Hometeam < $byeteam;  $Hometeam++)  {
		foreach ($athome[$Hometeam] as $pm)  {
			$ateam = $pm->ateam;
			if ($crosstable[$Hometeam][$ateam] >= 0)
				continue;
			$assigned = array_pad(array(), $nteams, false);
			$assigned[$Hometeam] = $assigned[$ateam] = true;
			$crosstable[$Hometeam][$ateam] = $Period;
			if (placeteams($matchespp-2, $Period))
				$Period++;
			else
				$crosstable[$Hometeam][$ateam] = -1;
		}
	}

	// Now set up and scramble the period numbers

	$plu = array(new Matchdate($dat));
	for ($p = 1;  $p < $periods;  $p++)  {
		$d = new Matchdate($plu[$p-1]);
		$d->next_month($mint, $mintnum);
		array_push($plu, $d);
	}
	unsort($plu);

	// Now assign matches for each period

	$Matchlist = array();

	for ($Period = 0; $Period < $periods; $Period++)  {
		for ($h = 0;  $h < $byeteam;  $h++)
			for ($a = 0;  $a < $byeteam; $a++)
				if ($crosstable[$h][$a] == $Period)  {
						$mtch = new Match(0, $div);
						$mtch->Hteam = $teams[$h];
						$mtch->Ateam = $teams[$a];
						$mtch->Date = $plu[$Period];
						$mtch->create();
						array_push($Matchlist, $mtch);
				}
	}

	// Now finally sort the match list by date and names

	usort($Matchlist, 'match_sort');

	$sdat = new Matchdate();		// Set silly starting date
	$sdat->next_month('m', 10);

	foreach ($Matchlist as $rm)  {
		if ($sdat->haschanged($rm->Date))  {
			$sdat = $rm->Date;
			print "<h2>{$sdat->display()}</h2>\n";
		}
		print "<p>{$rm->Hteam->display_name()} -v- {$rm->Ateam->display_name()}</p>\n";
	}
}
$nws = new News('ADMINS', "Draw made for new season division $div", true, "matches.php");
$nws->addnews();
print <<<EOT
<p>Click <a href="javascript:self.close()">here</a> to close this window.</p>

EOT;
lg_html_footer();
?>
