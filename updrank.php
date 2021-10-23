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

// This is to let team captains update ranks of members of their team.

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
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
   wrongentry($e->getMessage());
}
$Title = "Update member ranks";
lg_html_header($Title);
lg_html_nav();
print <<<EOT
<h1>Adjust ranks of team members</h1>
print <<<EOT
<p>Use this page to adjust ranks of members of the team {$team->display_name()} ({$team->display_description()})
in division {$team->display_division()}.</p>
<form name="trform" action="updrank2.php" method="post" enctype="application/x-www-form-urlencoded">
{$team->save_hidden()}
<table class="teamdisp">
<tr><th>Player</th><th>Rank</th></tr>

EOT;

$membs = $team->list_members();
$n=0;
foreach ($membs as $m) {
	$m->fetchdets();
	print "<tr><td>{$m->display_name(false)}</td>\n<td>";
	$m->rankopt($n);
	print "</td></tr>\n";
	$n++;
}

print <<<EOT
<p>Make any adjustments and input type="submit" value="Click here"> or <input type="reset" value="Reset form"></p>
</form>

EOT;
lg_html_footer();
?>
