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
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/params.php';

$pars = new Params();
$pars->fetchvalues();

// Check that we are ready to archive

include 'php/promoreleg.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Promotion and Relegation / Archive";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function checkok() {
	return confirm("Are you sure you want to do this - it is pretty well irreversible");
}
</script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Promotion and Relegation / Archive</h1>
<?php
if (count($messages) > 0)  {
	print <<<EOT
<p>
Sorry but we cannot proceed with the promotion / relegation and archive because of
the following:
</p>

EOT;
	foreach ($messages as $mess)
		print "<p>$mess</p>\n";
}
else  {
	$earliest = new Matchdate();
	$latest = new Matchdate();
	$seasnum = 1;
	$ret = mysql_query("select matchdate from lgmatch order by matchdate limit 1");
	if ($ret && mysql_num_rows($ret) > 0)  {
		$row = mysql_fetch_array($ret);
		if ($row)
			$earliest->enctime($row[0]);	
	}
	$ret = mysql_query("select matchdate from lgmatch order by matchdate desc limit 1");
	if ($ret && mysql_num_rows($ret) > 0)  {
		$row = mysql_fetch_array($ret);
		if ($row)
			$latest->enctime($row[0]);	
	}
	$ret = mysql_query("select count(*) from season where league='T'");
	if ($ret && mysql_num_rows($ret) > 0) {
		$row = mysql_fetch_array($ret);
		if ($row)
			$seasnum = $row[0]+1;
	}
	$name = "Season $seasnum {$earliest->display_month()} to {$latest->display_month()}";
	for ($d = 1; $d <= $ml; $d++) {
		$promo[$d]->fetchdets();
		$releg[$d]->fetchdets();
	}
	print "<h2>Champions</h2>\n";
	print "<p><b>{$promo[1]->display_name()} are the league champions!!!</b></p>\n";
	for ($d = 2; $d <= $ml; $d++) {
		print "<p>{$promo[$d]->display_name()} are champions of division $d</p>\n";
	}
	print <<<EOT
<h2>The Wooden Spoon</h2>
<p>We commiserate with {$releg[$ml]->display_name()} on coming bottom.</p>
<h2>End of Season / Promotions and relegations</h2>
<form action="archive2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkok()">
<p>Name for season: <input type="text" name="seasname" value="$name" size="60"></p>
<p>The following promotions and relegations are proposed. Please uncheck any to be
excluded.
</p>

EOT;
	for ($d = 1; $d < $ml; $d++)  {
		$nd = $d + 1;
		print <<<EOT
<p><input type="checkbox" name="pd$d" value="yes" checked>
Promote {$promo[$nd]->display_name()} from division $nd and relegate
{$releg[$d]->display_name()} from division $d.</p>
EOT;
	}
	print <<<EOT
<p>Remember that this will delete <u>all</u> current matches
after doing an archive and remove records of unplayed games so
do this with care!
</p>
<p>
Please <input type="submit" name="submit" value="Click Here"> when ready.
</p>
</form>
EOT;
}
?>
</div>
</div>
</body>
</html>
