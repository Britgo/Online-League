<?php
//   Copyright 2011 John Collins

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

include 'php/session.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/season.php';
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Team {$team->display_name()}";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php';
print <<<EOT
<h1>Team {$team->display_name()}</h1>
<p>
Team {$team->display_name()} - {$team->display_description()} - division
{$team->display_division()}
</p>
<p>
Team captain is {$team->display_captain()}.
{$team->display_capt_email($logged_in)}
</p>

EOT;
if ($admin && !$team->Paid)
	print <<<EOT
<p><b>Team has not paid.</b></p>
EOT;
print <<<EOT
<h3>Members</h3>
<table class="teamdisp">
<tr>
	<th>Name</th>
	<th>Rank</th>
	<th>Club</th>
	<th>Played</th>
	<th>Won</th>
	<th>Drawn</th>
	<th>Lost</th>

EOT;
if ($admin)
	print <<<EOT
<th>BGA</th>

EOT;
print <<<EOT
</tr>

EOT;
$membs = $team->list_members();
foreach ($membs as $m) {
	$m->fetchdets();
	$m->fetchclub();
	print <<<EOT
<tr>
	<td>{$m->display_name()}</td>
	<td>{$m->display_rank()}</td>
	<td>{$m->Club->display_name()}</td>
	<td align="right">{$m->played_games()}</td>
	<td align="right">{$m->won_games()}</td>
	<td align="right">{$m->drawn_games()}</td>
	<td align="right">{$m->lost_games()}</td>

EOT;
if ($admin)  {
	print "<td>";
	if ($m->BGAmemb)
		print "Yes";
	else
		print "No";
	print "</td>\n";
}
print <<<EOT
</tr>

EOT;
}
?>
</table>
<p>(Player record includes all online league games).</p>
<?php
$team->get_scores();
if ($team->Playedm != 0)  {
	print <<<EOT
<h2>Match Record</h2>
<p>
Match record is Played: {$team->Playedm} Won: {$team->Wonm}
Drawn: {$team->Drawnm} Lost: {$team->Lostm}.
</p>
<img src="php/piewdl.php?w={$team->Won}&d={$team->Drawn}&l={$team->Lost}">
<br />
<table class="teamdisp">
<tr>
	<th>Date</th>
	<th>Opponent</th>
	<th>Result</th>
</tr>

EOT;
	$ret = mysql_query("select ind from lgmatch where result!='N' and result!='P' and ({$team->queryof('hteam')} or {$team->queryof('ateam')}) order by matchdate");
	if ($ret)  {
		while ($row = mysql_fetch_array($ret))  {
			$mtch = new Match($row[0]);
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
	<td><a href="teamdisp.php?{$oppteam->urlof()}" class="nound">{$oppteam->display_name()}</a></td>
	<td><a href="showmtch.php?{$mtch->urlof()}" class="nound">$res</a></td>
</tr>

EOT;
		}
	}
	print "</table>\n";
}
if ($team->Wong + $team->Drawng + $team->Lostg != 0)  {
	print <<<EOT
<h2>Game Record</h2>
<p>
Game record is For: {$team->Wong} Against: {$team->Lostg} Drawn: {$team->Drawng}.
</p>
<img src="php/piewdl.php?w={$team->Wong}&d={$team->Drawng}&l={$team->Lostg}">
<br />

EOT;
}
?>
<p><b>Please note</b> you can click on players, opposing teams and results for more details.</p>
<?php
$ret = mysql_query("select seasind from histteam where {$team->queryof()} order by seasind");
if ($ret && mysql_num_rows($ret) > 0)  {
	print <<<EOT
<h2>Previous Seasons</h2>
<p>Select from the following to see the performance of {$team->display_name()} in previous seasons.
</p>
<table>

EOT;
	while ($row = mysql_fetch_array($ret)) {
		$s = new Season($row[0]);
		$s->fetchdets();
		print "<tr><td><a href=\"histteamdisp.php?{$team->urlof('htn')}&{$s->urlof()}\" class=\"nound\">{$s->display_name()}</a></td></tr>\n";
	}
	print <<<EOT
</table>

EOT;
}
?>
</div>
</div>
</body>
</html>
