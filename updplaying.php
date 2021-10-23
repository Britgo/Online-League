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

$Connection = opendatabase(true);
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
   wrongentry($e->getMessage());
}

$Title = "Update playing/not playing status for Team {$team->display_name()}";
lg_html_header($Title);
lg_html_nav();
print <<<EOT
<h1>$Title</h1>

EOT;
if ($team->Playing) {
	print <<<EOT
<p>Team {$team->display_name()} was previously marked as playing this season but setting to <b>not playing</b>.</p>

EOT;
	$v = false;
}
else {
	print <<<EOT
<p>Team {$team->display_name()} was previously marked as not playing this season. Now setting to <b>playing</b>.</p>

EOT;
	$v = true;
}
$team->setplaying($v);
print <<<EOT
<p>Click <a href="teamsupd.php">here</a> to return to the team update menu.</p>

EOT;
lg_html_footer();
?>
