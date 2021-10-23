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

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';

$Connection = opendatabase(true);
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
   wrongentry($e->getMessage());
}

// Slurp up player names until we can't find any more

$membs = array();

for ($i = 0;; $i++)  {
	try  {
		$m = new TeamMemb($team);
		$m->fromget("tm$i", true);
		$m->fetchdets();
		array_push($membs, $m);
	}
	catch (PlayerException $e) {
		break;
	}
}

try {
	// Delete existing members
	del_team_membs($team);
	// Add new team members
	foreach ($membs as $m) {
		$m->create();
	}
}
catch (TeamMembException $e) {
   database_error($e->getMessage());
}

$Title = "Updated Team Members for {$team->display_name()}";
lg_html_header($Title);
lg_html_nav();
print <<<EOT
<h1>$Title</h1>
<p>Updating team members for {$team->display_description()} is complete.</p>
<p>Click <a href="teamsupd.php">here</a> to resume editing teams.</p>

EOT;
lg_html_footer();
?>
