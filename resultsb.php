<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
?>
<html>
<?php
$Title = "Results for completed matches";
include 'php/head.php';
?>
<body>
<h1>Results for completed matches</h1>
<p>The following results are available. Bold indicates the winning team. Click on any
entry to see the individual scores and in some cases game scores.</p>
<table class="resultsb">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
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
			print "<tr><th colspan=\"3\" align=\"center\">Division $lastdiv</th></tr>\n";
		}
		print <<<EOT
<tr>
<td>{$mtch->Date->display()}</td>
EOT;
		$ht = $mtch->Hteam->display_name();
		$at = $mtch->Ateam->display_name();
		if  ($mtch->teamalloc())  {
			if ($mtch->Result == 'H')
				$ht = "<b>$ht</b>";
			else if ($mtch->Result == 'A')
				$at = "<b>$at</b>";
			$ref = "<a href=\"showmtch.php?{$mtch->urlof()}\">";
			print "<td>$ref$ht</a></td><td>$ref$at</a></td>\n";
		}
		else  {
			print "<td>$ht</td><td>$at</td>\n";
		}
		print "</tr>\n";
	}
}
else {
	print "<tr><td colspan=\"3\" align=\"center\">No matches yet</td></tr>\n";
}
?>
</table>
</body>
</html>
