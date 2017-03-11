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
include 'php/match.php';
include 'php/matchdate.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Results for completed matches";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
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
<?php
$ret = mysql_query("select ind from lgmatch where result='H' or result='A' order by divnum,matchdate,hteam,ateam");
if ($ret && mysql_num_rows($ret) > 0)  {
	$lastdiv = -99;
	while ($row = mysql_fetch_array($ret))  {
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
?>
</table>
<h2>Previous Seasons</h2>
<p><a href="league.php">Click here</a> to view the league
table and/or league tables and matches from previous seasons.</p>
</div>
</div>
</body>
</html>
