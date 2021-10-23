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

$Connection = opendatabase();
lg_html_header("Results for completed matches");
lg_html_nav();
print <<<EOT
<h1>Results for completed matches</h1>
<p>The following results are available. Bold indicates the winning team. Click on any
entry to see the individual scores and in some cases game scores.</p>
<table class="resultsb">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
<th>Score</th>
</tr>

EOT;
$ret = $Connection->query("SELECT ind FROM lgmatch WHERE result='H' OR result='A' ORDER BY divnum,matchdate,hteam,ateam");
if ($ret && $ret->num_rows > 0)  {
	$lastdiv = -99;
	while ($row = $ret->fetch_array())  {
		$ind = $row[0];
		$mtch = new Match($ind);
		$mtch->fetchdets();
		if ($mtch->Division != $lastdiv)  {
			$lastdiv = $mtch->Division;
			print "<tr><th colspan=\"4\" align=\"center\">Division $lastdiv</th></tr>\n";
		}
		print <<<EOT
<tr>
<td>{$mtch->Date->display_month()}</td>
EOT;
		$ht = $mtch->Hteam->display_name();
		$at = $mtch->Ateam->display_name();
		if  ($mtch->teamalloc())  {
			if ($mtch->Result == 'H')
				$ht = "<b>$ht</b>";
			else if ($mtch->Result == 'A')
				$at = "<b>$at</b>";
			$ref = "<a href=\"showmtch.php?{$mtch->urlof()}\" class=\"nound\">";
			print "<td>$ref$ht</a></td><td>$ref$at</a></td>\n";
		}
		else  {
			print "<td>$ht</td><td>$at</td>\n";
		}
		print "<td>{$mtch->summ_score()}</td></tr>\n";
	}
}
else {
	print "<tr><td colspan=\"4\" align=\"center\">No matches yet</td></tr>\n";
}
print <<<EOT
</table>
<h2>Previous Seasons</h2>
<p><a href="league.php">Click here</a> to view the league
table and/or league tables and matches from previous seasons.</p>

EOT;
lg_html_footer();
?>
