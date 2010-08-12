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

include 'php/session.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Matches";
include 'php/head.php';
?>
<body>
<h1>Matches</h1>
<table class="matchesd">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
<th>Status</th>
</tr>
<?php
$ret = mysql_query("select ind from lgmatch order by divnum,matchdate,hteam,ateam");
if ($ret && mysql_num_rows($ret) > 0)  {
	$lastdiv = -99;
	while ($row = mysql_fetch_array($ret))  {
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
			print "<tr><th colspan=\"3\" align=\"center\">Division $lastdiv</th></tr>\n";
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
			$c = $mtch->is_captain($username);
			if ($c == 'H')  {
				$href = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=H\" class=\"noundm\">";
				$hndref = "</a>";
			}
			elseif ($c == 'A') {
				$aref = "<a href=\"tcupdmatch.php?{$mtch->urlof()}&hora=A\" class=\"noundm\">";
				$andref = "</a>";
			}
			print "<td>$href$ht$hndref</td><td>$aref$at$andref</td>\n";
		}
		if ($mtch->Result == 'H' || $mtch->Result == 'A' || $mtch->Result == 'D')  {
				$h = $mtch->Hscore + 0;
				$a = $mtch->Ascore + 0;			
			print "<td>Played ($h-$a)</td>";
		}
		elseif ($mtch->is_allocated())  {
			if ($mtch->Result == 'P') {
				$h = $mtch->Hscore + 0;
				$a = $mtch->Ascore + 0;
				print "<td>Part played ($h-$a)</td>";
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
?>
</table>
<h2>Previous Seasons</h2>
<p><a href="prevleagueb.php">Click here</a> to view the league
table and/or matches from previous seasons.</p>
</body>
</html>
