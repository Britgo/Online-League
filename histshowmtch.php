<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
//   Copyright 2010 John Collins

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

include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/season.php';
include 'php/histteam.php';
include 'php/histteammemb.php';
include 'php/histmatch.php';
include 'php/matchdate.php';
include 'php/game.php';

$mtch = new HistMatch(null);
try  {
	$mtch->fromget();
	$mtch->getseason();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
}
catch (HistMatchException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
?>
<html>
<?php
$Title = "Historic Match Details";
include 'php/head.php';
?>
<body>
<h1>Historic Match Details</h1>
<p>
Match was between
<?php
print <<<EOT
{$mtch->Hteam->display_name()} ({$mtch->Hteam->display_description()})
and
{$mtch->Ateam->display_name()} ({$mtch->Ateam->display_description()})
for
{$mtch->Date->display_month()}.
</p>
<p>Player and board assignments were as follows:</p>
<table class="showmatch">
<tr><th colspan="5" align="center">White</th><th colspan="4" align="center">Black</th><th>Result</th></tr>
<tr><th>Date</th><th>Player</th><th>Rank</th><th>Online</th><th>Team</th><th>Player</th><th>Rank</th><th>Online</th><th>Team</th></tr>
EOT;
foreach ($mtch->Games as $g) {
	print <<<EOT
<tr>
<td>{$g->date_played()}</td>
<td>{$g->Wplayer->display_name()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>{$g->Bplayer->display_name()}</td>
<td>{$g->Bteam->display_name()}</td>
<td>{$g->display_result()}</td>
</tr>
EOT;
}
?>
</table>
<p>Click <a href="javascript:history.back()">here</a> to view some other match.</p>
</body>
</html>
