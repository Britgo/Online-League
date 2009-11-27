<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
//   Copyright 2009 John Collins

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

// This version of match update is for team captains who only have access to
// their own team members in ignorance of what the other team are doing
// Also they can't change it once the other side have allocated their team.

include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
$mtch = new Match();
try  {
	$hora = $_GET["hora"];
	if ($hora != 'H' && $hora != 'A')
		throw new MatchException("team not specified on entry");
	$mtch->fromget();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
	if ($mtch->is_allocated())
		throw new MatchException("Teams already fully allocated to match");
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
?>
<html>
<?php
$Title = "Edit Match";
include 'php/head.php';
?>
<body>
<script language="javascript">
function checkteamvalid() {
	var 	form = document.matchform;
	var	players = new Array(3);
	for (var n = 0;  n < 3;  n++)  {
		var el = form["tm" + n];
		if (el.selectedIndex <= 0)  {
			alert("No player " + (n+1) + " selected");
			return false;
		}
		var opt = el.options;
		players[n] = opt[el.selectedIndex].value;
	}
	for (var p1 = 0;  p1 < 2; p1++)  {
		for (var p2 = p1 + 1; p2 < 3; p2++) {
			if (players[p1] == players[p2])  {
				alert("Players " + (p1+1) + " and " + (p2+1) + " are the same");
				return false;
			}
		}
	}
	return true;		
}
</script>
<?php

// Load members of team and at the same time check we've got enough

function checkteam($team) {
	$result = $team->list_members();
	if (count($result) < 3)  {
		print <<<EOT
<h1>Edit Match</h1>
<p>
Sorry but there are not enough members in {$team->display_name()} yet to
make up a match with.
</p>
<p>Please <a href="javascript:history.back()">click here</a> to go back
or <a href="teamsupd.php" target="_top">here</a> to update teams.</p>
</body>
</html>
EOT;
		exit(0);
	}
	foreach ($result as $p) {
		$p->fetchdets();
	}
	return $result;
}

if ($hora == 'H')  {
	$Myteam = $mtch->Hteam;
	$Histeam = $mtch->Ateam;
}
else  {
	$Myteam = $mtch->Ateam;
	$Histeam = $mtch->Hteam;
}
$Tmemb = checkteam($Myteam);  
?>
<h1>Allocate team members to match</h1>
<?php
print <<<EOT
<p>This page is for allocation of members of the
{$Myteam->display_name()} ({$Myteam->display_description()})
for the division {$mtch->Division} match in
{$mtch->Date->display_month()} against
{$Histeam->display_name()} ({$Histeam->display_description()}).
</p>
<p>Please select or reselect team members using the form below.
<b>Please note</b> that once both teams are allocated you cannot change the team.
</p>
<p>
The players will be sorted into descending order of rank, so if you need to
adjust the team members' ranks
<a href="updrank.php?{$Myteam->urlof()}">go here first</a>.
</p>
EOT;
$ce = $Histeam->display_capt_email();
if (strlen($ce) != 0)
	print <<<EOT
<p>If you want to email the captain of {$Histeam->display_name()} first click here $ce.
</p>
EOT;
print <<<EOT
<form name="matchform" action="tcupdmatch2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkteamvalid()">
{$mtch->save_hidden()}
{$Myteam->save_hidden()}
<table class="updmatch">
<tr><th>Player assignments</th></tr>
EOT;

$ng = $mtch->ngames();

for ($row = 0; $row < 3; $row++)  {
	print "<tr><td>\n";
	$matchm = false;
	if ($row < $ng)  {
		$g = $mtch->Games[$row];
		if ($g->Wteam && $g->Wteam->is_same($Myteam))
			$matchm = $g->Wplayer;
		elseif ($g->Bteam && $g->Bteam->is_same($Myteam))
			$matchm = $g->Bplayer;
	}
	print <<<EOT
<select name="tm$row">
<option value="-">-</option>
EOT;
	foreach ($Tmemb as $memb) {
		$val = $memb->selof();
		if ($matchm && $matchm->is_same($memb))
			print <<<EOT
<option value="$val" selected>
EOT;
		else
			print <<<EOT
<option value="$val">
EOT;
		print <<<EOT
{$memb->display_name()} ({$memb->display_rank()})
</option>
EOT;
	}
	print <<<EOT
</select>
</td></tr>
EOT;
}
?>
</table>
<p>
Select the team members and
<input type="submit" value="Click here"> or <input type="reset" value="Reset form">
</p>
<p>
Colours will be assigned randomly. Note again that teams will be sorted into descending order of rank.
Click on the link above if you need to adjust these.
</p>
</form>
</body>
</html>
