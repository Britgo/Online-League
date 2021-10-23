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
include 'php/matchdate.php';

$Connection = opendatabase(true);

lg_html_header("Individual League Archive");
print <<<EOT
<body>
<script language="javascript">
function checkok() {
	return confirm("Are you sure you want to this - it is pretty well irreversible");
}
</script>

EOT;
lg_html_nav();
print <<<EOT
<h1>Individual League Archive</h1>
<p>Note that currently promotions and relegations have to be done manually pending
finalisation of the structure of the league.</p>

EOT;

$earliest = new Matchdate();
$latest = new Matchdate();
$seasnum = 1;
$ret = $Connection->query("SELECT matchdate FROM game WHERE league='I' ORDER BY matchdate limit 1");
if ($ret && $ret->num_rows > 0)  {
	$row = $ret->fetch_array();
	if ($row)
			$earliest->enctime($row[0]);
}
$ret = $Connection->query("SELECT matchdate FROM game ORDER BY matchdate desc limit 1");
if ($ret && $ret->num_rows > 0)  {
	$row = $ret->fetch_array();
	if ($row)
		$latest->enctime($row[0]);
}
$ret = $Connection->query("SELECT COUNT(*) FROM season WHERE league='I'");
if ($ret && $ret->num_rows > 0) {
	$row = $ret->fetch_array();
	if ($row)
		$seasnum = $row[0]+1;
}
$name = "IL Season $seasnum {$earliest->display_month()} to {$latest->display_month()}";
print <<<EOT
<form action="ilarchive2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkok()">
<p>Name for IL season: <input type="text" name="seasname" value="$name" size="60"></p>
<p>Please do this with care!</p>
<p>Please <input type="submit" name="submit" value="Click Here"> when ready.</p>
</form>

EOT;
lg_html_footer();
?>
