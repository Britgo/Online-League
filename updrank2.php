<?php
//   Copyright 2011-2021 John Collins

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

// Part 2 of update player ranks for team.

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';

$Connection = opendatabase(true);
try {
	$team = new Team();
	$team->frompost();
	$team->fetchdets();
}
catch (TeamException $e) {
   wrongentry($e->getMessage());
}
$membs = $team->list_members();
$n=0;
foreach ($membs as $m) {
	$m->fetchdets();
	$r = $_POST["rank$n"];
	if (strlen($r) == 0)  {
   wrongentry("Confused about team member ranks");
	}
	if ($r != $m->Rank->Rankvalue)
		$m->updrank($r);
	$n++;
}
$Title = "Update member ranks complete";
lg_html_header($Title);
lg_html_nav();
print <<<EOT
<h1>Rank adjustments complete</h1>
<p>Finished making rank adjustments.</p>
<p><a href="matches.php">Click here</a> if you want to go to match assignments.</p>

EOT;
lg_html_footer();
?>
