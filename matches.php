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
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
include 'php/season.php';

$Connection = opendatabase();
lg_html_header("Matches");
lg_html_nav();
print <<<EOT
<h1>Matches</h1>
<a name="topm"></a>
<table class="plinits"><tr>

EOT;
$maxdiv = max_division();
for ($n = 1;  $n <= $maxdiv;  $n++) {
	print <<<EOT
<td><a href="#div$n">Div $n</a></td>

EOT;
}
print <<<EOT
</tr></table>
<table class="matchesd">

EOT;
$ret = $Connection->query("SELECT ind FROM lgmatch ORDER BY divnum,matchdate,hteam,ateam");
if ($ret && $ret->num_rows > 0)  {
	$lastdiv = -99;
	while ($row = $ret->fetch_array())  {
		$ind = $row[0];
		$mtch = new Match($ind);
		$mtch->fetchdets();
		try {
			$mtch->fetchteams();
			$mtch->fetchgames();
		}
		catch (MatchException $e) {
			continue;
		}
		if ($mtch->Division != $lastdiv)  {
			$lastdiv = $mtch->Division;
			print <<<EOT
<tr><th colspan="4" align="center" valign="middle"><a name="div$lastdiv"></a><a href="#topm">Division $lastdiv</a></th></tr>
<tr><th>Date</th><th>Team A</th><th>Team B</th><th>Status</th></tr>

EOT;
		}
		print <<<EOT
<tr>
<td>{$mtch->Date->display_month()}</td>

EOT;
		$ht = $mtch->Hteam->display_name();
		$at = $mtch->Ateam->display_name();
		if  ($mtch->is_allocated())  {
			if ($mtch->Result == 'H')
				$ht = "<b>$ht</b>";
			else if ($mtch->Result == 'A')
				$at = "<b>$at</b>";
			$ref = "<a href=\"showmtch.php?{$mtch->urlof()}\" class=\"noundd\">";
			print "<td>$ref$ht</a></td><td>$ref$at</a></td>\n";
		}
		else  {
			$href = $aref = $hndref = $andref = '';
			if ($admin)  {
				$href = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=H\" class=\"noundm\">";
				$aref = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=A\" class=\"noundm\">";
				$hndref = $andref = "</a>";
			}
			else  {
				$c = $mtch->is_captain($username);
				if ($c == 'H' || $c == 'B')  {
					$href = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=H\" class=\"noundm\">";
					$hndref = "</a>";
				}
				if ($c == 'A' || $c == 'B') {
					$aref = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=A\" class=\"noundm\">";
					$andref = "</a>";
				}
			}
			print "<td>$href$ht$hndref</td><td>$aref$at$andref</td>\n";
		}
		if ($mtch->Result == 'H' || $mtch->Result == 'A' || $mtch->Result == 'D')  {
			print "<td>Played ({$mtch->summ_score()})</td>";
		}
		elseif ($mtch->is_allocated())  {
			if ($mtch->Result == 'P') {
				print "<td>Part played ({$mtch->summ_score()})</td>";
			}
			else
				print "<td>Not played</td>";
		}
		else
			print "<td>TBA</td>";
		print "</tr>\n";
	}
}
else {
	print "<tr><td colspan=\"3\" align=\"center\">No matches yet for the current season</td></tr>\n";
}
print <<<EOT
</table>
<h2>Previous Seasons</h2>
<a name="prev"></a>

EOT;

$seasons = list_seasons();
if (count($seasons) == 0) {
	print <<<EOT
<p>There are currently no past seasons to display.
Please come back soon!</p>
<p>Please <a href="javascript:history.back()">click here</a> to go back.</p>

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
	<th>Matches</th>
</tr>

EOT;
	foreach ($seasons as $seas) {
		$seas->fetchdets();
		print <<<EOT
<tr>
	<td>{$seas->display_name()}</td>
	<td>{$seas->display_start()}</td>
	<td>{$seas->display_end()}</td>
	<td><a href="seasleague.php?{$seas->urlof()}">Click</a></td>
	<td><a href="seasmatches.php?{$seas->urlof()}">Click</a></td>
</tr>

EOT;
	}
	print "</table>\n";
}
lg_html_footer();
?>
