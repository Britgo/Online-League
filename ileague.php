<?php
//   Copyright 2010-2021 John Collins

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
include 'php/matchdate.php';
include 'php/params.php';
include 'php/itrecord.php';
include 'php/season.php';

$Connection = opendatabase();
$mention = $Connection->logged_in;

if ($mention)  {
	try {
		$player = new Player();
		$player->fromid($Connection->userid);
		if ($player->ILdiv != 0)
			$mention = false;
	}
	catch (PlayerException $e) {
		$mention = false;
	}
}

lg_html_header("Individual League Standings", "il");
lg_html_nav();
print <<<EOT
<h1>Current Individual League Standings</h1>
<p>Click <a href="#prev">here</a> to view previous seasons.</p>
<div align="center">

EOT;
$pars = new Params();
$pars->fetchvalues();
$ml = max_ildivision();
for ($d = 1; $d <= $ml; $d++) {
	$pl = list_players_ildiv($d);
	$cn = 6 + count($pl);
	print <<<EOT
<table class="league">
<tr>
<th colspan="$cn" align="center">Division $d</th>
</tr>
<tr>
<th>Player</th>
<th>Rank</th>

EOT;

	foreach ($pl as $p) {
		$p->fetchdets();
		$p->get_scores($pars);
	}
	usort($pl, 'ilscore_compare');

	// Insert column header

	foreach ($pl as $p)  {
		print "<th>{$p->display_initials()}</th>\n";
	}

	print <<<EOT
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
</tr>

EOT;

	$maxrank = $pl[0]->Sortrank;
	$minrank = $pl[count($pl)-1]->Sortrank;
	// This avoids showing prom/releg if they're all the same as with nothing played.
	if ($maxrank == $minrank)
		$maxrank = $minrank = -9999999;
	foreach ($pl as $p) {
		$n = $p->display_name(false);
		if ($p->Sortrank == $maxrank)
			$n = "<span class=\"prom\">$n</span>";
		elseif ($p->Sortrank == $minrank)
			$n = "<span class=\"releg\">$n</span>";
		//  Do this by hand so span overrides colour of link
		$n = "<a href=\"playgames.php?{$p->urlof()}\" class=\"name\">$n</a>";
		print <<<EOT
<tr>
<td>$n</td>
<td>{$p->display_rank()}</td>

EOT;
		foreach ($pl as $op) {
			$reca = $p->record_against($op);
			print "<td>{$reca->display(false)}</td>\n";
		}

		print <<<EOT
<td align="right">{$p->played_games(true,'I')}</td>
<td align="right">{$p->won_games(true,'I')}</td>
<td align="right">{$p->drawn_games(true,'I')}</td>
<td align="right">{$p->lost_games(true,'I')}</td>
</tr>

EOT;
	}
	print "</table>\n";
	if ($d != $ml)
		print "<br><br><br>\n";
}
print <<<EOT
</div>
<p>Key to above: Matches <b>P</b>layed, <b>W</b>on, <b>D</b>rawn, <b>L</b>ost.
<span class="prom">Promotion Zone</span> and <span class="releg">Relegation Zone</span>.</p>

EOT;

if ($mention)
	print <<<EOT
<h2>Joining the Individual League</h2>
<p>Just select Update Account from <a href="ownupd.php">here</a>
or the left menu and check the box to join the individual league.</p>
<p>You will (at this stage) be put into the division with players nearest your ranking,
so please make sure that your ranking is correct first.</p>
<p>There is no obligation to play a lot of games so please play as many or as few as you like,
but please try to vary who you play with as much as you can.</p>

<h2>Previous Seasons</h2>
<a name="prev"></a>

EOT;
$seasons = list_seasons('I');
if (count($seasons) == 0) {
	print <<<EOT
<p>No previous seasons to display data for.</p>

EOT;
}
else {
		print <<<EOT
<table class="teamsb">
<tr>
	<th>Season Name</th>
	<th>Start Date</th>
	<th>End Date</th>
	<th>League table</th>
</tr>

EOT;
	foreach ($seasons as $seas) {
		$seas->fetchdets();
		print <<<EOT
<tr>
	<td>{$seas->display_name()}</td>
	<td>{$seas->display_start()}</td>
	<td>{$seas->display_end()}</td>
	<td><a href="seasileague.php?{$seas->urlof()}">Click</a></td>
</tr>

EOT;
	}
	print "</table>\n";
}
lg_html_footer();
?>
