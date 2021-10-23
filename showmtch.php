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
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
include 'php/params.php';
include 'php/hcp_message.php';
include 'php/jumpto.php';

$Connection = opendatabase();

$pars = new Params();
$pars->fetchvalues();

$mtch = new Match();
try  {
	$mtch->fromget();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
}
catch (MatchException $e) {
	if ($e->Nfound)
		jumpto("histshowmtch.php?hmi={$e->Mid}");
   wrongentry($e->getMessage());
}
$editok = $admin || $mtch->is_captain($username) != 'N';
$Title = "Match Details";
lg_html_header($Title);
lg_html_nav();
print <<<EOT
<h1>Match Details</h1>
<p>Match is between {$mtch->Hteam->display_name()} ({$mtch->Hteam->display_description()})
and {$mtch->Ateam->display_name()} ({$mtch->Ateam->display_description()}) in {$mtch->Date->display_month()}.</p>
<p>Team captains are {$mtch->Hteam->display_captain(true)} for {$mtch->Hteam->display_name()}
and {$mtch->Ateam->display_captain(true)} for {$mtch->Ateam->display_name()}.</p>

EOT;
if ($mtch->Defaulted) {
	$wdef = $mtch->Result == 'A'? $mtch->Ateam: $mtch->Hteam;
	print <<<EOT
<p>This match was defaulted in favour of {$wdef->display_name()}.</p>

EOT;
}
else {
   if ($mtch->Result=='H' || $mtch->Result=='A' || $mtch->Result=='D') {
		print <<<EOT
<p>The final score was {$mtch->summ_score()}.</p>
<p>Player and board assignments were:</p>

EOT;
	}
	else {
		if ($mtch->Result=='P') {
			print "<p>Score to date is {$mtch->summ_score()}</p>\n";
		}
		print <<<EOT
<p>Player and board assignments are as follows:</p>

EOT;
	}
	print <<<EOT
<table class="showmatch">
<tr><th colspan="5" align="center">White</th><th colspan="4" align="center">Black</th><th>Result</th></tr>
<tr><th>Date</th><th>Player</th><th>Rank</th><th>Online</th><th>Team</th><th>Player</th><th>Rank</th><th>Online</th><th>Team</th></tr>

EOT;
	$hcaps = array();
	$boards = array();
	$board = 1;
	foreach ($mtch->Games as $g) {
		$bpre = $bpost = $wpre = $wpost = "";
		switch ($g->Result)  {
		case 'W':
			$wpre = "<b>";
			$wpost = "</b>";
			break;
		case 'B':
			$bpre = "<b>";
			$bpost = "</b>";
			break;
		}
		$hmsg = hcp_message($g, $pars);
		if ($hmsg)  {
			array_push($hcaps, $hmsg);
			array_push($boards, $board);
		}
		print <<<EOT
<tr>
<td>{$g->date_played()}</td>
<td>$wpre{$g->Wplayer->display_name()}$wpost</td>
<td>{$g->Wplayer->display_rank()}</td>
<td>{$g->Wplayer->display_online()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>$bpre{$g->Bplayer->display_name()}$bpost</td>
<td>{$g->Bplayer->display_rank()}</td>
<td>{$g->Bplayer->display_online()}</td>
<td>{$g->Bteam->display_name()}</td>
<td>{$g->display_result($editok || $g->playerin($username))}</td>
</tr>

EOT;
		$board++;
	}
	print <<<EOT
</table>

EOT;
	if (count($boards) > 0)  {
		$n = count($boards);
		print <<<EOT
<h2>Handicaps</h2>
<p>Handicaps apply to

EOT;
		if ($n == 1)
			print "1 board\n";
		else
			print "$n boards\n";
		print <<<EOT
in this match as follows:</p>
<table class="showmatch">
<tr><th>Board</th><th>Handicap</th></tr>

EOT;
		for ($board = 0; $board < $n;  $board++)  {
			$b = $boards[$board];
			$h = $hcaps[$board];
			print "<tr><td>$b</td><td>$h</td></tr>\n";
		}
		print "</table>\n";
	}
}
print <<<EOT
<p>Click <a href="javascript:history.back()">here</a> to return to your previous page
or <a href="matches.php">here</a> to look at other matches.</p>

EOT;
lg_html_footer();
?>
