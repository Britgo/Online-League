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
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/matchdate.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Individual League Archive";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>

<script language="javascript">
function checkok() {
	return confirm("Are you sure you want to this - it is pretty well irreversible");
}
</script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Individual League Archive</h1>
<p>Note that currently promotions and relegations have to be done manually pending
finalisation of the structure of the league.</p>
<?php
$earliest = new Matchdate();
$latest = new Matchdate();
$seasnum = 1;
$ret = mysql_query("select matchdate from game where league='I' order by matchdate limit 1");
if ($ret && mysql_num_rows($ret) > 0)  {
	$row = mysql_fetch_array($ret);
	if ($row)
			$earliest->enctime($row[0]);	
}
$ret = mysql_query("select matchdate from game order by matchdate desc limit 1");
if ($ret && mysql_num_rows($ret) > 0)  {
	$row = mysql_fetch_array($ret);
	if ($row)
		$latest->enctime($row[0]);	
}
$ret = mysql_query("select count(*) from season where league='I'");
if ($ret && mysql_num_rows($ret) > 0) {
	$row = mysql_fetch_array($ret);
	if ($row)
		$seasnum = $row[0]+1;
}
$name = "IL Season $seasnum {$earliest->display_month()} to {$latest->display_month()}";
print <<<EOT
<form action="ilarchive2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkok()">
<p>Name for IL season: <input type="text" name="seasname" value="$name" size="60"></p>

EOT;
?>
<p>Please do this with care!</p>
<p>
Please <input type="submit" name="submit" value="Click Here"> when ready.
</p>
</form>
</div>
</div>
</body>
</html>
