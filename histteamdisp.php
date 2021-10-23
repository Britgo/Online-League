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
include 'php/teambase.php';
include 'php/matchbase.php';
include 'php/histteam.php';
include 'php/histteammemb.php';
include 'php/histmatch.php';
include 'php/matchdate.php';
include 'php/season.php';

$Connection = opendatabase();

try {
	$seas = new Season();
	$seas->fromget();
	$seas->fetchdets();
}
catch (SeasonException $e) {
    wrongentry($e->getMessage());
}
try {
	$team = new Histteam($seas);
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
   wrongentry($e->getMessage());
}

lg_html_header("Historic Team {$team->display_name()}");
lg_html_nav();
print <<<EOT
<h1>Historic Team {$team->display_name()}</h1>
<p>This is the record for
{$team->display_name()} - {$team->display_description()}
in {$team->display_division()}
for <b>{$seas->display_name()}</b>
running from
{$seas->display_start()} to {$seas->display_end()}.
</p>
<h3>Members were</h3>
<table class="teamdisp">
<tr>
	<th>Name</th>
	<th>Rank</th>
	<th>Played</th>
	<th>Won</th>
	<th>Drawn</th>
	<th>Lost</th>
</tr>

EOT;
$membs = $team->list_members();
foreach ($membs as $m) {
	$m->fetchdets();
	$m->fetchrank();
	print <<<EOT
<tr>
	<td>{$m->display_name()}</td>
	<td>{$m->display_rank()}</td>
	<td align="right">{$m->played_games()}</td>
	<td align="right">{$m->won_games()}</td>
	<td align="right">{$m->drawn_games()}</td>
	<td align="right">{$m->lost_games()}</td>
</tr>

EOT;
}
print <<<EOT
</table>
<p>(Player record includes all online league games not just the one for the season, click
on player name for more details).</p>

EOT;

if ($team->Playedm != 0)  {
	print <<<EOT
<h2>Match Record</h2>
<p>
Match record for season is Played: {$team->Playedm} Won: {$team->Wonm}
Drawn: {$team->Drawnm} Lost: {$team->Lostm}.</p>
<img src="php/piewdl.php?w={$team->Wonm}&d={$team->Drawnm}&l={$team->Lostm}">
<br />
<table class="teamdisp">
<tr>
	<th>Date</th>
	<th>Opponent</th>
	<th>Result</th>
</tr>

EOT;
	$ret = $Connection->query("SELECT ind FROM histmatch WHERE {$seas->queryof()} AND result!='N' AND result!='P' AND ({$team->queryof('hteam')} OR {$team->queryof('ateam')}) ORDER BY matchdate");
	if ($ret)  {
		while ($row = $ret->fetch_array())  {
			$mtch = new HistMatch($seas, $row[0]);
			$mtch->fetchdets();
			$oppteam = $mtch->Hteam;
			if ($oppteam->is_same($team))  {
				$oppteam = $mtch->Ateam;
				switch ($mtch->Result) {
				case 'H':	$res = 'Won';	break;
				case 'D':	$res = 'Drawn'; break;
				case 'A':	$res = 'Lost';	break;
				}
			}
			else
				switch ($mtch->Result) {
				case 'H':	$res = 'Lost';	break;
				case 'D':	$res = 'Drawn'; break;
				case 'A':	$res = 'Won';	break;
				}
			print <<<EOT
<tr>
	<td>{$mtch->Date->display_month()}</td>
	<td><a href="histteamdisp.php?{$oppteam->urlof()}&{$seas->urlof()}" class="nound">{$oppteam->display_name()}</a></td>
	<td><a href="histshowmtch.php?{$mtch->urlof()}" class="nound">$res</a></td>
</tr>

EOT;
		}
	}
	print "</table>\n";
}
if ($team->Wong + $team->Drawng + $team->Lostg != 0)  {
	print <<<EOT
<h2>Game Record</h2>
<p>Game record is For: {$team->Wong} Against: {$team->Lostg} Drawn: {$team->Drawng}.</p>
<img src="php/piewdl.php?w={$team->Wong}&d={$team->Drawng}&l={$team->Lostg}">
<br />

EOT;
}
print <<<EOT
<p><b>Please note</b> you can click on players, opposing teams and results for more details.</p>

EOT;
lg_html_footer();
?>
