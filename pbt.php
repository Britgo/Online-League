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
include 'php/teammemb.php';

$Connection = opendatabase();
lg_html_header("Players List by team");
lg_html_nav();
$cs = 13;

print <<<EOT
<h1>Players by team</h1>
<table class="pllist">
<tr>
<th colspan="3">&nbsp;</th>
<th colspan="4" align="center">Current</th>
<th>&nbsp;</th>
<th colspan="4" align="center">Total</th>
</tr>
<th>Name</th>
<th>Rank</th>
<th>Online</th>
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
<th>&nbsp;</th>
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
<th>Club</th>
</tr>

EOT;
$tlist = list_teams();
foreach ($tlist as $team) {
	$team->fetchdets();
	print <<<EOT
<tr>
<th colspan=$cs align="center">
<a href="teamdisp.php?{$team->urlof()}" class="nound">{$team->display_name()}</a>
</th>
</tr>

EOT;
	$pl = $team->list_members();
	foreach ($pl as $m) {
		$m->fetchdets();
		$m->fetchclub();
		print <<<EOT
<tr>
<td>{$m->display_name()}</td>
<td>{$m->display_rank()}</td>
<td>{$m->display_online()}</td>
<td>{$m->played_games(true)}</td>
<td>{$m->won_games(true)}</td>
<td>{$m->drawn_games(true)}</td>
<td>{$m->lost_games(true)}</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>{$m->played_games()}</td>
<td>{$m->won_games()}</td>
<td>{$m->drawn_games()}</td>
<td>{$m->lost_games()}</td>
<td>{$m->Club->display_name()}</td>
</tr>

EOT;
	}
}
print <<<EOT
<tr><th colspan=$cs align="center">Not in a team</th></tr>

EOT;

$ret = $Connection->query("SELECT first,last FROM player ORDER BY last,first,rank desc");
if ($ret) {
	while ($row = $ret->fetch_assoc()) {
		$p = new Player($row["first"], $row["last"]);
		if ($p->count_teams() != 0)
			continue;
		$p->fetchdets();
		$p->fetchclub();
		print <<<EOT
<tr>
<td>{$p->display_name()}</td>
<td>{$p->display_rank()}</td>
<td>{$p->display_online()}</td>
<td>{$p->played_games(true, 'T')}</td>
<td>{$p->won_games(true, 'T')}</td>
<td>{$p->drawn_games(true, 'T')}</td>
<td>{$p->lost_games(true, 'T')}</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>{$p->played_games()}</td>
<td>{$p->won_games()}</td>
<td>{$p->drawn_games()}</td>
<td>{$p->lost_games()}</td>
<td>{$p->Club->display_name()}</td>
</tr>

EOT;
	}
}
print <<<EOT
</table>
<p>Click on player name to get game record for player.</p>

EOT;
lg_html_footer();
?>
