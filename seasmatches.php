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

include 'php/session.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/season.php';
include 'php/teambase.php';
include 'php/matchbase.php';
include 'php/histteam.php';
include 'php/histmatch.php';
include 'php/matchdate.php';
include 'php/game.php';

try {
	$seas = new Season();
	$seas->fromget();
	$seas->fetchdets();
}
catch (SeasonException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Historic Matches";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Matches</h1>
<?php
print <<<EOT
<p>
This is the final matches list for
<b>{$seas->display_name()}</b>, the start date for which was
{$seas->display_start()} and the end was {$seas->display_end()}.
</p>

EOT;
?>

<table class="matchesd">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
<th>Score</th>
</tr>
<?php
$ret = mysql_query("select ind from histmatch where {$seas->queryof()} order by divnum,matchdate,hteam,ateam");
if ($ret && mysql_num_rows($ret) > 0)  {
	$lastdiv = -99;
	while ($row = mysql_fetch_array($ret))  {
		$ind = $row[0];
		$mtch = new HistMatch($seas, $ind);
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
		if ($mtch->Result == 'H')
			$ht = "<b>$ht</b>";
		else if ($mtch->Result == 'A')
			$at = "<b>$at</b>";
		$ref = "<a href=\"histshowmtch.php?{$mtch->urlof()}\" class=\"nound\">";
		print <<<EOT
<td>$ref$ht</a></td>
<td>$ref$at</a></td>
<td>{$mtch->summ_score()}</td>
</tr>

EOT;
	}
}
else {
	print "<tr><td colspan=\"4\" align=\"center\">No matches to display</td></tr>\n";
}
?>
</table>
<h2>Other Seasons</h2>
<p>Please <a href="javascript:history.back()">click here</a> to go back.</p>
</div>
</div>
</body>
</html>
