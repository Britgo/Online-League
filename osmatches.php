<?php
//   Copyright 2011-7 John Collins

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
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
include 'php/params.php';
include 'php/hcp_message.php';

$asuser = $userid;
if ($admin  &&  strlen($_GET["asuser"]) != 0)
	$asuser = $_GET["asuser"];

// Get parameters to include handicap info

$pars = new Params();
$pars->fetchvalues();

$player = new Player();
try {
	$player->fromid($asuser);
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
   include 'php/wrongentry.php';
   exit(0);
}

// Get the teams this player is captain of
	
try {	
	$captain_of = list_teams_captof($player);
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$unalloc_matches = array();
$oppunalloc_matches = array();
$unplayed_matches = array();

// Do each team in turn it's easier to code

foreach ($captain_of as $team) {
	$ret = mysql_query("select ind from lgmatch where (result='N' or result='P') and ({$team->queryof('hteam')} or {$team->queryof('ateam')}) order by matchdate");
	if ($ret && mysql_num_rows($ret) > 0)  {
		while ($row = mysql_fetch_array($ret))  {
			try {
				$mtch = new Match($row[0]);
				$mtch->fetchdets();
				$mtch->fetchteams();
				$mtch->fetchgames();
				if ($mtch->is_allocated())  {
					array_push($unplayed_matches, $mtch);
				}
				elseif ($mtch->team_allocated($team)) {
					array_push($oppunalloc_matches, $mtch);
				}
				else {
					array_push($unalloc_matches, $mtch);
				}
			}
			catch (MatchException $e) {
				continue;
			}
		}
	}	 
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Outstanding Matches";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
include 'php/nav.php';
if (count($captain_of) > 0 && (count($unplayed_matches) > 0 || count($unalloc_matches) > 0 || count($oppunalloc_matches) > 0)) {
	$tnames = array();
	foreach ($captain_of as $team) {
		array_push($tnames, $team->display_name());
	}
	$tnames = join(', ', $tnames);
	print <<<EOT
<h1>Team captain tasks</h1>
<p>In your capacity as team captain of $tnames please complete the following:</p>

EOT;
	if (count($unalloc_matches) > 0)  {
		print <<<EOT
<h2>Matches to have teams allocated</h2>
<p>Please can you allocate teams to the following matches</p>
<table class="matchesosd">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
<th>Status</th>
</tr>

EOT;
		foreach ($unalloc_matches as $mtch)  {
			$c = $mtch->is_captain($username);
			$href = $aref = $hndref = $andref = '';
			$ht = $mtch->Hteam->display_name();
			$at = $mtch->Ateam->display_name();
			if ($c == 'H' ||  $c == 'B')  {
				$href = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=H\" class=\"noundm\">";
				$hndref = "</a>";
			}
			if ($c == 'A' ||  $c == 'B')  {
				$aref = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=A\" class=\"noundm\">";
				$andref = "</a>";
			}
			print <<<EOT
<tr>
<td>{$mtch->Date->display_month()}</td>
<td>$href$ht$hndref</td>
<td>$aref$at$andref</td>
<td>TBA</td>
</tr>

EOT;
		}
		print "</table>\n";
	}
	if (count($oppunalloc_matches) > 0)  {
		print <<<EOT
<h2>Matches to have teams allocated by opponents</h2>
<p>I know that you have allocated teams to the following matches, but please
can you chase your opponents to do so in the following matches</p>
<table class="matchesosd">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
<th>Status</th>
</tr>

EOT;
		foreach ($oppunalloc_matches as $mtch)  {
			$c = $mtch->is_captain($username);
			$href = $aref = $hndref = $andref = '';
			$ht = $mtch->Hteam->display_name();
			$at = $mtch->Ateam->display_name();
			if ($c == 'H')  {
				$href = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=H\" class=\"noundm\">";
				$hndref = "</a>";
			}
			else {
				$aref = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=A\" class=\"noundm\">";
				$andref = "</a>";
			}
			print <<<EOT
<tr>
<td>{$mtch->Date->display_month()}</td>
<td>$href$ht$hndref</td>
<td>$aref$at$andref</td>
<td>TBA</td>
</tr>

EOT;
		}
		print "</table>\n";
	}
	if (count($unplayed_matches) > 0)  {
		print <<<EOT
<h2>Unfinished matches</h2>
<p>Please can you organise for the following matches to be completed</p>
<table class="matchesosd">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
<th>Status</th>
</tr>

EOT;
		foreach ($unplayed_matches as $mtch)  {
			$href = $aref = $hndref = $andref = '';
			$ht = $mtch->Hteam->display_name();
			$at = $mtch->Ateam->display_name();
			if ($mtch->Result == 'H')
				$ht = "<b>$ht</b>";
			else if ($mtch->Result == 'A')
				$at = "<b>$at</b>";
			$ref = "<a href=\"showmtch.php?{$mtch->urlof()}\" class=\"noundd\">";
			if ($mtch->Result == 'P') {
				$res = "Part played ({$mtch->summ_score()})";
			}
			else
				$res = "Not played";
			print <<<EOT
<tr>
<td>{$mtch->Date->display_month()}</td>
<td>$ref$ht</a></td>
<td>$ref$at</a></td>
<td>$res</td>
</tr>

EOT;
		}
		print "</table>\n";
	}
}

// Now for user's games

print <<<EOT
<h1>Outstanding games for {$player->display_name(false)}</h1>

EOT;
$osgames = array();
$ret = mysql_query("select ind from game where result='N' and (({$player->queryof('w')}) or ({$player->queryof('b')})) order by matchdate");
if ($ret && mysql_num_rows($ret) > 0)  {
	while ($row = mysql_fetch_array($ret))  {
		try {
			$g = new Game($row[0]);
			$g->fetchdets();
			if (!$g->Wteam || !$g->Bteam)
				continue;
			array_push($osgames, $g);
		}
		catch (GameException $e) {
			continue;
		}
	}
}
if (count($osgames) == 0)
	print <<<EOT
<p>You currently do not have any outstanding games to play.</p>

EOT;
else  {
	print <<<EOT
<table class="showmatch">
<tr>
<th colspan="4" align="center">White</th>
<th colspan="4" align="center">Black</th></tr>
<tr>
<th>Player</th>
<th>Rank</th>
<th>Online</th>
<th>Team</th>
<th>Player</th>
<th>Rank</th>
<th>Online</th>
<th>Team</th>
<th>Hcp</th>
</tr>

EOT;
	foreach ($osgames as $g) {
		$hcp = hcp_message($g, $pars);
		if (!$hcp)
			$hcp = "None";
		print <<<EOT
<tr>
<td>{$g->Wplayer->display_name()}</td>
<td>{$g->Wplayer->display_rank()}</td>
<td>{$g->Wplayer->display_online()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>{$g->Bplayer->display_name()}</td>
<td>{$g->Bplayer->display_rank()}</td>
<td>{$g->Bplayer->display_online()}</td>
<td>{$g->Bteam->display_name()}</td>
<td>$hcp</td>
<td>{$g->display_result(true)}</td>
</tr>

EOT;
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
